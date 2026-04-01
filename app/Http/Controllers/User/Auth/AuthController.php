<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('user.auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (is_null($user->email_verified_at)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Please verify your email first.',
                ]);
            }
            // Debug logging
            \Log::info('User login attempt', [
                'email' => $user->email,
                'role_id' => $user->role_id,
                'hasRole_User' => $user->hasRole('User'),
            ]);

            // Check if user has ANY role
            if (!$user->role) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Account not properly configured. Please contact administrator.',
                ]);
            }
            // Redirect based on role
            if ($user->hasRole('Admin') || $user->hasRole('Manager') || $user->hasRole('Staff')) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have permission to access the User panel.',
                ]);
            }

            if ($user->hasRole('User')) {
                return redirect()->route('user.dashboard')
                    ->with('success', 'Login Successfully!');
            }

            // Fallback
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    // Show registration form
    public function showRegister()
    {

        return view('user.auth.register');
    }

    // Handle registration
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'cnic'        => 'nullable|string|max:20',
            'password'    => 'required|min:8',
        ]);

        // Count users in selected city

        $token = Str::random(64);

        // Create user
        $user = User::create([
            'name'        =>  $validated['name'],
            'email'       => $validated['email'],
//            'cnic'        => $validated['cnic'],
            'password'    => Hash::make($validated['password']),
            'is_active'   => true,
            'email_verification_token' => $token,
        ]);

        // Assign "User" role by default
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $user->role_id = $userRole->id;
            $user->save();
        } else {
            \Log::error('User role not found during registration');
        }
        $this->sendVerificationEmail($user);
        // Auto login after registration
//        Auth::login($user);

        return redirect()->route('login')
            ->with('success', 'Account created successfully.please check your email for verification!');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Logout Successfully!');
    }
    protected function sendVerificationEmail($user)
    {
        $verificationUrl = route('verify.email', $user->email_verification_token);

        Mail::send('emails.verify-email', ['url' => $verificationUrl, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify Your Email Address');
        });
    }
    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Invalid or expired verification link.');
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return redirect()->route('login')
            ->with('success', 'Email verified successfully. You can now login.');
    }

}
