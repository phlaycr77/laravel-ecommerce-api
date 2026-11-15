<?php

namespace App\Repositories\Contracts;

interface OrderRepositoryInterface
{
    public function getOrdersByUser(int $userId);
    public function findOrderByUserAndId(int $userId, int $orderId);
    public function createOrder(array $data);
    public function createOrderItem(array $data);
}