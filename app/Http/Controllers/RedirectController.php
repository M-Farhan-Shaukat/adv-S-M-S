<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function home()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check roles
        if ($user->hasRole('Admin') || $user->hasRole('Manager') || $user->hasRole('Staff')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('User')) {
            return redirect()->route('user.dashboard');
        }

        // Default fallback
        return redirect()->route('user.dashboard');
    }
}
