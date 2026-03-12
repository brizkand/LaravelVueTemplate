<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class,'login'])->name('login');
    Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum')->name('logout');
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('user', [UserController::class,'show'])->name('user');
    });
});


