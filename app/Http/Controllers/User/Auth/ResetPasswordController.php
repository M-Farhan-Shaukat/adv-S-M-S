<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showForgotPassword()
    {
        return view('user.auth.forgot-password');
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $request->email
        ]);


        Mail::send('emails.reset-password', ['url' => $resetUrl], function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Reset Your Password');
        });

        return back()->with('success', 'Password reset link sent to your email.');
    }

    public function showResetPassword(Request $request,string $token)
    {
        return view('user.auth.reset-password', ['token' => $token, 'email' => $request->query('email')] );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->with('error', 'Invalid or expired token.');
        }

        // Optional: Expire token after 60 minutes
        if (now()->diffInMinutes($record->created_at) > 60) {
            return back()->with('error', 'Token expired.');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Password reset successful. You can now login.');
    }
}
