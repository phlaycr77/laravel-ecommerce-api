<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function getOrdersByUser(int $userId);
    public function findOrderByUserAndId(int $userId, int $orderId);
    public function createOrder(array $data);
    public function createOrderItem(array $data);
    public function getAllOrders(array $filters = []);
    public function findById(int $orderId);
    public function updateStatus(int $orderId, string $status);
}