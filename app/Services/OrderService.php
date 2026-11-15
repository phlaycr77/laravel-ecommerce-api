<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepo,
        protected CartRepositoryInterface $cartRepo
    ) {}

    public function getMyOrders(int $userId)
    {
        return $this->orderRepo->getOrdersByUser($userId);
    }

    public function getMyOrderDetail(int $userId, int $orderId)
    {
        $order = $this->orderRepo->findOrderByUserAndId($userId, $orderId);

        if (!$order) {
            throw new Exception('Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn này.', 404);
        }

        return $order;
    }

    public function checkoutCOD(int $userId, array $shippingData)
    {
        $cartItems = $this->cartRepo->getByUser($userId);

        if ($cartItems->isEmpty()) {
            throw new Exception('Giỏ hàng đang trống, không thể đặt hàng.', 400);
        }

        return DB::transaction(function () use ($userId, $shippingData, $cartItems) {
            $totalAmount = 0;
            $orderItemsPayload = [];

            foreach ($cartItems as $item) {
                // Khóa dòng sản phẩm để chống 2 người đặt trùng làm âm kho
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (!$product || $product->stock < $item->quantity) {
                    $bookName = $product ? $product->name : 'Sản phẩm';
                    throw new Exception("Sách '{$bookName}' không đủ số lượng tồn kho.", 400);
                }

                $product->decrement('stock', $item->quantity);

                $lineTotal = $product->price * $item->quantity;
                $totalAmount += $lineTotal;

                $orderItemsPayload[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item->quantity,
                    'unit_price' => $product->price,
                ];
            }

            $order = $this->orderRepo->createOrder([
                'user_id'          => $userId,
                'total_amount'     => $totalAmount,
                'status'           => 'pending',
                'payment_method'   => 'COD',
                'receiver_name'    => $shippingData['receiver_name'],
                'phone'            => $shippingData['phone'],
                'shipping_address' => $shippingData['shipping_address'],
                'note'             => $shippingData['note'] ?? null,
            ]);

            foreach ($orderItemsPayload as $payload) {
                $payload['order_id'] = $order->id;
                $this->orderRepo->createOrderItem($payload);
            }

            $this->cartRepo->clearCart($userId);

            return $order->load('items.product');
        });
    }

    public function cancelMyOrder(int $userId, int $orderId)
    {
        $order = $this->orderRepo->findOrderByUserAndId($userId, $orderId);

        if (!$order) {
            throw new Exception('Không tìm thấy đơn hàng hoặc bạn không có quyền hủy đơn này.', 404);
        }

        if ($order->status !== 'pending') {
            throw new Exception('Chỉ được hủy đơn hàng ở trạng thái chờ xác nhận (pending).', 400);
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);
            return $order->fresh('items.product');
        });
        
    }
    public function getAllOrdersForAdmin(array $filters = [])
    {
        return $this->orderRepo->getAllOrders($filters);
    }

    public function getOrderDetailForAdmin(int $orderId)
    {
        return $this->orderRepo->findById($orderId);
    }

    public function updateOrderStatusByAdmin(int $orderId, string $newStatus)
    {
        $order = $this->orderRepo->findById($orderId);

        if (in_array($order->status, ['delivered', 'cancelled'], true)) {
            throw new Exception('Không thể thay đổi trạng thái của đơn đã hoàn thành hoặc đã hủy.', 400);
        }

        return DB::transaction(function () use ($order, $newStatus) {
            // Nếu Admin chuyển sang hủy đơn thì hoàn lại tồn kho
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            return $this->orderRepo->updateStatus($order->id, $newStatus);
        });
    }
}