<?php

use App\Http\Controllers\Api\CafeApiController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/auth/login', [CafeApiController::class, 'login']);
Route::post('/auth/register', [CafeApiController::class, 'register']);
Route::get('/menus', [CafeApiController::class, 'getMenus']);

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth & Password
    Route::post('/auth/change-password', [CafeApiController::class, 'changePassword']);

    // Pelanggan Orders (Pribadi - Poin 14)
    Route::get('/pesanan/my-orders', [CafeApiController::class, 'getMyOrders']);
    Route::post('/pesanan', [CafeApiController::class, 'createOrder']);
    Route::patch('/pesanan/{id}/status', [CafeApiController::class, 'updateStatus']);

    // Kasir Specific
    Route::get('/kasir/orders', [CafeApiController::class, 'getAllOrders']);
    Route::get('/kasir/customers', [CafeApiController::class, 'getCustomers']);
    Route::post('/kasir/reset-password', [CafeApiController::class, 'resetCustomerPassword']);
    Route::get('/kasir/bahan-baku', [CafeApiController::class, 'getBahanBaku']);
});
