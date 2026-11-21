# TÀI LIỆU THIẾT KẾ CƠ SỞ DỮ LIỆU (CHUẨN HÓA 3NF)

## 1. Danh sách 6 bảng và thuộc tính chi tiết

### Bảng 1: `users` (Người dùng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã người dùng |
| `name` | `VARCHAR(255)` | `NOT NULL` | Họ và tên |
| `email` | `VARCHAR(255)` | `UNIQUE, NOT NULL` | Email đăng nhập |
| `password` | `VARCHAR(255)` | `NOT NULL` | Mật khẩu đã băm (bcrypt) |
| `phone` | `VARCHAR(20)` | `NULL` | Số điện thoại |
| `address` | `TEXT` | `NULL` | Địa chỉ giao hàng mặc định |
| `role` | `ENUM('customer','admin')` | `DEFAULT 'customer'` | Vai trò hệ thống |
| `is_active` | `BOOLEAN` | `DEFAULT TRUE` | Trạng thái hoạt động (`false` = bị khóa) |
| `created_at`, `updated_at` | `TIMESTAMP` | `NULL` | Thời gian tạo/cập nhật |

### Bảng 2: `categories` (Danh mục sách)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã danh mục |
| `name` | `VARCHAR(255)` | `UNIQUE, NOT NULL` | Tên danh mục |
| `slug` | `VARCHAR(255)` | `UNIQUE, NOT NULL` | Đường dẫn tĩnh |
| `description` | `TEXT` | `NULL` | Mô tả danh mục |

### Bảng 3: `products` (Sản phẩm / Sách)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã sản phẩm |
| `category_id` | `BIGINT UNSIGNED` | `FK -> categories.id` | Thuộc danh mục |
| `name` | `VARCHAR(255)` | `NOT NULL, INDEX` | Tên sách |
| `slug` | `VARCHAR(255)` | `UNIQUE, NOT NULL` | Đường dẫn tĩnh |
| `author` | `VARCHAR(255)` | `NULL` | Tác giả |
| `price` | `DECIMAL(12,2)` | `NOT NULL` | Giá bán (VNĐ) |
| `stock` | `INT UNSIGNED` | `DEFAULT 0` | Số lượng tồn kho ($\ge 0$) |
| `image_url` | `VARCHAR(500)` | `NULL` | Đường dẫn ảnh bìa |
| `description` | `TEXT` | `NULL` | Mô tả chi tiết sách |
| `is_featured` | `BOOLEAN` | `DEFAULT FALSE` | Đánh dấu sách nổi bật |

### Bảng 4: `cart_items` (Chi tiết giỏ hàng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã mục giỏ hàng |
| `user_id` | `BIGINT UNSIGNED` | `FK -> users.id (CASCADE)` | Chủ giỏ hàng |
| `product_id` | `BIGINT UNSIGNED` | `FK -> products.id (CASCADE)` | Sách trong giỏ |
| `quantity` | `INT UNSIGNED` | `NOT NULL` | Số lượng mua |

### Bảng 5: `orders` (Đơn hàng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã đơn hàng |
| `user_id` | `BIGINT UNSIGNED` | `FK -> users.id` | Khách đặt hàng |
| `receiver_name` | `VARCHAR(255)` | `NOT NULL` | Tên người nhận |
| `receiver_phone` | `VARCHAR(20)` | `NOT NULL` | SĐT người nhận |
| `shipping_address` | `TEXT` | `NOT NULL` | Địa chỉ giao hàng |
| `total_amount` | `DECIMAL(12,2)` | `NOT NULL` | Tổng tiền thanh toán |
| `payment_method` | `VARCHAR(50)` | `DEFAULT 'COD'` | Phương thức thanh toán |
| `status` | `ENUM(...)` | `DEFAULT 'pending'` | `pending`, `confirmed`, `shipping`, `completed`, `cancelled` |

### Bảng 6: `order_items` (Chi tiết đơn hàng)
| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `PK, AUTO_INCREMENT` | Mã dòng chi tiết |
| `order_id` | `BIGINT UNSIGNED` | `FK -> orders.id (CASCADE)` | Thuộc đơn hàng |
| `product_id` | `BIGINT UNSIGNED` | `FK -> products.id` | Sách đã mua |
| `quantity` | `INT UNSIGNED` | `NOT NULL` | Số lượng đặt |
| `unit_price` | `DECIMAL(12,2)` | `NOT NULL` | **Giá bán tại thời điểm đặt hàng** |

## 2. Chỉ mục (Indexes) & Giải thích thiết kế
* **Chỉ mục tối ưu truy vấn:** `products(name)`, `products(category_id)`, `orders(user_id, status)`.
* **Vì sao tách `order_items` và lưu `unit_price`?** Một đơn hàng có nhiều đầu sách (quan hệ N–N giữa `orders` và `products`), do đó phải tách bảng trung gian `order_items` để đạt chuẩn 3NF. Cột `unit_price` lưu giá cố định lúc khách chốt đơn nhằm đảm bảo lịch sử hóa đơn không bị sai lệch khi Admin thay đổi giá sách về sau.