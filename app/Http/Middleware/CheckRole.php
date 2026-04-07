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

        // Get user roles in lowercase
        $userRoles = $user->getRoleNames()->map(fn($r) => strtolower($r));

        foreach ($roles as $role) {
            if ($userRoles->contains(strtolower($role))) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized. Required role: ' . implode(', ', $roles));
    }
}
