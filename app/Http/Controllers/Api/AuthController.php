<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Đăng ký tài khoản thành công',
            'data'    => $user,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email hoặc mật khẩu không chính xác',
            ], 401);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Đăng nhập thành công',
            'data'    => $result,
        ], 200);
    }
    public function me(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->authService->me(),
        ], 200);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'status'  => 'success',
            'message' => 'Đăng xuất thành công',
        ], 200);
    }
}