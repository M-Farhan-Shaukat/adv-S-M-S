<?php

use App\Http\Controllers\Portal\ParentPortalController;
use App\Http\Controllers\Portal\StudentPortalController;
use App\Http\Controllers\Portal\TeacherPortalController;
use App\Http\Controllers\Portal\ChangePasswordController;
use Illuminate\Support\Facades\Route;

// =================== CHANGE PASSWORD (all portal roles) ===================
Route::prefix('{school}')
    ->middleware(['identify.school', 'auth'])
    ->name('portal.')
    ->group(function () {
        Route::get('/change-password',  [ChangePasswordController::class, 'show'])->name('change-password');
        Route::put('/change-password',  [ChangePasswordController::class, 'update'])->name('change-password.update');
    });

// =================== TEACHER PORTAL ===================
Route::prefix('{school}/teacher-portal')
    ->middleware(['identify.school', 'auth', 'role:teacher'])
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard',    [TeacherPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance',   [TeacherPortalController::class, 'attendance'])->name('attendance');
        Route::get('/payroll',      [TeacherPortalController::class, 'payroll'])->name('payroll');
        Route::get('/subjects',     [TeacherPortalController::class, 'subjects'])->name('subjects');
        Route::get('/exam-schedule',[TeacherPortalController::class, 'examSchedule'])->name('exam-schedule');
        Route::get('/remarks',      [TeacherPortalController::class, 'remarks'])->name('remarks');
        Route::get('/marks/{schedule}/enter',  [TeacherPortalController::class, 'enterMarks'])->name('marks.enter');
        Route::post('/marks/{schedule}/save',  [TeacherPortalController::class, 'saveMarks'])->name('marks.save');
        Route::post('/remarks/{remark}/respond', [TeacherPortalController::class, 'respondRemark'])->name('remarks.respond');
    });

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
