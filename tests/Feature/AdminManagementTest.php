<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_order_status_and_lock_user(): void
    {
        $admin = User::create([
            'name'      => 'Admin',
            'email'     => 'admin@bookstore.vn',
            'password'  => Hash::make('123456'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $customer = User::create([
            'name'      => 'Customer',
            'email'     => 'cust@bookstore.vn',
            'password'  => Hash::make('123456'),
            'role'      => 'customer',
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id'      => $customer->id,
            'total_amount' => 200000,
            'status'       => 'pending',
        ]);

        $token = auth('api')->login($admin);

        // Admin duyệt đơn sang confirmed
        $orderRes = $this->withHeader('Authorization', "Bearer {$token}")
                         ->patchJson("/api/admin/orders/{$order->id}/status", [
                             'status' => 'confirmed',
                         ]);
        $orderRes->assertStatus(200);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);

        // Admin khóa tài khoản customer
        $userRes = $this->withHeader('Authorization', "Bearer {$token}")
                        ->patchJson("/api/admin/users/{$customer->id}/status", [
                            'is_active' => false,
                        ]);
        $userRes->assertStatus(200);
        $this->assertFalse((bool) $customer->fresh()->is_active);
    }
}