<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Statistics
    Route::get('/statistics', [AdminDashboardController::class, 'statistics'])
        ->name('statistics');



    // Role Management
    Route::get('/roles', function() {
        return view('admin.roles.index');
    })->name('admin.roles');

    // Settings
    Route::get('/settings', function() {
        return view('admin.settings');
    })->name('admin.settings');

