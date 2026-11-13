# Thiết kế Cơ sở dữ liệu (Chuẩn hóa 3NF)

## 1. Danh sách các bảng & Ràng buộc chính
- **`users`**: Lưu thông tin tài khoản (`id` PK, `email` UNIQUE, `password` băm Bcrypt, `role` gồm `admin`/`customer`, `is_active` mặc định `true`)[cite: 25, 30].
- **`categories`**: Danh mục sách (`id` PK, `name`, `slug` UNIQUE).
- **`products`**: Thông tin sách (`id` PK, `category_id` FK -> `categories.id`, `price` kiểu `DECIMAL(12,2)`, `stock` kiểu `INT` ràng buộc $\ge 0$).
- **`cart_items`**: Giỏ hàng người dùng (`id` PK, `user_id` FK -> `users.id`, `product_id` FK -> `products.id`, `quantity` $\ge 1$).
- **`orders`**: Đơn hàng COD (`id` PK, `user_id` FK -> `users.id`, `total_amount` kiểu `DECIMAL(12,2)`, `status` gồm `pending`, `confirmed`, `shipping`, `completed`, `cancelled`)[cite: 26, 27].
- **`order_items`**: Chi tiết từng sách trong đơn (`id` PK, `order_id` FK -> `orders.id`, `product_id` FK -> `products.id`, `quantity`, `unit_price` kiểu `DECIMAL(12,2)`)[cite: 27, 30].

## 2. Chỉ mục (Indexes) tối ưu truy vấn
- `products(name)`: Tăng tốc tìm kiếm sách theo tên từ khóa[cite: 30].
- `products(category_id)`: Tăng tốc lọc sách theo danh mục[cite: 30].
- `orders(user_id, status)`: Tăng tốc truy vấn lịch sử đơn hàng của khách và lọc đơn theo trạng thái phía Admin[cite: 30].

## 3. Lý do thiết kế nghiệp vụ
- **Tách bảng `order_items` khỏi `orders`**: Đảm bảo chuẩn hóa 3NF vì một đơn hàng có thể mua nhiều đầu sách khác nhau với số lượng riêng biệt[cite: 22, 30].
- **Lưu cột `unit_price` trong `order_items`**: Ghi nhận đúng đơn giá bán tại thời điểm khách chốt đơn; khi Admin thay đổi giá sách trên bảng `products` về sau thì doanh thu và hóa đơn lịch sử không bị sai lệch[cite: 22, 30].