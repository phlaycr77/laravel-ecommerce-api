<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderCodTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_checkout_cod_and_cancel_pending_order(): void
    {
        $user = User::create([
            'name'      => 'Khach Mua Sach',
            'email'     => 'order@bookstore.vn',
            'password'  => Hash::make('123456'),
            'role'      => 'customer',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Kinh tế']);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Cha Giàu Cha Nghèo',
            'price'       => 110000,
            'stock'       => 10,
        ]);

        CartItem::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 3,
        ]);

        $token = auth('api')->login($user);

        // 1. Đặt hàng COD -> tồn kho phải giảm từ 10 xuống 7, giỏ hàng trống
        $checkoutRes = $this->withHeader('Authorization', "Bearer {$token}")
                            ->postJson('/api/orders', [
                                'receiver_name'    => 'Nguyen Van A',
                                'phone'            => '0901234567',
                                'shipping_address' => '123 Le Loi, Q1, TP.HCM',
                            ]);

        $checkoutRes->assertStatus(201);
        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);

        $orderId = $checkoutRes->json('data.id');

        // 2. Hủy đơn khi còn pending -> tồn kho hoàn lại 10
        $cancelRes = $this->withHeader('Authorization', "Bearer {$token}")
                          ->patchJson("/api/orders/{$orderId}/cancel");

        $cancelRes->assertStatus(200);
        $this->assertEquals(10, $product->fresh()->stock);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => 'cancelled']);
    }
}