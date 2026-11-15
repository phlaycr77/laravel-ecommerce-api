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
    public function getAllOrders(array $filters = [])
    {
        $query = Order::with(['user:id,name,email', 'items.product'])->orderByDesc('id');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(15);
    }

    public function findById(int $orderId)
    {
        return Order::with(['user:id,name,email', 'items.product'])->findOrFail($orderId);
    }

    public function updateStatus(int $orderId, string $status)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        return $order->fresh(['user:id,name,email', 'items.product']);
    }
}