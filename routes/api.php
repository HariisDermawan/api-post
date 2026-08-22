<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group( function () {
    Route::POST('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function() {
        Route::GET('/me', [AuthController::class, 'me']);
        Route::POST('/logout', [AuthController::class, 'logout']);

        Route::apiResource('product-categories', ProductCategoryController::class);
    });
});
