<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FeeVoucherController;

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

Route::prefix('{school}')
//    ->middleware(['identify.school'])
    ->group(function () {

        Route::get('/students', [StudentController::class, 'index']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
        Route::post('/students/{id}/promote', [StudentController::class, 'promote']);
        Route::post('/students/bulk-promote', [StudentController::class, 'bulkPromote']);
        Route::post('/students', [StudentController::class, 'store']);


//Fee Structure
        Route::post('/fee-structures', [FeeStructureController::class, 'store']);
        Route::get('/fee-structures', [FeeStructureController::class, 'index']);

//        fee-vouchers
        Route::post('/fee-vouchers/generate', [FeeVoucherController::class, 'generate']);
        Route::get('/dashboard', function () {
            return 'Dashboard';
        });

    });
