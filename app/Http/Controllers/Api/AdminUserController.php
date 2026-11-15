<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminUserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct(
        protected AdminUserService $adminUserService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->adminUserService->listUsers($request->all()),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        try {
            $user = $this->adminUserService->toggleUserStatus(
                auth('api')->id(),
                $id,
                $validated['is_active']
            );

            return response()->json([
                'status'  => 'success',
                'message' => $validated['is_active'] ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.',
                'data'    => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }
}