# TÀI LIỆU KỊCH BẢN KIỂM THỬ (TEST CASES) – MỐC 2

* **Dự án:** BookStore Laravel eCommerce API
* **Công cụ kiểm thử:** PHPUnit / Laravel Feature Test (`php artisan test`) & Postman Collection v2.1 (`docs/BookStore Laravel API.json`)
* **Base URL mặc định:** `http://127.0.0.1:8000/api`

---

## 1. Nhóm kiểm thử Xác thực, Hồ sơ & Phân quyền RBAC (`AuthAndRbacTest.php`)

| Mã TC | Tên kịch bản kiểm thử | API Endpoint | Dữ liệu / Điều kiện đầu vào | Kết quả mong đợi | Trạng thái |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-01** | Đăng ký tài khoản mới hợp lệ | `POST /auth/register` | Email chưa tồn tại, mật khẩu $\ge 8$ ký tự | HTTP `201`, tạo user vai trò `customer`, mật khẩu đã băm | Pass |
| **TC-02** | Đăng ký trùng email đã tồn tại | `POST /auth/register` | Email `khachdemo@gmail.com` đã có trong DB | HTTP `422`, trả về thông báo lỗi validate email | Pass |
| **TC-03** | Đăng nhập thành công (Customer & Admin) | `POST /auth/login` | Đúng email, đúng mật khẩu, `is_active = true` | HTTP `200`, trả về `access_token` (JWT) và thông tin user | Pass |
| **TC-04** | Đăng nhập sai mật khẩu | `POST /auth/login` | Đúng email, sai mật khẩu | HTTP `401`, báo *"Email hoặc mật khẩu không chính xác"* | Pass |
| **TC-05** | Chặn đăng nhập khi tài khoản bị khóa | `POST /auth/login` | Tài khoản đã bị Admin cập nhật `is_active = false` | HTTP `403`, báo *"Tài khoản của bạn đã bị khóa."* | Pass |
| **TC-06** | Xem & cập nhật hồ sơ cá nhân | `GET /auth/me`<br>`PUT /auth/profile` | Token hợp lệ, cập nhật họ tên, SĐT, địa chỉ | HTTP `200`, dữ liệu hồ sơ được cập nhật chính xác | Pass |
| **TC-07** | Đổi mật khẩu cá nhân | `PUT /auth/change-password` | Nhập đúng mật khẩu cũ, mật khẩu mới $\ge 8$ ký tự | HTTP `200`, đăng nhập được bằng mật khẩu mới | Pass |
| **TC-08** | Customer truy cập API của Admin | `GET /admin/users` | Sử dụng `customer_token` | HTTP `403 Forbidden`, từ chối truy cập | Pass |

---

## 2. Nhóm kiểm thử Danh mục & Sản phẩm công khai (`ProductCatalogTest.php`)

| Mã TC | Tên kịch bản kiểm thử | API Endpoint | Dữ liệu / Điều kiện đầu vào | Kết quả mong đợi | Trạng thái |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-09** | Lấy danh sách danh mục công khai | `GET /categories` | Không cần token | HTTP `200`, trả về danh sách danh mục sách | Pass |
| **TC-10** | Lấy danh sách sản phẩm nổi bật | `GET /products/featured` | Không cần token | HTTP `200`, trả về danh sách sản phẩm nổi bật | Pass |
| **TC-11** | Tìm kiếm & lọc sản phẩm phân trang | `GET /products?keyword=...&category_id=...` | Từ khóa tên sách hoặc ID danh mục | HTTP `200`, trả về kết quả lọc kèm thông tin phân trang | Pass |
| **TC-12** | Xem chi tiết sản phẩm tồn tại / không tồn tại | `GET /products/{id}` | ID hợp lệ và ID không có trong DB | HTTP `200` (nếu có) / HTTP `404` (nếu không tồn tại) | Pass |

---

## 3. Nhóm kiểm thử Giỏ hàng (`CartTest.php`)

