<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Nạp dữ liệu mẫu bảng users (Admin, Khách hàng, Tài khoản bị khóa)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'khach@gmail.com'],
            [
                'name' => 'Nguyễn Văn Khách',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'khoa@gmail.com'],
            [
                'name' => 'Tài khoản bị khóa',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'is_active' => false,
            ]
        );

        // 2. Nạp dữ liệu mẫu bảng categories (tự kiểm tra cột slug, description)
        $categories = [
            [
                'name' => 'Công nghệ thông tin',
                'description' => 'Sách lập trình, kiến trúc phần mềm, cơ sở dữ liệu',
            ],
            [
                'name' => 'Kinh tế - Quản trị',
                'description' => 'Sách khởi nghiệp, quản lý dự án và tài chính',
            ],
            [
                'name' => 'Văn học - Kỹ năng',
                'description' => 'Tiểu thuyết và sách phát triển bản thân',
            ],
        ];

        $hasCatSlug = Schema::hasColumn('categories', 'slug');
        $hasCatDesc = Schema::hasColumn('categories', 'description');

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            $lookup = $hasCatSlug ? ['slug' => $slug] : ['name' => $cat['name']];

            $data = [
                'name' => $cat['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if ($hasCatSlug) {
                $data['slug'] = $slug;
            }
            if ($hasCatDesc) {
                $data['description'] = $cat['description'];
            }

            DB::table('categories')->updateOrInsert($lookup, $data);
        }

        $cnttId = DB::table('categories')->where('name', 'Công nghệ thông tin')->value('id');
        $kinhTeId = DB::table('categories')->where('name', 'Kinh tế - Quản trị')->value('id');
        $vanHocId = DB::table('categories')->where('name', 'Văn học - Kỹ năng')->value('id');

        // 3. Nạp dữ liệu mẫu bảng products (hoặc books)
        $productTable = Schema::hasTable('products') ? 'products' : 'books';
        if (!Schema::hasTable($productTable)) {
            return;
        }

        $nameCol = Schema::hasColumn($productTable, 'title') ? 'title' : 'name';
        $stockCol = Schema::hasColumn($productTable, 'stock')
            ? 'stock'
            : (Schema::hasColumn($productTable, 'quantity') ? 'quantity' : null);

        $items = [
            [
                'category_id' => $cnttId,
                'title' => 'Clean Code - Mã sạch và con đường trở thành lập trình viên giỏi',
                'author' => 'Robert C. Martin',
                'price' => 280000,
                'stock' => 25,
                'description' => 'Hướng dẫn viết mã nguồn dễ đọc, dễ bảo trì và tái cấu trúc.',
            ],
            [
                'category_id' => $cnttId,
                'title' => 'Thiết kế hệ thống dữ liệu lớn (Designing Data-Intensive Applications)',
                'author' => 'Martin Kleppmann',
                'price' => 450000,
                'stock' => 12,
                'description' => 'Kiến trúc cơ sở dữ liệu phân tán, xử lý luồng và độ tin cậy hệ thống.',
            ],
            [
                'category_id' => $cnttId,
                'title' => 'Lập trình Laravel thực chiến',
                'author' => 'Nguyễn Minh Hoàng',
                'price' => 195000,
                'stock' => 0, // Sách hết hàng để test kiểm tra tồn kho
                'description' => 'Xây dựng RESTful API chuẩn kiến trúc phân lớp với Laravel.',
            ],
            [
                'category_id' => $kinhTeId,
                'title' => 'Khởi nghiệp tinh gọn (The Lean Startup)',
                'author' => 'Eric Ries',
                'price' => 165000,
                'stock' => 30,
                'description' => 'Phương pháp xây dựng sản phẩm khả dụng tối thiểu (MVP) và lặp nhanh.',
            ],
            [
                'category_id' => $kinhTeId,
                'title' => 'Tâm lý học về tiền',
                'author' => 'Morgan Housel',
                'price' => 140000,
                'stock' => 40,
                'description' => 'Những bài học vượt thời gian về tài chính cá nhân và đầu tư.',
            ],
            [
                'category_id' => $vanHocId,
                'title' => 'Nhà giả kim',
                'author' => 'Paulo Coelho',
                'price' => 89000,
                'stock' => 50,
                'description' => 'Hành trình theo đuổi vận mệnh và ước mơ của chàng chăn cừu Santiago.',
            ],
        ];

        foreach ($items as $item) {
            $data = [
                $nameCol => $item['title'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn($productTable, 'slug')) {
                $data['slug'] = Str::slug($item['title']);
            }
            if (Schema::hasColumn($productTable, 'category_id')) {
                $data['category_id'] = $item['category_id'];
            }
            if (Schema::hasColumn($productTable, 'author')) {
                $data['author'] = $item['author'];
            }
            if (Schema::hasColumn($productTable, 'price')) {
                $data['price'] = $item['price'];
            }
            if ($stockCol) {
                $data[$stockCol] = $item['stock'];
            }
            if (Schema::hasColumn($productTable, 'description')) {
                $data['description'] = $item['description'];
            }

            DB::table($productTable)->updateOrInsert(
                [$nameCol => $item['title']],
                $data
            );
        }
    }
}