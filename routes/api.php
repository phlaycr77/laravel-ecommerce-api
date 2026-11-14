<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

// Nhóm API Xác thực (Đăng ký, Đăng nhập, Profile, Đăng xuất)
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

// Nhóm API Công khai cho Khách xem sách (TM-01, TM-02, TM-03)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Nhóm API dành riêng cho Admin (/api/admin/*) - dùng chung guard auth:api
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