| Mã TC | Tên kịch bản kiểm thử | API Endpoint | Dữ liệu / Điều kiện đầu vào | Kết quả mong đợi | Trạng thái |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-13** | Thêm sản phẩm hợp lệ vào giỏ hàng | `POST /cart/items` | `customer_token`, `product_id`, `quantity` $\le$ tồn kho | HTTP `200`/`201`, tạo mục giỏ hàng hoặc cộng dồn số lượng | Pass |
| **TC-14** | Thêm sản phẩm vượt quá số lượng tồn kho | `POST /cart/items` | `customer_token`, `quantity = 9999` ($>$ `stock`) | HTTP `400`/`422`, chặn thao tác và báo vượt tồn kho | Pass |
| **TC-15** | Xem giỏ hàng & tính tổng tiền | `GET /cart` | `customer_token` | HTTP `200`, hiển thị chi tiết sản phẩm và tổng tiền chính xác | Pass |
| **TC-16** | Cập nhật số lượng sản phẩm trong giỏ | `PUT /cart/items/{id}` | `customer_token`, cập nhật số lượng hợp lệ / vượt kho | HTTP `200` khi hợp lệ; báo lỗi khi vượt tồn kho | Pass |

---

## 4. Nhóm kiểm thử Đặt hàng COD & Đơn hàng khách hàng (`OrderCodTest.php`)

| Mã TC | Tên kịch bản kiểm thử | API Endpoint | Dữ liệu / Điều kiện đầu vào | Kết quả mong đợi | Trạng thái |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-17** | Đặt hàng COD thành công | `POST /orders/checkout` | Giỏ hàng có sản phẩm, đủ thông tin người nhận | HTTP `201`, tạo đơn `pending`, trừ tồn kho, làm rỗng giỏ | Pass |
| **TC-18** | Đặt hàng thất bại do giỏ rỗng hoặc hết hàng | `POST /orders/checkout` | Giỏ hàng trống hoặc tồn kho không đủ lúc thanh toán | HTTP `400`/`422`, rollback transaction, không trừ sai kho | Pass |
| **TC-19** | Xem danh sách đơn hàng của bản thân | `GET /orders` | `customer_token` | HTTP `200`, chỉ trả về đơn hàng thuộc về user hiện tại | Pass |
| **TC-20** | Hủy đơn hàng đang ở trạng thái chờ xác nhận | `PATCH /orders/{id}/cancel` | Đơn của user có trạng thái `pending` | HTTP `200`, chuyển trạng thái `cancelled` và hoàn lại tồn kho | Pass |
| **TC-21** | Chặn xem hoặc hủy đơn hàng của người khác | `GET /orders/{other_id}` | `customer_token` của user A truy cập đơn của user B | HTTP `403` hoặc `404`, bảo vệ dữ liệu riêng tư | Pass |

---

## 5. Nhóm kiểm thử Quản trị Admin (`AdminManagementTest.php`)

| Mã TC | Tên kịch bản kiểm thử | API Endpoint | Dữ liệu / Điều kiện đầu vào | Kết quả mong đợi | Trạng thái |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **TC-22** | Tạo danh mục mới & chặn trùng tên | `POST /admin/categories` | `admin_token`, tên danh mục mới / tên đã tồn tại | HTTP `201` khi hợp lệ; HTTP `422` nếu trùng tên | Pass |
| **TC-23** | Chặn xóa danh mục đang chứa sản phẩm | `DELETE /admin/categories/{id}` | Danh mục đang có sản phẩm liên kết | HTTP `400`/`422`, từ chối xóa để bảo toàn toàn vẹn dữ liệu | Pass |
| **TC-24** | Tạo sản phẩm mới với dữ liệu hợp lệ / sai | `POST /admin/products` | `admin_token`, kiểm tra `price > 0`, `stock >= 0` | HTTP `201` khi hợp lệ; HTTP `422` khi giá/tồn kho âm | Pass |
| **TC-25** | Admin xem & duyệt trạng thái đơn hàng | `GET /admin/orders`<br>`PATCH /admin/orders/{id}/status` | `admin_token`, duyệt đơn từ `pending` -> `confirmed` | HTTP `200`, cập nhật trạng thái đơn hàng thành công | Pass |
| **TC-26** | Admin tìm kiếm & khóa/mở khóa tài khoản user | `GET /admin/users`<br>`PATCH /admin/users/{id}/status` | `admin_token`, gửi `{"is_active": false}` hoặc `true` | HTTP `200`, cập nhật đúng trạng thái hoạt động của user | Pass |

---

## 6. Lệnh thực thi kiểm thử tự động

Chạy toàn bộ bộ kiểm thử tự động trong thư mục gốc dự án bằng lệnh:

```bash
php artisan test
```