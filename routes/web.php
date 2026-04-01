<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;

// Public home redirect
Route::get('/', function () {
    return view('user.auth.login'); // Create a welcome page or redirect to login
});

// Include user routes
require __DIR__ . '/user/user.web.php';

// Include admin routes
require __DIR__ . '/admin/admin.web.php';

// Role-based redirect after login (for users logging in via /login)
Route::get('/home', [RedirectController::class, 'home'])
    ->middleware('auth')
    ->name('home');

// Email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
