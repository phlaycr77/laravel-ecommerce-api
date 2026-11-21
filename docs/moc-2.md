# BÁO CÁO TIẾN ĐỘ HOÀN THÀNH MỐC 2

* **Đề tài:** Hệ thống Thương mại điện tử – BookStore RESTful API (`laravel-ecommerce-api`)
* **Môn học:** Đồ án Lập trình Web
* **Giảng viên hướng dẫn:** Lữ Cao Tiến
* **Sinh viên thực hiện:** Nguyễn Thanh Bình

---

## 1. Tổng quan kiến trúc & Cơ sở dữ liệu đã triển khai

Hệ thống Backend được xây dựng bằng framework **Laravel** theo kiến trúc phân lớp rõ ràng, tách biệt trách nhiệm xử lý giữa các tầng:

* **Tầng định tuyến & Middleware (`routes/api.php`, `app/Http/Middleware/`):** Phân tách API công khai (Public) và API yêu cầu xác thực JWT (`auth:api`), kiểm tra vai trò (`admin` / `customer`) và trạng thái hoạt động của tài khoản (`is_active`).
* **Tầng Validation (`app/Http/Requests/`):** Kiểm tra dữ liệu đầu vào chặt chẽ phía server (định dạng email, độ dài mật khẩu $\ge 8$ ký tự, giá sản phẩm $> 0$, số lượng tồn kho $\ge 0$) và trả về thông báo lỗi chuẩn JSON.
* **Tầng Controller (`app/Http/Controllers/Api/`):** Tiếp nhận request và điều phối phản hồi HTTP chuẩn RESTful (`200`, `201`, `400`, `401`, `403`, `404`, `422`).
* **Tầng nghiệp vụ & Truy xuất dữ liệu (`app/Services/`, `app/Repositories/`, `app/Models/`):** Đóng gói logic nghiệp vụ phức tạp (đặt hàng COD trong `DB::transaction`, kiểm tra tồn kho, cộng dồn giỏ hàng, phân trang và lọc dữ liệu).
* **Cơ sở dữ liệu (6 bảng chuẩn hóa):** `users`, `categories`, `products`, `cart_items`, `orders`, `order_items` kèm đầy đủ migration và seeder dữ liệu mẫu.

---

## 2. Bảng tổng hợp tiến độ hoàn thành theo mã chức năng (TM-01 -> TM-14)

| Mã chức năng | Tên chức năng | API Endpoints đã triển khai | Controller / Test | Tiến độ |
| :--- | :--- | :--- | :--- | :---: |
| **TM-01** | Xem danh sách danh mục | `GET /api/categories` | `CategoryController`<br>`ProductCatalogTest` | **100%** |
| **TM-02** | Xem sản phẩm nổi bật & tìm kiếm, lọc, phân trang | `GET /api/products/featured`<br>`GET /api/products` | `ProductController`<br>`ProductCatalogTest` | **100%** |
| **TM-03** | Xem chi tiết sản phẩm | `GET /api/products/{id}` | `ProductController`<br>`ProductCatalogTest` | **100%** |
| **TM-04** | Đăng ký, đăng nhập JWT & đăng xuất | `POST /api/auth/register`<br>`POST /api/auth/login`<br>`POST /api/auth/logout` | `AuthController`<br>`AuthAndRbacTest` | **100%** |
| **TM-05** | Quản lý giỏ hàng (thêm, xem, cập nhật, xóa) | `POST /api/cart/items`<br>`GET /api/cart`<br>`PUT /api/cart/items/{id}`<br>`DELETE /api/cart/items/{id}` | `CartController`<br>`CartTest` | **100%** |
| **TM-06** | Đặt hàng thanh toán khi nhận hàng (COD) | `POST /api/orders/checkout` | `OrderController`<br>`OrderCodTest` | **100%** |
| **TM-07** | Xem lịch sử đơn hàng & hủy đơn chờ xác nhận | `GET /api/orders`<br>`GET /api/orders/{id}`<br>`PATCH /api/orders/{id}/cancel` | `OrderController`<br>`OrderCodTest` | **100%** |
| **TM-08** | Xem/cập nhật hồ sơ & đổi mật khẩu cá nhân | `GET /api/auth/me`<br>`PUT /api/auth/profile`<br>`PUT /api/auth/change-password` | `AuthController`<br>`ProfileController`<br>`AuthAndRbacTest` | **100%** |
| **TM-11** | Quản lý danh mục (Admin CRUD) | `POST /api/admin/categories`<br>`PUT /api/admin/categories/{id}`<br>`DELETE /api/admin/categories/{id}` | `CategoryController`<br>`AdminManagementTest` | **100%** |
| **TM-12** | Quản lý sản phẩm (Admin CRUD) | `POST /api/admin/products`<br>`PUT /api/admin/products/{id}`<br>`DELETE /api/admin/products/{id}` | `ProductController`<br>`AdminManagementTest` | **100%** |
| **TM-13** | Quản lý & duyệt trạng thái đơn hàng (Admin) | `GET /api/admin/orders`<br>`PATCH /api/admin/orders/{id}/status` | `AdminOrderController`<br>`AdminManagementTest` | **100%** |
| **TM-14** | Quản lý người dùng & khóa/mở khóa tài khoản | `GET /api/admin/users`<br>`PATCH /api/admin/users/{id}/status` | `AdminUserController`<br>`AuthAndRbacTest` | **100%** |

---

## 3. Kết quả kiểm thử & Tài liệu bàn giao Mốc 2

* **Kiểm thử tự động (`php artisan test`):** Toàn bộ **5 bộ Feature Test** (`AuthAndRbacTest`, `ProductCatalogTest`, `CartTest`, `OrderCodTest`, `AdminManagementTest`) chạy thông suốt (100% Pass).
* **Kiểm thử API trên Postman:** Đã đóng gói 23 request chia theo 4 thư mục nghiệp vụ vào tệp `docs/BookStore Laravel API.json`, cấu hình biến môi trường `{{base_url}}` và tự động lưu `customer_token`, `admin_token`.
* **Nhật ký sử dụng AI (`ai-logs/`):** Lưu trữ đầy đủ quá trình sử dụng AI, prompt và phần tự kiểm chứng sửa lỗi tại `ai-logs/AI-03.md` và `ai-logs/AI-04.md` (điển hình là việc phát hiện và đưa điều kiện kiểm tra `!$user->is_active` lên trước lệnh phản hồi thành công trong `AuthController@login` để chặn tài khoản bị khóa với mã lỗi `403`).