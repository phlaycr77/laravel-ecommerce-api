# TÀI LIỆU THIẾT KẾ CƠ SỞ DỮ LIỆU (CHUẨN HÓA 3NF)

## 1. Danh sách 6 bảng và thuộc tính chi tiết (Khớp với `database/migrations/`)

### Bảng 1: `users` (Người dùng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã người dùng |
| `name` | `VARCHAR(255)` | `NOT NULL` | Họ và tên |
| `email` | `VARCHAR(255)` | `UNIQUE, NOT NULL` | Email đăng nhập |
| `email_verified_at` | `TIMESTAMP` | `NULL` | Thời gian xác thực email |
| `password` | `VARCHAR(255)` | `NOT NULL` | Mật khẩu đã băm (bcrypt) |
| `role` | `VARCHAR(255)` | `DEFAULT 'customer'` | Vai trò hệ thống (`customer` / `admin`) |
| `is_active` | `BOOLEAN` | `DEFAULT TRUE` | Trạng thái hoạt động (`false` = bị khóa) |
| `remember_token` | `VARCHAR(100)` | `NULL` | Token ghi nhớ phiên |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

### Bảng 2: `categories` (Danh mục sách)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã danh mục |
| `name` | `VARCHAR(255)` | `NOT NULL` | Tên danh mục |
| `slug` | `VARCHAR(255)` | `UNIQUE, NOT NULL` | Đường dẫn tĩnh duy nhất |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

### Bảng 3: `products` (Sản phẩm / Sách)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã sản phẩm |
| `category_id` | `BIGINT UNSIGNED` | `FK -> categories.id (CASCADE)` | Thuộc danh mục |
| `name` | `VARCHAR(255)` | `NOT NULL` | Tên sách |
| `price` | `DECIMAL(12,2)` | `NOT NULL` | Giá bán (VNĐ) |
| `stock` | `INT` | `DEFAULT 0` | Số lượng tồn kho |
| `description` | `TEXT` | `NULL` | Mô tả chi tiết sách |
| `image` | `VARCHAR(255)` | `NULL` | Đường dẫn ảnh bìa sản phẩm |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

### Bảng 4: `cart_items` (Chi tiết giỏ hàng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã mục giỏ hàng |
| `user_id` | `BIGINT UNSIGNED` | `FK -> users.id (CASCADE)` | Chủ giỏ hàng |
| `product_id` | `BIGINT UNSIGNED` | `FK -> products.id (CASCADE)` | Sách trong giỏ |
| `quantity` | `INT` | `DEFAULT 1` | Số lượng mua |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

### Bảng 5: `orders` (Đơn hàng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã đơn hàng |
| `user_id` | `BIGINT UNSIGNED` | `FK -> users.id (CASCADE)` | Khách đặt hàng |
| `total_amount` | `DECIMAL(12,2)` | `NOT NULL` | Tổng tiền thanh toán |
| `status` | `VARCHAR(255)` | `DEFAULT 'pending'` | Trạng thái đơn (`pending`, `confirmed`, `shipping`, `delivered`, `cancelled`) |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

### Bảng 6: `order_items` (Chi tiết đơn hàng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã dòng chi tiết |
| `order_id` | `BIGINT UNSIGNED` | `FK -> orders.id (CASCADE)` | Thuộc đơn hàng |
| `product_id` | `BIGINT UNSIGNED` | `FK -> products.id (CASCADE)` | Sách đã mua |
| `price` | `DECIMAL(12,2)` | `NOT NULL` | **Giá bán tại thời điểm đặt hàng** |
| `quantity` | `INT` | `NOT NULL` | Số lượng đặt |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

## 2. Giải thích thiết kế chuẩn hóa 3NF
* **Vì sao tách `order_items` và lưu cột `price`?** Một đơn hàng có nhiều đầu sách và một đầu sách nằm trong nhiều đơn hàng (quan hệ N–N giữa `orders` và `products`), do đó tách bảng trung gian `order_items` giúp loại bỏ lặp nhóm dữ liệu (đạt 1NF, 2NF, 3NF). Cột `price` trong `order_items` lưu cứng mức giá ngay tại thời điểm chốt đơn để bảo toàn lịch sử hóa đơn khi Admin thay đổi giá ở bảng `products`.