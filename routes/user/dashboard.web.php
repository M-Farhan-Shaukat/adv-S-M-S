<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Profile
Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
