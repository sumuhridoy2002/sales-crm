<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sales', [SalesController::class, 'store']);
    Route::get('/ecommerce/products', [ProductApiController::class, 'index']);
});
