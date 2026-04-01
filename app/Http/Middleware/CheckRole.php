<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // If user is admin, allow access to everything
        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Required role: ' . implode(', ', $roles));
    }
}
