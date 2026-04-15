<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user  = Auth::user();
        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is inactive. Please contact admin.']);
        }

        if ($user->school && !$user->school->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your school is inactive. Please contact admin.']);
        }

        return $next($request);
    }
}
