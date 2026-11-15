<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\CartRepositoryInterface;
use Exception;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $cartRepo
    ) {}

    public function getUserCart(int $userId)
    {
        $items = $this->cartRepo->getByUser($userId);
        $totalAmount = $items->sum(fn ($item) => $item->quantity * ($item->product->price ?? 0));

        return [
            'items'        => $items,
            'total_items'  => $items->sum('quantity'),
            'total_amount' => $totalAmount,
        ];
    }

    public function addToCart(int $userId, int $productId, int $quantity)
    {
        $product = Product::findOrFail($productId);

        if ($product->stock < $quantity) {
            throw new Exception('Số lượng sản phẩm trong kho không đủ.', 400);
        }

        return $this->cartRepo->addOrUpdate($userId, $productId, $quantity);
    }

    public function updateCartItem(int $userId, int $cartItemId, int $quantity)
    {
        return $this->cartRepo->updateQuantity($userId, $cartItemId, $quantity);
    }

    public function removeCartItem(int $userId, int $cartItemId)
    {
        return $this->cartRepo->removeItem($userId, $cartItemId);
    }
}