<?php

namespace App\Repositories;

use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function getByUser(int $userId)
    {
        return CartItem::with('product.category')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();
    }

    public function addOrUpdate(int $userId, int $productId, int $quantity)
    {
        $item = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
            return $item->load('product');
        }

        return CartItem::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'quantity'   => $quantity,
        ])->load('product');
    }

    public function updateQuantity(int $userId, int $cartItemId, int $quantity)
    {
        $item = CartItem::where('user_id', $userId)->findOrFail($cartItemId);
        $item->update(['quantity' => $quantity]);
        return $item->load('product');
    }

    public function removeItem(int $userId, int $cartItemId)
    {
        $item = CartItem::where('user_id', $userId)->findOrFail($cartItemId);
        return $item->delete();
    }

    public function clearCart(int $userId)
    {
        return CartItem::where('user_id', $userId)->delete();
    }
}