<?php

use Illuminate\Support\Facades\Route;

Route::post('/courier/login', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'login']); // Giriş için API Route
Route::post('/courier/register', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'register']); // Giriş için API Route

// Auth middleware ile korunan route'lar
Route::middleware('jwt.courier')->group(function () {
    Route::prefix('courier')->group(function () {
        Route::post('auth/logout', [\App\Http\Controllers\Api\v2\Courier\Auth\LoginController::class, 'logout']);

        Route::get('/profile', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'index']);
        Route::post('/profile', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'update']);
        Route::delete('/', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'destroy']);
        Route::post('/profile/update-password', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'updatePassword']);
        Route::post('/profile/update-status', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'updateStatus']);
        Route::post('/location', [\App\Http\Controllers\Api\v2\Courier\Profile\IndexController::class, 'updateLocation']);
        Route::get('/orders', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'activeOrders']);
        Route::get('/available-orders', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'availableOrders']);
        Route::get('/old-orders', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'pastOrdes']);
        Route::get('/report', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'report']);
        Route::post('/order/{orderId}/status', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'changeStatus']);
        Route::post('/order/{orderId}/transfer', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'transfer']);
        Route::post('/order/{orderId}/verify', [\App\Http\Controllers\Api\v2\Courier\Orders\OrderController::class, 'verifyOrderCode']);
    });
});
