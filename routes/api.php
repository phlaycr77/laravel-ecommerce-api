<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

// Nhóm API dành riêng cho Admin (/api/admin/*) - dùng chung guard auth:api
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    // Route mẫu để test Postman
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Chào mừng Admin!']);
    });

    // Sau này thêm các API quản lý sách, danh mục, đơn hàng vào đây
});