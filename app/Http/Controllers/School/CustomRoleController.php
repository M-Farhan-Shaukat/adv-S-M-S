<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SchoolCustomRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class CustomRoleController extends Controller
{
    private function school()
    {
        return app('school');
    }

    public function index()
    {
        $school = $this->school();
        $roles  = SchoolCustomRole::where('school_id', $school->id)
            ->withCount('users')
            ->with('permissions')
            ->latest()
            ->get();

        $allPermissions = Permission::orderBy('name')->pluck('name');

        return view('school.custom_roles.index', compact('roles', 'allPermissions', 'school'));
    }

    public function store(Request $request)
    {
        $school = $this->school();

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $slug = Str::slug($data['name']);

        $exists = SchoolCustomRole::where('school_id', $school->id)
            ->where('slug', $slug)->exists();

        if ($exists) {
            return back()->with('error', 'A role with this name already exists.');
        }

        $role = SchoolCustomRole::create([
            'school_id'   => $school->id,
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
        ]);

        $role->syncPermissionList($data['permissions'] ?? []);

        return back()->with('success', "Role '{$role->name}' created.");
    }

    public function edit(string $school, SchoolCustomRole $customRole)
    {
        $this->authorizeRole($customRole);
        $allPermissions = Permission::orderBy('name')->pluck('name');
        $school = $this->school();
        return view('school.custom_roles.edit', compact('customRole', 'allPermissions', 'school'));
    }

    public function update(Request $request, string $school, SchoolCustomRole $customRole)
    {
        $this->authorizeRole($customRole);

        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $customRole->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $customRole->syncPermissionList($data['permissions'] ?? []);

        return redirect()->route('school.custom-roles.index', $this->school()->slug)
            ->with('success', "Role '{$customRole->name}' updated.");
    }

    public function destroy(string $school, SchoolCustomRole $customRole)
    {
        $this->authorizeRole($customRole);
        $name = $customRole->name;
        $customRole->delete();
        return back()->with('success', "Role '{$name}' deleted.");
    }

    // Assign a custom role to a user
    public function assignUser(Request $request, string $school, SchoolCustomRole $customRole)
    {
        $this->authorizeRole($customRole);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($data['user_id']);

        // Ensure user belongs to this school
        if ($user->school_id !== $this->school()->id) {
            return back()->with('error', 'User does not belong to this school.');
        }

        $customRole->users()->syncWithoutDetaching([$user->id]);

        return back()->with('success', "{$user->name} assigned to '{$customRole->name}'.");
    }

    // Remove a user from a custom role
    public function removeUser(string $school, SchoolCustomRole $customRole, User $user)
    {
        $this->authorizeRole($customRole);
        $customRole->users()->detach($user->id);
        return back()->with('success', "{$user->name} removed from '{$customRole->name}'.");
    }

    private function authorizeRole(SchoolCustomRole $role): void
    {
        if ($role->school_id !== $this->school()->id) {
            abort(403);
        }
    }
}
