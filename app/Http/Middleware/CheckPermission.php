<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): mixed
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // admin/principal has all permissions
        $userRoles = $user->getRoleNames()->map(fn($r) => strtolower($r));
        if ($userRoles->intersect(['admin', 'principal'])->isNotEmpty()) {
            return $next($request);
        }

        if ($user->hasPermissionTo($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Required permission: ' . $permission);
    }
}
