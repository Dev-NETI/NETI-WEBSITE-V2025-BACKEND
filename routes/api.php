<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Events\EventController;
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

    Route::middleware(['auth:sanctum'])->group(function () {
        // Event management routes
        Route::apiResource('events', EventController::class);
    });
});

// Public news endpoint for homepage
Route::get('/news/public', [NewsController::class, 'getPublicNews']);

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

    // News management routes
    Route::apiResource('news', NewsController::class);
    Route::patch('news/{id}/reactivate', [NewsController::class, 'reactivate']);
});
