<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::POST('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::GET('/me', [AuthController::class, 'me']);
        Route::POST('/logout', [AuthController::class, 'logout']);
        Route::get('product-categories/options', [ProductCategoryController::class, 'options']);
        Route::get('products/options', [ProductController::class, 'options']);
        Route::get('customers/options', [CustomerController::class, 'options']);
        Route::apiResource('product-categories', ProductCategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('customers', CustomerController::class);
    });
});
