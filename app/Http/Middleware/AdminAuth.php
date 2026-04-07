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

        $roles = Auth::user()->getRoleNames()->map(fn($r) => strtolower($r));

        if (!$roles->intersect(['admin', 'principal', 'manager', 'staff'])->isNotEmpty()) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
