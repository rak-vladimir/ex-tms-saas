<?php

use App\Http\Controllers\CourierController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\TenantMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(TenantMiddleware::class)->group(function () {
    // Orders
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{order}', 'show');
        Route::post('{order}/assign', 'assign');
        Route::post('{order}/status', 'updateStatus');
    });

    // Couriers
    Route::controller(CourierController::class)->prefix('couriers')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{courier}', 'show');
        Route::delete('{courier}', 'destroy');
    });
});
