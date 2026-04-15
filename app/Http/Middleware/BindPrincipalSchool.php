<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BindPrincipalSchool
{
    /**
     * For principal users, always bind their school into the container
     * so app('school') is available on every route (admin or school).
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !app()->bound('school')) {
            $user  = auth()->user();
            $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));

            if ($roles->contains('principal') && !$roles->contains('admin') && $user->school) {
                app()->instance('school', $user->school);
            }
        }

        return $next($request);
    }
}
