<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Chưa đăng nhập hoặc token không hợp lệ
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        // 2. Tài khoản bị khóa
        if ($user->is_active == false || $user->status === 'locked') {
            return response()->json(['message' => 'Tài khoản đã bị khóa'], 403);
        }

        // 3. Không phải tài khoản admin (ví dụ role === 'customer')[cite: 28, 30]
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Không có quyền truy cập khu vực quản trị'
            ], 403);
        }

        return $next($request);
    }
}