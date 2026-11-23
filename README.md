# BookStore Laravel eCommerce API

Hệ thống Backend RESTful API cho website thương mại điện tử bán sách (**BookStore**), xây dựng trên nền tảng **Laravel** với cơ chế xác thực **JWT**, phân quyền theo vai trò (**RBAC**: `admin` / `customer`), xử lý giao dịch đặt hàng COD an toàn bằng **Database Transaction** và bộ kiểm thử tự động **Feature Tests**.

* **Sinh viên thực hiện:** Nguyễn Thành Bình – MSSV: `0306241431`
* **Giảng viên hướng dẫn:** ThS. Lữ Cao Tiến
* **Repository GitHub:** https://github.com/phlaycr77/laravel-ecommerce-api.git
* **Bảng tiến độ Trello:** https://trello.com/b/028DpbOT/laravel-ecommerce-api-nguy%E1%BB%85nthanhbinh
* **Link Video Demo:** `[Dán link Video Demo của bạn tại đây]`

---

## 1. Công nghệ sử dụng

* **Ngôn ngữ & Framework:** PHP 8.x, Laravel
* **Cơ sở dữ liệu:** MySQL (6 bảng chuẩn hóa 3NF: `users`, `categories`, `products`, `cart_items`, `orders`, `order_items`)
* **Xác thực & Bảo mật:** JSON Web Token (`tymon/jwt-auth`), mã hóa mật khẩu Bcrypt, Middleware kiểm tra vai trò `admin` và trạng thái khóa tài khoản (`is_active`)
* **Kiểm thử & Tài liệu API:** PHPUnit Feature Tests, Postman Collection v2.1 (`docs/BookStore Laravel API.json`)

---

## 2. Cấu trúc thư mục chính

```text
laravel-ecommerce-api/
├── ai-logs/                     # Nhật ký sử dụng và kiểm chứng AI (AI-01.md -> AI-06.md)
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/     # REST API Controllers (Auth, Profile, Category, Product, Cart, Order, Admin...)
│   │   ├── Middleware/          # Middleware xác thực JWT & phân quyền Admin
│   │   └── Requests/Api/        # FormRequest validate dữ liệu đầu vào
│   ├── Models/                  # Eloquent Models (User, Category, Product, CartItem, Order, OrderItem)
│   ├── Repositories/            # Tầng truy xuất dữ liệu (Contracts + Implementations)
│   └── Services/                # Tầng xử lý nghiệp vụ (AuthService, CartService, OrderService...)
├── database/
│   ├── migrations/              # Định nghĩa cấu trúc 6 bảng CSDL
│   └── seeders/                 # Dữ liệu mẫu (1 Admin, 10 Khách hàng, 5 Danh mục, 30 Sách, 10 Đơn hàng)
├── docs/
│   ├── BookStore Laravel API.json # Bộ sưu tập 23 API trên Postman
│   ├── moc-1.md & moc-2.md      # Tài liệu phân tích thiết kế & tiến độ các mốc
│   └── test-cases.md            # Bảng kịch bản kiểm thử chi tiết
└── tests/
    └── Feature/                 # 5 bộ kiểm thử tự động toàn bộ luồng nghiệp vụ