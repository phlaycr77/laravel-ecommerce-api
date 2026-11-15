<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function getOrdersByUser(int $userId)
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();
    }

    public function findOrderByUserAndId(int $userId, int $orderId)
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->first();
    }

    public function createOrder(array $data)
    {
        return Order::create($data);
    }

    public function createOrderItem(array $data)
    {
        return OrderItem::create($data);
    }
}