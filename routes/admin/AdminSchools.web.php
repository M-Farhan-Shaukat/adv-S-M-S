<?php

use App\Http\Controllers\Admin\SchoolController;
use Illuminate\Support\Facades\Route;

Route::prefix('schools')->name('schools.')->group(function () {
    Route::get('/',              [SchoolController::class, 'index'])->name('index');
    Route::get('/create',        [SchoolController::class, 'create'])->name('create');
    Route::post('/',             [SchoolController::class, 'store'])->name('store');
    Route::get('/{school}/edit', [SchoolController::class, 'edit'])->name('edit');
    Route::put('/{school}',      [SchoolController::class, 'update'])->name('update');
    Route::patch('/{school}/toggle', [SchoolController::class, 'toggle'])->name('toggle');
    Route::delete('/{school}',   [SchoolController::class, 'destroy'])->name('destroy');
});
