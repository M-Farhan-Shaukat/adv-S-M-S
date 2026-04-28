<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function show()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();
        $user  = Auth::user();
        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));

        if (!$roles->intersect(['admin', 'principal', 'manager', 'staff'])->isNotEmpty()) {
            Auth::logout();
            return back()->withErrors(['email' => 'You do not have admin access.']);
        }

        // Check user is active
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is inactive. Please contact admin.']);
        }

        // Check school is active (skip for super admin)
        if (!$roles->contains('admin') && $user->school && !$user->school->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Your school is inactive. Please contact admin.']);
        }

        // Flush permission cache so sidebar shows correctly
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Principal goes to their school dashboard
        if ($roles->contains('principal') && !$roles->contains('admin') && $user->school) {
            return redirect()->route('school.dashboard', $user->school->slug);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
