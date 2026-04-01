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
            if (Auth::user()->role_is === 1) {
                return redirect('/admin/dashboard');
            }

            return redirect('/dashboard');
        }

        return $next($request);
    }
}
