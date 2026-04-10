<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /** Returns query scoped to current user's access level */
    private function userQuery()
    {
        $authUser = auth()->user();
        $roles    = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        // Super admin sees everyone
        if ($roles->contains('admin')) {
            return User::with('roles');
        }

        // Principal sees only their school's users
        return User::with('roles')->where('school_id', $authUser->school_id);
    }

    /** Roles principal is allowed to assign */
    private function allowedRoles(): \Illuminate\Database\Eloquent\Collection
    {
        $authUser = auth()->user();
        $roles    = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        if ($roles->contains('admin')) {
            return Role::orderBy('name')->get();
        }

        // Principal can only create school-level roles
        return Role::whereIn('name', ['principal', 'teacher', 'staff', 'student', 'parent'])
            ->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $perPage = is_numeric($request->per_page) ? (int) $request->per_page : 15;

        $users = $this->userQuery()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->role)))
            ->latest()
            ->paginate($perPage)
            ->appends($request->only(['per_page', 'search', 'role']));

        $roles = $this->allowedRoles();

        return view('admin.users.index', compact('users', 'perPage', 'roles'));
    }

    public function create()
    {
        $roles = $this->allowedRoles();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();
        $authRoles = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        // Principal can only assign to their own school
        $schoolId = $authRoles->contains('admin')
            ? ($request->school_id ?: null)
            : $authUser->school_id;

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'] ?? null,
            'school_id'         => $schoolId,
            'password'          => Hash::make($data['password']),
            'is_active'         => $request->boolean('is_active', true),
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        $user->syncRoles([$data['role']]);

        // Send welcome credentials email
        \App\Services\CredentialService::sendCredentials(
            email:      $user->email,
            name:       $user->name,
            password:   $data['password'],
            role:       ucfirst($data['role']),
            schoolName: $user->school?->name ?? config('app.name'),
            loginUrl:   $this->getLoginUrl($data['role']),
            portalNote: $this->getPortalNote($data['role'], $user),
        );

        return redirect()->route('admin.users')
            ->with('success', "User '{$user->name}' created. Credentials sent to {$user->email}");
    }

    public function edit(User $user)
    {
        // Principal can only edit users of their school
        $this->authorizeAccess($user);

        $roles   = $this->allowedRoles();
        $userRole = $user->getRoleNames()->first() ?? '';
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAccess($user);

        $authUser  = auth()->user();
        $authRoles = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
            'password' => 'nullable|min:6',
        ]);

        $updateData = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        // Only super admin can change school
        if ($authRoles->contains('admin')) {
            $updateData['school_id'] = $request->school_id ?: null;
        }

        if ($request->boolean('email_verified') && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }

        $user->update($updateData);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users')->with('success', "User '{$user->name}' updated");
    }

    public function toggleStatus(User $user)
    {
        $this->authorizeAccess($user);
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated');
    }

    public function show(User $user)
    {
        $this->authorizeAccess($user);
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $this->authorizeAccess($user);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $user->name;
        $user->delete();
        return redirect()->route('admin.users')->with('success', "User '{$name}' deleted");
    }

    /** Abort if principal tries to access user from another school */
    private function authorizeAccess(User $user): void
    {
        $authUser  = auth()->user();
        $authRoles = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        if (!$authRoles->contains('admin') && $user->school_id !== $authUser->school_id) {
            abort(403, 'You can only manage users of your own school.');
        }
    }

    private function getLoginUrl(string $role): string
    {
        return in_array(strtolower($role), ['admin', 'principal', 'manager', 'staff'])
            ? url('/admin/login')
            : url('/login');
    }

    private function getPortalNote(string $role, User $user): ?string
    {
        $school = $user->school;
        if (!$school) return null;

        return match (strtolower($role)) {
            'teacher' => "Teacher portal: " . url("/{$school->slug}/teacher-portal/dashboard"),
            'student' => "Student portal: " . url("/{$school->slug}/student/dashboard"),
            'parent'  => "Parent portal: "  . url("/{$school->slug}/parent/dashboard"),
            default   => null,
        };
    }
}
