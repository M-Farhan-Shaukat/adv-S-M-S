<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Principal has all permissions — bypass gate checks entirely
        Gate::before(function ($user, $ability) {
            $roles = $user->getRoleNames()->map(fn($r) => strtolower($r));
            if ($roles->contains('principal')) {
                return true;
            }
        });
    }
}
