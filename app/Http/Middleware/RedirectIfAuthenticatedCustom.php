<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedCustom
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $roles = Auth::user()->getRoleNames()->map(fn($r) => strtolower($r));

            if ($roles->intersect(['admin', 'principal', 'manager', 'staff'])->isNotEmpty()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('user.dashboard');
        }

        return $next($request);
    }
}
