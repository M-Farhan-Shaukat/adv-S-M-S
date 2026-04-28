<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/adminAuth.web.php';

// ===== SUPER ADMIN ONLY =====
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/AdminUsers.web.php';
        require __DIR__ . '/AdminSchools.web.php';
    });

// ===== ADMIN + PRINCIPAL (shared dashboard) =====
Route::prefix('admin')
    ->middleware(['auth', 'role:admin,principal'])
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/dashboard.web.php';
    });
