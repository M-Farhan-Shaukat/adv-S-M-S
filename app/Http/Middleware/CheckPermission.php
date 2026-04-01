<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin has all permissions
        if ($user->hasRole('Admin') || $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Required permission: ' . $permission);
    }
}
