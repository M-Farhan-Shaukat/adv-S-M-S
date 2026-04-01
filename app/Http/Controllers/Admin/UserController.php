<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Default per page
        $perPage = $request->input('per_page', 10);

        // Make sure it's a number
        $perPage = is_numeric($perPage) ? intval($perPage) : 10;

        $users = User::with('role')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]); // keep per_page in query string

        return view('admin.users.index', compact('users', 'perPage'));
    }


    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
//            'age'         => 'required|integer|min:1',
            'city'        => 'required|string|max:255',
//            'phone'       => 'required|string|max:20',
            'cnic'        => 'required|string|max:20',
//            'postal_code' => 'required|string|max:20',
            'password'    => 'required|min:6',
//            'role_id'     => 'required|exists:roles,id',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
//            'age'         => $request->age,
            'city'        => $request->city,
//            'phone'       => $request->phone,
            'cnic'        => $request->cnic,
//            'postal_code' => $request->postal_code,
            'password'    => Hash::make($request->password),
//            'role_id'     => $request->role_id,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
//            'age'         => 'required|integer|min:1',
            'city'        => 'required|string|max:255',
//            'phone'       => 'required|string|max:20',
            'cnic'        => 'required|string|max:20',
//            'postal_code' => 'required|string|max:20',
//            'role_id'     => 'required|exists:roles,id',
        ]);

        $user->update([
            'name'        => $request->name,
            'email'       => $request->email,
//            'age'         => $request->age,
            'city'        => $request->city,
//            'phone'       => $request->phone,
            'cnic'        => $request->cnic,
//            'postal_code' => $request->postal_code,
//            'role/_id'     => $request->role_id,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully');
    }


    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'User status updated');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Delete user - Controller Method
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            if (!$user) {
                return redirect()->route('admin.users')
                    ->with('error', 'User not found.');
            }
            if (auth()->id() === $user->id) {
                return redirect()->route('admin.users')
                    ->with('error', 'You cannot delete your own account.');
            }
            $userName = $user->name;
            $user->delete();
            return redirect()->route('admin.users')
                ->with('success', "User '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            \Log::error('User deletion failed: ' . $e->getMessage());
            return redirect()->route('admin.users')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Alternative: Delete with POST method
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(Request $request, $id): RedirectResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return redirect()->route('admin.users')
                    ->with('error', 'User not found.');
            }

            if (auth()->id() === $user->id) {
                return redirect()->route('admin.users')
                    ->with('error', 'You cannot delete your own account.');
            }

            if (!auth()->user()->hasPermission('delete_users')) {
                return redirect()->route('admin.users')
                    ->with('error', 'You do not have permission to delete users.');
            }

            $userName = $user->name;
            $user->delete();

            return redirect()->route('admin.users')
                ->with('success', "User '{$userName}' has been deleted successfully.");

        } catch (\Exception $e) {
            \Log::error('User deletion failed: ' . $e->getMessage());

            return redirect()->route('admin.users')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Bulk delete users
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        try {
            // Validate request
            $request->validate([
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer|exists:users,id'
            ]);

            $userIds = $request->user_ids;
            $deletedCount = 0;
            $failedNames = [];

            // Prevent admin from deleting themselves
            if (in_array(auth()->id(), $userIds)) {
                return redirect()->route('admin.users')
                    ->with('error', 'You cannot delete your own account.');
            }

            // Check permission
            if (!auth()->user()->hasPermission('delete_users')) {
                return redirect()->route('admin.users')
                    ->with('error', 'You do not have permission to delete users.');
            }

            // Delete users
            foreach ($userIds as $userId) {
                try {
                    $user = User::find($userId);
                    if ($user && auth()->id() !== $user->id) {
                        $user->delete();
                        $deletedCount++;
                    } else {
                        $failedNames[] = $user ? $user->name : "ID: {$userId}";
                    }
                } catch (\Exception $e) {
                    $failedNames[] = "ID: {$userId}";
                    \Log::warning("Failed to delete user ID: {$userId} - " . $e->getMessage());
                }
            }

            if ($deletedCount > 0) {
                return redirect()->route('admin.users')
                    ->with('success', "{$deletedCount} user(s) deleted successfully.");
            } else {
                return redirect()->route('admin.users')
                    ->with('error', 'No users were deleted.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.users')
                ->with('error', 'Please select at least one user to delete.');
        } catch (\Exception $e) {
            \Log::error('Bulk user deletion failed: ' . $e->getMessage());

            return redirect()->route('admin.users')
                ->with('error', 'Failed to delete users. Please try again.');
        }
    }
}
