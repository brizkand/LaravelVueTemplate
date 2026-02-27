<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class,'register'])->name('register');
    Route::post('/login', [AuthController::class,'login'])->name('login');
    Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum')->name('logout');
    Route::get('/user', [AuthController::class,'user'])->middleware('auth:sanctum')->name('user');
});
// Route::post('/register', [AuthController::class,'register'])->name('register');
// Route::post('/login', [AuthController::class,'login'])->name('login');
// Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:sanctum')->name('logout');


// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/users', [UserController::class,'index'])->name('users.index');

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('user', [UserController::class,'show'])->name('user');
    });
});


Route::get('/test', function () {
    return response()->json([
        'message' => 'Hello from Laravel API'
    ]);
});


