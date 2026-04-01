<?php

use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\AttachmentController;
use Illuminate\Support\Facades\Route;



// Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:User')
        ->name('dashboard');

// Admin documents
Route::get('/agreement/download', [DashboardController::class,'downloadAgreement'])->name('agreement.download');
Route::get('/challan/download', [DashboardController::class,'downloadChallan'])->name('challan.download');



// Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])
        ->middleware('role:User')
        ->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->middleware('role:User')
        ->name('profile.update');

    // Document Management


    // Application Status
    Route::get('/application/status', [DashboardController::class, 'status'])
        ->middleware('role:User')
        ->name('application.status');
    Route::get('/application/track', [DashboardController::class, 'track'])
        ->middleware('role:User')
        ->name('application.track');

    // Agreement & Payment
    Route::get('/agreement/view', [DocumentController::class, 'agreement'])
        ->middleware('role:User')
        ->name('agreement.view');
    Route::post('/agreement/sign', [DocumentController::class, 'signAgreement'])
        ->middleware('role:User')
        ->name('agreement.sign');
    Route::get('/payment/upload', [DocumentController::class, 'paymentForm'])
        ->middleware('role:User')
        ->name('payment.upload');
    Route::post('/payment/upload', [DocumentController::class, 'uploadPayment'])
        ->middleware('role:User')
        ->name('payment.store');

//    // Attachment Downloads
//    Route::get('/download/{file}', [AttachmentController::class, 'download'])
//        ->middleware('role:User')
//        ->name('download');
//    Route::get('/view/{file}', [AttachmentController::class, 'view'])
//        ->middleware('role:User')
//        ->name('view');


// Public attachment download (still requires auth)
Route::get('/user-attachments/{file}', [AttachmentController::class, 'downloadPublic'])
    ->name('user.attachments.download')
    ->middleware('auth');
