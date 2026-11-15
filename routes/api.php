<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

// 1. Nhóm API Xác thực (TM-04)
Route::prefix('auth')->group(function () {
    // API công khai
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // API bắt buộc phải truyền JWT Token (Header Bearer)
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// 2. Nhóm API Công khai cho Khách xem sách (TM-01, TM-02, TM-03)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// 3. Nhóm API Khách hàng đã đăng nhập (TM-05, TM-06, TM-07)
Route::middleware('auth:api')->group(function () {
    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    // Đơn hàng COD
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});

// 4. Nhóm API dành riêng cho Admin (/api/admin/*) - dùng chung guard auth:api
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    // Route mẫu để test Postman
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Chào mừng Admin!']);
    });

    // TM-11: Quản lý danh mục (Admin)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // TM-12: Quản lý sản phẩm & ảnh (Admin)
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});