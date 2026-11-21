# BookStore Laravel eCommerce API

Hệ thống Backend RESTful API cho website thương mại điện tử bán sách (**BookStore**), xây dựng trên nền tảng **Laravel** với cơ chế xác thực **JWT**, phân quyền theo vai trò (**RBAC**: `admin` / `customer`), xử lý giao dịch đặt hàng COD an toàn bằng **Database Transaction** và bộ kiểm thử tự động **Feature Tests**.

---

## 1. Công nghệ sử dụng

* **Ngôn ngữ & Framework:** PHP 8.x, Laravel
* **Cơ sở dữ liệu:** MySQL (6 bảng: `users`, `categories`, `products`, `cart_items`, `orders`, `order_items`)
* **Xác thực & Bảo mật:** JSON Web Token (`tymon/jwt-auth` / `php-open-source-saver/jwt-auth`), mã hóa mật khẩu Bcrypt, Middleware kiểm tra vai trò và trạng thái khóa tài khoản (`is_active`)
* **Kiểm thử & Tài liệu API:** PHPUnit Feature Tests, Postman Collection v2.1 (`docs/BookStore Laravel API.json`)

---

## 2. Cấu trúc thư mục chính

```text
laravel-ecommerce-api/
├── ai-logs/                     # Nhật ký sử dụng và kiểm chứng AI (AI-03.md, AI-04.md)
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/     # Các REST API Controller (Auth, Profile, Category, Product, Cart, Order, Admin...)
│   │   ├── Middleware/          # Middleware xác thực JWT & phân quyền Admin/Customer
│   │   └── Requests/            # FormRequest validate dữ liệu đầu vào
│   ├── Models/                  # Eloquent Models (User, Category, Product, CartItem, Order, OrderItem)
│   ├── Repositories/            # Tầng truy xuất dữ liệu
│   └── Services/                # Tầng xử lý nghiệp vụ (AuthService, CartService, OrderService...)
├── database/
│   ├── migrations/              # Định nghĩa cấu trúc 6 bảng CSDL
│   └── seeders/                 # Dữ liệu mẫu (Admin, Khách hàng demo, Danh mục, Sách)
├── docs/
│   ├── BookStore Laravel API.json # Bộ sưu tập 23 API trên Postman
│   ├── moc-2.md                 # Báo cáo tiến độ hoàn thành Mốc 2
│   └── test-cases.md            # Bảng kịch bản kiểm thử chi tiết
└── tests/
    └── Feature/                 # 5 bộ kiểm thử tự động toàn bộ luồng nghiệp vụ
```

---

## 3. Hướng dẫn cài đặt và khởi chạy

### Bước 1: Cài đặt thư viện phụ thuộc
```bash
composer install
```

### Bước 2: Cấu hình môi trường
Sao chép tệp `.env.example` thành `.env` và cập nhật thông tin kết nối MySQL (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`):
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### Bước 3: Khởi tạo cơ sở dữ liệu và dữ liệu mẫu
```bash
php artisan migrate --seed
```

### Bước 4: Khởi chạy máy chủ API
```bash
php artisan serve
```
Máy chủ sẽ hoạt động tại địa chỉ mặc định: `http://127.0.0.1:8000` (Base API URL: `http://127.0.0.1:8000/api`).

---

## 4. Tài khoản mẫu phục vụ kiểm thử & Demo

Sau khi chạy `php artisan migrate --seed`, hệ thống có sẵn các tài khoản mặc định:

| Vai trò | Email | Mật khẩu | Ghi chú |
| :--- | :--- | :--- | :--- |
| **Khách hàng (Customer)** | `khachdemo@gmail.com` | `password` | Dùng cho luồng xem giỏ hàng, đặt hàng COD, quản lý hồ sơ |
| **Quản trị viên (Admin)** | *(Xem trong `database/seeders/`)* | `password` | Dùng cho quản lý danh mục, sản phẩm, duyệt đơn, khóa tài khoản |

---

## 5. Kiểm thử tự động & Kiểm thử Postman

### Chạy bộ kiểm thử tự động (Feature Tests)
```bash
php artisan test
```
Bao gồm 5 bộ kiểm thử: `AuthAndRbacTest`, `ProductCatalogTest`, `CartTest`, `OrderCodTest`, `AdminManagementTest`.

### Kiểm thử thủ công bằng Postman
1. Mở **Postman** -> chọn **Import** -> chọn tệp `docs/BookStore Laravel API.json`.
2. Đảm bảo biến `base_url` có giá trị `http://127.0.0.1:8000/api`.
3. Chạy request **Login Customer** hoặc **Login Admin** để Postman tự động lưu token vào biến môi trường trước khi gọi các API được bảo vệ.