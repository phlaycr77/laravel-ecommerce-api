<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_search_and_filter_products(): void
    {
        $category = Category::create(['name' => 'Kỹ năng sống']);

        Product::create([
            'category_id' => $category->id,
            'name'        => 'Đắc Nhân Tâm',
            'price'       => 85000,
            'stock'       => 20,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name'        => 'Nhà Giả Kim',
            'price'       => 120000,
            'stock'       => 15,
        ]);

        $response = $this->getJson('/api/products?keyword=Đắc');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Đắc Nhân Tâm'])
                 ->assertJsonMissing(['name' => 'Nhà Giả Kim']);
    }

    public function test_admin_can_create_new_product(): void
    {
        $admin = User::create([
            'name'      => 'Admin',
            'email'     => 'admin@bookstore.vn',
            'password'  => Hash::make('123456'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Văn học']);
        $token = auth('api')->login($admin);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/admin/products', [
                             'category_id' => $category->id,
                             'name'        => 'Số Đỏ',
                             'price'       => 95000,
                             'stock'       => 30,
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Số Đỏ']);
    }
}