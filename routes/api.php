<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/{id}', [StudentController::class, 'show']);
Route::post('/students/{id}/promote', [StudentController::class, 'promote']);
Route::post('/students/bulk-promote', [StudentController::class, 'bulkPromote']);
Route::post('/students', [StudentController::class, 'store']);
