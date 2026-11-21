# ĐỀ CƯƠNG ĐỒ ÁN LẬP TRÌNH WEB
**Đề tài:** Xây dựng hệ thống Backend API Website Thương mại điện tử bán sách (BookStore)
**Sinh viên thực hiện:** Nguyễn Thành Bình (Nhóm 1 thành viên – kiêm nhiệm LE, BE1, BE2, GT)
**Giảng viên hướng dẫn:** Lữ Cao Tiến

## 1. Mục tiêu đề tài
Xây dựng hệ thống RESTful API phục vụ bán sách trực tuyến với đầy đủ quy trình từ tra cứu danh mục/sản phẩm, đăng ký/đăng nhập phân quyền JWT, quản lý giỏ hàng theo tồn kho, đặt hàng thanh toán khi nhận hàng (COD) an toàn giao dịch, đến trang quản trị dành cho Admin.

## 2. Tổ hợp công nghệ sử dụng
* **Ngôn ngữ & Framework:** PHP 8.2+, Laravel 11 (kiến trúc phân lớp: `Controllers/Api` – `Services` – `Repositories` – `Models` – `Requests` – `Middleware`).
* **Cơ sở dữ liệu:** MySQL 8.0 / MariaDB (chuẩn hóa 3NF gồm 6 bảng).
* **Xác thực & Phân quyền:** JWT (`tymon/jwt-auth`) kết hợp Middleware phân tách vai trò `customer` và `admin`.
* **Kiểm thử & Công cụ:** PHPUnit (Feature Test), Postman Collection, Git/GitHub, Trello.

## 3. Danh sách chức năng bắt buộc (User Stories)
* **Must-have (Bắt buộc):**
  * `TM-01`: Xem danh sách danh mục sách và sách nổi bật trên trang chủ.
  * `TM-02`: Tìm kiếm sách theo từ khóa, lọc theo danh mục, sắp xếp và phân trang.
  * `TM-03`: Xem chi tiết thông tin sách, giá bán và số lượng tồn kho.
  * `TM-04`: Đăng ký tài khoản (băm mật khẩu bcrypt), đăng nhập JWT, đăng xuất, chặn tài khoản bị khóa.
  * `TM-05`: Thêm sách vào giỏ hàng, cập nhật số lượng, xóa sản phẩm và tự động tính tổng tiền.
  * `TM-06`: Đặt hàng COD trong Transaction (kiểm tra tồn kho, trừ tồn kho, tạo đơn và xóa giỏ).
  * `TM-07`: Xem danh sách đơn hàng cá nhân, chi tiết đơn và hủy đơn ở trạng thái chờ xác nhận (hoàn tồn kho).
  * `TM-08`: Xem/cập nhật thông tin hồ sơ cá nhân và đổi mật khẩu.
  * `TM-11`: Quản trị danh mục sách (Thêm/Sửa/Xóa – chặn xóa danh mục đang có sản phẩm).
  * `TM-12`: Quản trị sản phẩm (Thêm/Sửa/Xóa, tìm kiếm và phân trang phía server).
  * `TM-13`: Quản trị đơn hàng (Xem danh sách, lọc theo trạng thái và cập nhật trạng thái một chiều).
  * `TM-14`: Quản trị người dùng (Tìm kiếm theo email, phân trang và khóa/mở khóa tài khoản).
* **Could-have (Mở rộng):**
  * `TM-09`, `TM-10`: Lọc nâng cao theo khoảng giá và lưu lịch sử trạng thái đơn hàng.

## 4. Rủi ro kỹ thuật & Giải pháp
* **Bán vượt tồn kho khi đặt hàng đồng thời:** Xử lý bằng `DB::transaction` kết hợp khóa dòng `lockForUpdate()` trên bảng `products` trước khi trừ tồn kho.
* **Lộ thông tin nhạy cảm / truy cập trái phép:** Không trả về `password` trong JSON response; kiểm tra quyền sở hữu `user_id` khi xem/hủy đơn và áp dụng middleware `admin` cho toàn bộ route `/api/admin/*`.