<?php

use App\Http\Controllers\SalesController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

// সেলস রাউট
Route::post('/sales', [SalesController::class, 'store']);

// CRM রাউটস
Route::get('/crm/inactive-customers', [CrmController::class, 'inactiveCustomers']);
Route::post('/crm/customers/{customer}/assign', [CrmController::class, 'assignCustomer']);
use App\Models\Customer; // Ensure customer model is imported or fully qualified
Route::post('/crm/customers/{customer}/re-engage', [CrmController::class, 'reEngage']);

// ই-কমার্স থার্ডপার্টি এপিআই (বোনাস)
Route::get('/ecommerce/products', [ProductApiController::class, 'index']);