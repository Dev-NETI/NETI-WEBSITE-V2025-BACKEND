<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel API is running',
        'timestamp' => now()->toISOString(),
    ]);
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/profile', [AdminAuthController::class, 'profile']);
        Route::get('/verify', [AdminAuthController::class, 'verify']);
    });
});

// Protected user route
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// User Management API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Get available roles
    Route::get('/roles', [UserController::class, 'getRoles']);

    // User management routes
    Route::apiResource('users', UserController::class);

    // Additional user endpoints
    Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
});
