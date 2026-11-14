<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function listProducts(array $filters)
    {
        $perPage = isset($filters['per_page']) ? min((int) $filters['per_page'], 50) : 10;
        return $this->productRepo->searchAndFilter($filters, $perPage);
    }

    public function getFeaturedProducts()
    {
        return $this->productRepo->getFeatured(8);
    }

    public function getProductDetail(int $id)
    {
        return $this->productRepo->findById($id);
    }

    public function createProduct(array $data, ?UploadedFile $image = null)
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }

        return $this->productRepo->create($data);
    }

    public function updateProduct(int $id, array $data, ?UploadedFile $image = null)
    {
        $product = $this->productRepo->findById($id);

        if ($image) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        }

        return $this->productRepo->update($id, $data);
    }

    public function deleteProduct(int $id)
    {
        $product = $this->productRepo->findById($id);

        if ($product->orderItems()->exists()) {
            throw new Exception('Không thể xóa sách đã phát sinh trong đơn hàng.', 400);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        return $this->productRepo->delete($id);
    }
}