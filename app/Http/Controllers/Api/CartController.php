<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->cartService->getUserCart(auth('api')->id()),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        try {
            $item = $this->cartService->addToCart(
                auth('api')->id(),
                $validated['product_id'],
                $validated['quantity']
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Đã thêm sách vào giỏ hàng.',
                'data'    => $item,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item = $this->cartService->updateCartItem(
            auth('api')->id(),
            $id,
            $validated['quantity']
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật số lượng thành công.',
            'data'    => $item,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->cartService->removeCartItem(auth('api')->id(), $id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
        ]);
    }
}