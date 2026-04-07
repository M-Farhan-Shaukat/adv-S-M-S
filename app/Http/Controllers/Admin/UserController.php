<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = is_numeric($request->per_page) ? (int)$request->per_page : 15;

        $users = User::with('roles')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($perPage)
            ->appends($request->only(['per_page', 'search']));

        return view('admin.users.index', compact('users', 'perPage'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6',
            'role'     => 'required|exists:roles,name',
            'is_active'=> 'nullable|boolean',
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'] ?? null,
            'school_id'         => $request->school_id ?: null,
            'password'          => Hash::make($data['password']),
            'is_active'         => $request->boolean('is_active', true),
            'email_verified_at' => $request->boolean('email_verified') ? now() : null,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users')
            ->with('success', "User '{$user->name}' created with role '{$data['role']}'");
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $userRole = $user->getRoleNames()->first() ?? '';
        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|exists:roles,name',
            'password' => 'nullable|min:6',
            'is_active'=> 'nullable|boolean',
        ]);

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'school_id' => $request->school_id ?: null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->boolean('email_verified') && !$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users')
            ->with('success', "User '{$user->name}' updated");
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated');
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $name = $user->name;
        $user->delete();
        return redirect()->route('admin.users')->with('success', "User '{$name}' deleted");
    }
}
