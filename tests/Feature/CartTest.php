<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_product_to_cart_and_cannot_exceed_stock(): void
    {
        $user = User::create([
            'name'      => 'Khach Hang',
            'email'     => 'buyer@bookstore.vn',
            'password'  => Hash::make('123456'),
            'role'      => 'customer',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Công nghệ']);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Clean Code',
            'price'       => 250000,
            'stock'       => 5,
        ]);

        $token = auth('api')->login($user);

        // Thêm hợp lệ 2 cuốn
        $resOk = $this->withHeader('Authorization', "Bearer {$token}")
                      ->postJson('/api/cart', [
                          'product_id' => $product->id,
                          'quantity'   => 2,
                      ]);
        $resOk->assertStatus(201);

        // Thêm vượt tồn kho (10 > 5)
        $resFail = $this->withHeader('Authorization', "Bearer {$token}")
                        ->postJson('/api/cart', [
                            'product_id' => $product->id,
                            'quantity'   => 10,
                        ]);
        $resFail->assertStatus(400);
    }
}