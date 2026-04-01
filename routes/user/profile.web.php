<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProfileController;

Route::get('/profile', [ProfileController::class, 'show'])
    ->name('profile');

Route::post('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');
