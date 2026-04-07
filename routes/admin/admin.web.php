<?php

use App\Http\Controllers\Admin\AdminLoginController;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/adminAuth.web.php';
Route::prefix('admin')->middleware(['auth', 'role:admin,principal,manager,staff'])->name('admin.')->group(function () {
require __DIR__ . '/dashboard.web.php';
require __DIR__ . '/AdminUsers.web.php';
});





