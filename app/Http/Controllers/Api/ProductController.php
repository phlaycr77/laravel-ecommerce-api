<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductRequest;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    // TM-02: Danh sách, tìm kiếm, lọc, phân trang phía server
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->listProducts($request->all());

        return response()->json([
            'status' => 'success',
            'data'   => $products,
        ]);
    }

    // TM-01: Sách nổi bật cho trang chủ
    public function featured(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data'   => $this->productService->getFeaturedProducts(),
        ]);
    }

    // TM-03: Chi tiết sản phẩm
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductDetail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $product,
        ]);
    }

    // TM-12: Admin thêm sản phẩm + upload ảnh
    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct(
            $request->validated(),
            $request->file('image')
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Thêm sản phẩm thành công.',
            'data'    => $product,
        ], 201);
    }

    // TM-12: Admin cập nhật sản phẩm + ảnh
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateProduct(
            $id,
            $request->validated(),
            $request->file('image')
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật sản phẩm thành công.',
            'data'    => $product,
        ]);
    }

    // TM-12: Admin xóa sản phẩm
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);
            return response()->json([
                'status'  => 'success',
                'message' => 'Xóa sản phẩm thành công.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }
}