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

Route::prefix('forms')->group(function () {
    Route::get('/', [FormController::class, 'index']);
    Route::post('/', [FormController::class, 'store']);
    Route::get('{form}', [FormController::class, 'show']);
    Route::put('{form}', [FormController::class, 'update']);
    Route::delete('{form}', [FormController::class, 'destroy']);

    Route::post('{form}/fields', [FormFieldController::class, 'store']);
    Route::put('fields/{field}', [FormFieldController::class, 'update']);
    Route::delete('fields/{field}', [FormFieldController::class, 'destroy']);
    Route::post('{form}/reorder-fields', [FormFieldController::class, 'reorder']);

    Route::post('{form}/submit', [FormSubmissionController::class, 'store']);
    Route::get('{form}/responses', [FormSubmissionController::class, 'index']);
    Route::get('{form}/analytics', [FormAnalyticsController::class, 'show']);
    Route::get('{form}/export', [FormExportController::class, 'export']);
});


