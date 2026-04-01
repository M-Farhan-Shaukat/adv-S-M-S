<?php
use App\Http\Controllers\Admin\AdminLoginController;
use Illuminate\Support\Facades\Route;

// Admin login (separate from user login)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'show'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.store');
});

// Admin logout
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout')
    ->middleware('auth'); // Changed from admin.auth to auth
