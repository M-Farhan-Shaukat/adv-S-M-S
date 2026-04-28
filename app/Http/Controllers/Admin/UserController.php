<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Principal sees their school's users — excluding students
        return User::with('roles')
            ->where('school_id', $authUser->school_id)
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'student'));
    }

    /** Roles principal is allowed to assign */
    private function allowedRoles(): \Illuminate\Support\Collection
    {
        $authUser = auth()->user();
        $roles    = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        if ($roles->contains('admin')) {
            return Role::orderBy('name')->get();
        }

        // Principal: system roles (no student, no principal) + their custom roles
        $systemRoles = Role::whereIn('name', ['teacher', 'staff', 'parent'])
            ->orderBy('name')
            ->get()
            ->map(fn($r) => (object)[
                'name'   => $r->name,
                'label'  => ucfirst($r->name),
                'custom' => false,
            ]);

        $customRoles = \App\Models\SchoolCustomRole::where('school_id', $authUser->school_id)
            ->orderBy('name')
            ->get()
            ->map(fn($r) => (object)[
                'name'   => $r->name,
                'label'  => $r->name . ' (custom)',
                'custom' => true,
                'id'     => $r->id,
            ]);

        return $systemRoles->concat($customRoles);
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

        $roles       = $this->allowedRoles();
        $layout      = $this->resolveLayout();
        $routePrefix = $this->routePrefix();
        $routeParams = $this->routeParams();

        return view('admin.users.index', compact('users', 'perPage', 'roles', 'layout', 'routePrefix', 'routeParams'));
    }

    /** Super admin: view users of a specific school */
    public function bySchool(Request $request, \App\Models\School $school)
    {
        $perPage = is_numeric($request->per_page) ? (int) $request->per_page : 15;

        $users = User::with('roles')
            ->where('school_id', $school->id)
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $request->role)))
            ->latest()
            ->paginate($perPage)
            ->appends($request->only(['per_page', 'search', 'role']));

        $roles  = \Spatie\Permission\Models\Role::orderBy('name')->get();
        $layout = $this->resolveLayout();

        return view('admin.users.by_school', compact('users', 'school', 'perPage', 'roles', 'layout'));
    }
    public function create()
    {
        $roles       = $this->allowedRoles();
        $layout      = $this->resolveLayout();
        $routePrefix = $this->routePrefix();
        $routeParams = $this->routeParams();
        $isPrincipal = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r))->contains('principal');
        return view('admin.users.create', compact('roles', 'layout', 'routePrefix', 'routeParams', 'isPrincipal'));
    }

    public function store(Request $request)
    {
        $authUser  = auth()->user();
        $authRoles = $authUser->getRoleNames()->map(fn($r) => strtolower($r));
        $isPrincipal = $authRoles->contains('principal') && !$authRoles->contains('admin');

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string|max:20',
            'password'    => 'required|min:6',
            'role'        => 'required|string',
            'custom_role' => 'nullable|string', // "custom:{id}"
        ]);

        $schoolId = $authRoles->contains('admin')
            ? ($request->school_id ?: null)
            : $authUser->school_id;

        // Determine if it's a custom role
        $isCustomRole   = str_starts_with($data['role'], 'custom:');
        $customRoleId   = $isCustomRole ? (int) str_replace('custom:', '', $data['role']) : null;
        $spatieRoleName = $isCustomRole ? 'staff' : $data['role']; // fallback spatie role for custom

        // Validate spatie role exists if not custom
        if (!$isCustomRole && !Role::where('name', $data['role'])->exists()) {
            return back()->withErrors(['role' => 'Invalid role selected.'])->withInput();
        }

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'] ?? null,
            'school_id'         => $schoolId,
            'password'          => Hash::make($data['password']),
            'is_active'         => true,
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        $user->syncRoles([$spatieRoleName]);

        // Assign custom role if selected
        if ($isCustomRole && $customRoleId) {
            $customRole = \App\Models\SchoolCustomRole::find($customRoleId);
            if ($customRole && $customRole->school_id === $schoolId) {
                $customRole->users()->syncWithoutDetaching([$user->id]);
            }
        }

        \App\Services\CredentialService::sendCredentials(
            email:      $user->email,
            name:       $user->name,
            password:   $data['password'],
            role:       $isCustomRole ? ($customRole->name ?? 'Staff') : ucfirst($data['role']),
            schoolName: $user->school?->name ?? config('app.name'),
            loginUrl:   $this->getLoginUrl($spatieRoleName),
            portalNote: $this->getPortalNote($spatieRoleName, $user),
        );

        return redirect()->route($this->routePrefix() . 'index', $this->routeParams())
            ->with('success', "User '{$user->name}' created. Credentials sent to {$user->email}");
    }

    public function edit(User $user)
    {
        $this->authorizeAccess($user);
        $roles       = $this->allowedRoles();
        $userRole    = $user->getRoleNames()->first() ?? '';
        $layout      = $this->resolveLayout();
        $routePrefix = $this->routePrefix();
        $routeParams = $this->routeParams();
        $isPrincipal = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r))->contains('principal');
        return view('admin.users.edit', compact('user', 'roles', 'userRole', 'layout', 'routePrefix', 'routeParams', 'isPrincipal'));
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
            'role'     => 'required|string',
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

        // Handle custom vs spatie role
        $isCustomRole = str_starts_with($data['role'], 'custom:');
        if ($isCustomRole) {
            $customRoleId = (int) str_replace('custom:', '', $data['role']);
            $user->syncRoles(['staff']); // base spatie role
            $customRole = \App\Models\SchoolCustomRole::find($customRoleId);
            if ($customRole) {
                // Remove from all other custom roles of this school, assign new one
                $schoolCustomRoleIds = \App\Models\SchoolCustomRole::where('school_id', $user->school_id)->pluck('id');
                \DB::table('school_custom_role_users')
                    ->where('user_id', $user->id)
                    ->whereIn('school_custom_role_id', $schoolCustomRoleIds)
                    ->delete();
                $customRole->users()->syncWithoutDetaching([$user->id]);
            }
        } else {
            if (!Role::where('name', $data['role'])->exists()) {
                return back()->withErrors(['role' => 'Invalid role.']);
            }
            $user->syncRoles([$data['role']]);
        }

        return redirect()->route($this->routePrefix() . 'index', $this->routeParams())->with('success', "User '{$user->name}' updated");
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
        $layout      = $this->resolveLayout();
        $routePrefix = $this->routePrefix();
        $routeParams = $this->routeParams();
        return view('admin.users.show', compact('user', 'layout', 'routePrefix', 'routeParams'));
    }

    public function destroy(User $user)
    {
        $this->authorizeAccess($user);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $user->name;
        $user->delete();
        return redirect()->route($this->routePrefix() . 'index', $this->routeParams())->with('success', "User '{$name}' deleted");
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

    /** Returns the correct layout and route prefix based on logged-in role */
    private function resolveLayout(): string
    {
        $authUser  = auth()->user();
        $authRoles = $authUser->getRoleNames()->map(fn($r) => strtolower($r));

        if ($authRoles->contains('principal') && !$authRoles->contains('admin')) {
            if ($authUser->school && !app()->bound('school')) {
                app()->instance('school', $authUser->school);
            }
            return 'school.layouts.app';
        }

        return 'admin.layouts.app';
    }

    /** Route prefix for user management links — differs for admin vs principal */
    private function routePrefix(): string
    {
        $roles = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r));
        return ($roles->contains('principal') && !$roles->contains('admin'))
            ? 'school.users.'
            : 'admin.users.';
    }

    /** Route params needed alongside the prefix (school slug for principal) */
    private function routeParams(array $extra = []): array
    {
        $roles = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r));
        if ($roles->contains('principal') && !$roles->contains('admin')) {
            $slug = auth()->user()->school?->slug ?? '';
            return array_merge([$slug], $extra);
        }
        return $extra;
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
