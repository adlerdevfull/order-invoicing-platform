<?php

use App\Http\Controllers\Api\V1\{AuthController, OrderController, ProductController, InvoiceController};
use Illuminate\Support\Facades\Route;

// Health check
Route::get('v1/health', fn () => response()->json(['status' => 'ok', 'service' => 'orders-platform']));

// Public
Route::prefix('v1/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected
Route::prefix('v1')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Products - Admin & Seller
    Route::middleware('role:admin|seller')->group(function () {
        Route::apiResource('products', ProductController::class);
    });

    // Orders - All authenticated
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::patch('orders/{id}/transition', [OrderController::class, 'transition'])
        ->middleware('role:admin|seller');

    // Invoices - Admin & Financial
    Route::middleware('role:admin|financial')->group(function () {
        Route::get('invoices', [InvoiceController::class, 'index']);
        Route::get('invoices/{id}', [InvoiceController::class, 'show']);
        Route::post('invoices', [InvoiceController::class, 'store']);
    });
});
