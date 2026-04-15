<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user  = Auth::user();
        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));

        if (!$roles->intersect(['admin', 'principal', 'manager', 'staff'])->isNotEmpty()) {
            abort(403, 'Admin access required.');
        }

        // Super admin is never blocked
        if ($roles->contains('admin')) {
            return $next($request);
        }

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your account is inactive. Please contact admin.']);
        }

        if ($user->school && !$user->school->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your school is inactive. Please contact admin.']);
        }

        return $next($request);
    }
}
