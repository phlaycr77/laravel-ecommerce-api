<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->orderService->getMyOrders(auth('api')->id()),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getMyOrderDetail(auth('api')->id(), $id);
            return response()->json([
                'status' => 'success',
                'data'   => $order,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 404);
        }
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->checkoutCOD(
                auth('api')->id(),
                $request->validated()
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Đặt hàng COD thành công.',
                'data'    => $order,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->cancelMyOrder(auth('api')->id(), $id);
            return response()->json([
                'status'  => 'success',
                'message' => 'Đã hủy đơn hàng và hoàn lại tồn kho.',
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