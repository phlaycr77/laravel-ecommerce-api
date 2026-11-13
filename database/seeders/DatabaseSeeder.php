<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Tạo 1 Admin + 10 Khách hàng (khách 10 bị khóa is_active = false)
        DB::table('users')->insert([
            'name' => 'Quản Trị Viên',
            'email' => 'admin@bookstore.vn',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $customerIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $customerIds[] = DB::table('users')->insertGetId([
                'name' => "Khách Hàng {$i}",
                'email' => "khachhang{$i}@gmail.com",
                'password' => Hash::make('User@123'),
                'role' => 'customer',
                'is_active' => $i !== 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Tạo 5 Danh mục
        $categories = [
            ['name' => 'Công nghệ thông tin', 'slug' => 'cong-nghe-thong-tin'],
            ['name' => 'Kinh tế & Quản trị', 'slug' => 'kinh-te-quan-tri'],
            ['name' => 'Văn học Việt Nam', 'slug' => 'van-hoc-viet-nam'],
            ['name' => 'Kỹ năng sống', 'slug' => 'ky-nang-song'],
            ['name' => 'Ngoại ngữ', 'slug' => 'ngoai-ngu'],
        ];

        $hasCatSlug = Schema::hasColumn('categories', 'slug');
        $categoryIds = [];
        foreach ($categories as $cat) {
            $data = [
                'name' => $cat['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if ($hasCatSlug) {
                $data['slug'] = $cat['slug'];
            }
            $categoryIds[] = DB::table('categories')->insertGetId($data);
        }

        // 3. Tạo 30 Đầu sách (tự nhận diện các cột có trong bảng products)
        $hasProdAuthor = Schema::hasColumn('products', 'author');
        $hasProdSlug = Schema::hasColumn('products', 'slug');
        $hasProdStock = Schema::hasColumn('products', 'stock');
        $hasProdQty = Schema::hasColumn('products', 'quantity');
        $hasProdDesc = Schema::hasColumn('products', 'description');

        $productIds = [];
        for ($i = 1; $i <= 30; $i++) {
            $price = 55000 + ($i * 5000);
            $stock = ($i % 10 === 0) ? 0 : (10 + $i);

            $data = [
                'category_id' => $categoryIds[($i - 1) % 5],
                'name' => "Sách Mẫu Chuyên Đề Số {$i}",
                'price' => $price,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if ($hasProdAuthor) {
                $data['author'] = "Tác Giả {$i}";
            }
            if ($hasProdSlug) {
                $data['slug'] = "sach-mau-chuyen-de-so-{$i}";
            }
            if ($hasProdStock) {
                $data['stock'] = $stock;
            } elseif ($hasProdQty) {
                $data['quantity'] = $stock;
            }
            if ($hasProdDesc) {
                $data['description'] = "Mô tả chi tiết nội dung cuốn sách số {$i}.";
            }

            $productIds[] = DB::table('products')->insertGetId($data);
        }

        // 4. Tạo 10 Đơn hàng mẫu nhiều trạng thái
        $statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        $hasShippingAddr = Schema::hasColumn('orders', 'shipping_address');
        $hasAddr = Schema::hasColumn('orders', 'address');
        $hasPhone = Schema::hasColumn('orders', 'phone');
        $hasPayment = Schema::hasColumn('orders', 'payment_method');
        $hasTotalAmount = Schema::hasColumn('orders', 'total_amount');
        $hasTotalPrice = Schema::hasColumn('orders', 'total_price');
        $hasUnitPrice = Schema::hasColumn('order_items', 'unit_price');

        for ($i = 1; $i <= 10; $i++) {
            $prod1 = $productIds[$i - 1];
            $prod2 = $productIds[$i];
            $price1 = 55000 + ($i * 5000);
            $price2 = 55000 + (($i + 1) * 5000);
            $totalAmount = ($price1 * 1) + ($price2 * 2);

            $orderData = [
                'user_id' => $customerIds[($i - 1) % 9],
                'status' => $statuses[($i - 1) % 5],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if ($hasShippingAddr) {
                $orderData['shipping_address'] = "Số {$i}0 Đường Nguyễn Văn Cừ, Quận 5, TP.HCM";
            } elseif ($hasAddr) {
                $orderData['address'] = "Số {$i}0 Đường Nguyễn Văn Cừ, Quận 5, TP.HCM";
            }
            if ($hasPhone) {
                $orderData['phone'] = sprintf('09012345%02d', $i);
            }
            if ($hasPayment) {
                $orderData['payment_method'] = 'COD';
            }
            if ($hasTotalAmount) {
                $orderData['total_amount'] = $totalAmount;
            } elseif ($hasTotalPrice) {
                $orderData['total_price'] = $totalAmount;
            } else {
                $orderData['total'] = $totalAmount;
            }

            $orderId = DB::table('orders')->insertGetId($orderData);

            $priceCol = $hasUnitPrice ? 'unit_price' : 'price';
            DB::table('order_items')->insert([
                [
                    'order_id' => $orderId,
                    'product_id' => $prod1,
                    'quantity' => 1,
                    $priceCol => $price1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'order_id' => $orderId,
                    'product_id' => $prod2,
                    'quantity' => 2,
                    $priceCol => $price2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }
}