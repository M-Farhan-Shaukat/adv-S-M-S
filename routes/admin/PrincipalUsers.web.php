<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Principal manages only their school's users
Route::prefix('school-users')->name('school.users.')->group(function () {
    Route::get('/',                  [UserController::class, 'index'])->name('index');
    Route::get('/create',            [UserController::class, 'create'])->name('create');
    Route::post('/',                 [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit',       [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}',            [UserController::class, 'update'])->name('update');
    Route::get('/{user}',            [UserController::class, 'show'])->name('show');
    Route::patch('/{user}/status',   [UserController::class, 'toggleStatus'])->name('status');
    Route::delete('/{user}',         [UserController::class, 'destroy'])->name('destroy');
});
