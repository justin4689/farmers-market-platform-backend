<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only
    Route::middleware('role.admin')->prefix('admin')->group(function () {
        Route::post('/supervisors', [UserController::class, 'createSupervisor']);
    });

    // Supervisor only
    Route::middleware('role.supervisor')->prefix('supervisor')->group(function () {
        Route::post('/operators', [UserController::class, 'createOperator']);
    });
});
