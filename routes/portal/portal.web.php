<?php

use App\Http\Controllers\Portal\ParentPortalController;
use App\Http\Controllers\Portal\StudentPortalController;
use Illuminate\Support\Facades\Route;

// Parent Portal
Route::prefix('{school}/parent')
    ->middleware(['identify.school', 'auth', 'role:parent'])
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/results', [ParentPortalController::class, 'results'])->name('results');
        Route::get('/fees', [ParentPortalController::class, 'feeVouchers'])->name('fees');
        Route::get('/exam-schedule', [ParentPortalController::class, 'examSchedule'])->name('exam-schedule');
        Route::get('/complaints', [ParentPortalController::class, 'complaints'])->name('complaints');
        Route::post('/complaints', [ParentPortalController::class, 'submitComplaint'])->name('complaints.store');
        Route::get('/meetings', [ParentPortalController::class, 'meetings'])->name('meetings');
        Route::post('/remarks', [ParentPortalController::class, 'submitRemark'])->name('remarks.store');
    });

// Student Portal
Route::prefix('{school}/student')
    ->middleware(['identify.school', 'auth', 'role:student'])
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/results', [StudentPortalController::class, 'results'])->name('results');
        Route::post('/recheck', [StudentPortalController::class, 'requestRecheck'])->name('recheck');
        Route::get('/exam-schedule', [StudentPortalController::class, 'examSchedule'])->name('exam-schedule');
        Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/fees', [StudentPortalController::class, 'feeVouchers'])->name('fees');
    });
