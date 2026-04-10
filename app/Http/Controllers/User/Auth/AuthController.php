<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('user.auth.login');
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

        // Block admin roles from user portal
        if ($roles->intersect(['admin', 'principal', 'manager'])->isNotEmpty()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Please use the admin login page.']);
        }

        // Redirect based on role
        if ($roles->contains('parent') && $user->school) {
            return redirect()->route('parent.dashboard', $user->school->slug);
        }

        if ($roles->contains('student') && $user->school) {
            return redirect()->route('student.dashboard', $user->school->slug);
        }

        if ($roles->contains('teacher') && $user->school) {
            return redirect()->route('teacher.dashboard', $user->school->slug);
        }

        if ($roles->contains('staff') && $user->school) {
            return redirect()->route('school.dashboard', $user->school->slug);
        }

        return redirect()->route('user.dashboard');
    }

    public function showRegister()
    {
        return view('user.auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'is_active'         => true,
            'email_verified_at' => now(), // auto verify for now
        ]);

        // Default role: student (can be changed by admin)
        $user->assignRole('student');

        return redirect()->route('login')
            ->with('success', 'Account created. Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        $user->update(['email_verified_at' => now(), 'email_verification_token' => null]);

        return redirect()->route('login')->with('success', 'Email verified. You can now login.');
    }
}
