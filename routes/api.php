<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::middleware('throttle:auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    

    Route::middleware(['auth:api', 'throttle:api'])->group(function () {
        Route::post('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    // Get all products (paginated)
    Route::get('products', [ProductController::class, 'index']);

    // Get a single product by ID
    Route::get('products/{product_id}', [ProductController::class, 'show']);
});
