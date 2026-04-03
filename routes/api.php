<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FeeStructureController;

Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/{id}', [StudentController::class, 'show']);
Route::post('/students/{id}/promote', [StudentController::class, 'promote']);
Route::post('/students/bulk-promote', [StudentController::class, 'bulkPromote']);
Route::post('/students', [StudentController::class, 'store']);

//Fee Structure
Route::post('/fee-structures', [FeeStructureController::class, 'store']);
Route::get('/fee-structures', [FeeStructureController::class, 'index']);
