<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // -----------------------------------------------------------------
    // Categories — read: all roles | write: admin + supervisor
    // -----------------------------------------------------------------
    Route::get('/categories', [CategoryController::class, 'index']);

    Route::middleware('role.admin_or_supervisor')->group(function () {
        Route::post('/categories',          [CategoryController::class, 'store']);
        Route::put('/categories/{id}',      [CategoryController::class, 'update']);
        Route::delete('/categories/{id}',   [CategoryController::class, 'destroy']);
    });

    // -----------------------------------------------------------------
    // Products — read: all roles | write: admin + supervisor
    // -----------------------------------------------------------------
    Route::get('/products',       [ProductController::class, 'index']);
    Route::get('/products/{id}',  [ProductController::class, 'show']);

    Route::middleware('role.admin_or_supervisor')->group(function () {
        Route::post('/products',        [ProductController::class, 'store']);
        Route::put('/products/{id}',    [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    // -----------------------------------------------------------------
    // Farmers — all authenticated roles (operator and above)
    // -----------------------------------------------------------------
    Route::middleware('role.operator_or_above')->group(function () {
        Route::get('/farmers/search',  [FarmerController::class, 'search']);
        Route::get('/farmers/{id}',    [FarmerController::class, 'show']);
        Route::post('/farmers',        [FarmerController::class, 'store']);
    });

    // -----------------------------------------------------------------
    // User management
    // -----------------------------------------------------------------
    Route::middleware('role.admin')->prefix('admin')->group(function () {
        Route::post('/supervisors', [UserController::class, 'createSupervisor']);
    });

    Route::middleware('role.supervisor')->prefix('supervisor')->group(function () {
        Route::post('/operators', [UserController::class, 'createOperator']);
    });
});
