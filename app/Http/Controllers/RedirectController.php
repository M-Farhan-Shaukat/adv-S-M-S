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

        $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));

        if ($roles->intersect(['admin', 'principal', 'manager', 'staff'])->isNotEmpty()) {
            return redirect()->route('admin.dashboard');
        }

        if ($roles->contains('parent')) {
            $school = $user->school;
            if ($school) return redirect()->route('parent.dashboard', $school->slug);
        }

        if ($roles->contains('student')) {
            $school = $user->school;
            if ($school) return redirect()->route('student.dashboard', $school->slug);
        }

        if ($roles->contains('teacher')) {
            $school = $user->school;
            if ($school) return redirect()->route('school.dashboard', $school->slug);
        }

        return redirect()->route('user.dashboard');
    }
}
