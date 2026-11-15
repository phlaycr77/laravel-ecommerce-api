<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->orderService->getAllOrdersForAdmin($request->all()),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->orderService->getOrderDetailForAdmin($id),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,shipping,delivered,cancelled'],
        ]);

        try {
            $order = $this->orderService->updateOrderStatusByAdmin($id, $validated['status']);

            return response()->json([
                'status'  => 'success',
                'message' => 'Cập nhật trạng thái đơn hàng thành công.',
                'data'    => $order,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }
}