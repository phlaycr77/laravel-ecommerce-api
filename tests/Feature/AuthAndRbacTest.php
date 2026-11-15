<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_and_login_to_get_jwt_token(): void
    {
        $registerRes = $this->postJson('/api/auth/register', [
            'name'                  => 'Nguyen Van A',
            'email'                 => 'khachhang@bookstore.vn',
            'password'              => '123456',
            'password_confirmation' => '123456',
        ]);

        $registerRes->assertStatus(201);

        $loginRes = $this->postJson('/api/auth/login', [
            'email'    => 'khachhang@bookstore.vn',
            'password' => '123456',
        ]);

        $loginRes->assertStatus(200);
        $payload = $loginRes->json();
        $token = $payload['access_token']
            ?? $payload['data']['access_token']
            ?? $payload['token']
            ?? $payload['data']['token']
            ?? null;

        $this->assertNotNull($token, 'Không tìm thấy JWT token trong response: ' . json_encode($payload));
    }

    public function test_customer_cannot_access_admin_routes(): void
    {
        $customer = User::create([
            'name'      => 'Khach Thuong',
            'email'     => 'user@bookstore.vn',
            'password'  => Hash::make('123456'),
            'role'      => 'customer',
            'is_active' => true,
        ]);

        $token = auth('api')->login($customer);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }
}