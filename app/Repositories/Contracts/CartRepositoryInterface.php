<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface
{
    public function getByUser(int $userId);
    public function addOrUpdate(int $userId, int $productId, int $quantity);
    public function updateQuantity(int $userId, int $cartItemId, int $quantity);
    public function removeItem(int $userId, int $cartItemId);
    public function clearCart(int $userId);
}