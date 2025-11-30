<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;

// Authentication routes
Route::prefix('api/v1/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/me', [AuthController::class, 'user'])->middleware('auth:sanctum');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
});

// User management routes
Route::middleware(['auth:sanctum', 'permission:users.view'])->prefix('api/v1/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create');
    Route::get('/{user}', [UserController::class, 'show']);
    Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users.edit');
    Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete');
    Route::get('/roles', [UserController::class, 'roles']);
    Route::get('/permissions', [UserController::class, 'permissions']);
});

// Dashboard API route
Route::get('/api/v1/dashboard', function () {
    return response()->json([
        'message' => 'Welcome to Academic Scheduling System Dashboard',
        'version' => '1.0.0',
        'status' => 'success'
    ]);
})->middleware('auth:sanctum');

// API Info route
Route::get('/api/v1', function () {
    return response()->json([
        'message' => 'Academic Scheduling System API',
        'version' => '1.0.0',
        'status' => 'success',
        'endpoints' => [
            'auth' => [
                'login' => '/api/v1/auth/login',
                'logout' => '/api/v1/auth/logout',
                'register' => '/api/v1/auth/register',
                'me' => '/api/v1/auth/me',
                'refresh' => '/api/v1/auth/refresh'
            ],
            'users' => '/api/v1/users',
            'dashboard' => '/api/v1/dashboard'
        ]
    ]);
});

// Test simple route first
Route::get('/api/test-simple', function() {
    return response()->json(['message' => 'Test route works!']);
});

// SPA routes - serve Vue application
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
