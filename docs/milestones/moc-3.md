# BÁO CÁO MỐC 3 (TAG v1.0) – HOÀN THIỆN & BÀN GIAO ĐỒ ÁN

**Đề tài:** BookStore Laravel eCommerce API – Hệ thống Backend RESTful API bán sách trực tuyến  
**Sinh viên thực hiện:** Nguyễn Thành Bình – MSSV: `0306241431` – Lớp: `CĐ CNTT 24 WebD`  
**Giảng viên hướng dẫn:** ThS. Lữ Cao Tiến  
**GitHub Release:** `Tag v1.0` (Nhánh `main` – 42 commits, 15 nhánh chức năng)  

---

## 1. Tổng hợp kết quả hoàn thành chức năng (14/14 yêu cầu)

Hệ thống đã hoàn thiện 100% 12 chức năng bắt buộc (Must-have) và 2 chức năng mở rộng (Could-have) theo đúng phạm vi đề cương:

| Mã YC | Phân hệ | Nội dung chức năng | Trạng thái |
| :--- | :--- | :--- | :--- |
| **TM-01** | Guest | Xem danh sách danh mục sách & danh sách sách nổi bật (`/api/products/featured`) | Hoàn thành 100% |
| **TM-02** | Guest | Tìm kiếm sách theo tên/tác giả, lọc danh mục, sắp xếp giá & phân trang | Hoàn thành 100% |
| **TM-03** | Guest | Xem chi tiết thông tin sách (giá bán, tồn kho, mô tả) | Hoàn thành 100% |
| **TM-04** | Guest | Đăng ký tài khoản (Bcrypt) & Đăng nhập cấp phát Stateless JWT Token | Hoàn thành 100% |
| **TM-05** | Customer | Quản lý giỏ hàng (xem, thêm sách, cập nhật số lượng, xóa, tính tổng tiền) | Hoàn thành 100% |
| **TM-06** | Customer | Đặt hàng COD từ giỏ hàng trong `DB::transaction` + `lockForUpdate` | Hoàn thành 100% |
| **TM-07** | Customer | Xem lịch sử đơn hàng cá nhân, chi tiết đơn & Hủy đơn khi đang `pending` | Hoàn thành 100% |
| **TM-08** | Customer | Xem hồ sơ cá nhân (`/api/auth/me`), cập nhật họ tên & đổi mật khẩu | Hoàn thành 100% |
| **TM-09** | Guest | Lọc nâng cao sản phẩm theo khoảng giá (`min_price`, `max_price`) | Hoàn thành 100% |
| **TM-10** | Customer | Theo dõi chi tiết trạng thái xử lý đơn hàng qua từng giai đoạn | Hoàn thành 100% |
| **TM-11** | Admin | CRUD Danh mục sách (tự động chặn xóa danh mục đang có sản phẩm – HTTP 400) | Hoàn thành 100% |
| **TM-12** | Admin | CRUD Sản phẩm sách, cập nhật giá/tồn kho, tìm kiếm & phân trang server | Hoàn thành 100% |
| **TM-13** | Admin | Duyệt vòng đời trạng thái đơn hàng (`pending` -> `confirmed` -> `shipping` -> `delivered` / `cancelled`) | Hoàn thành 100% |
| **TM-14** | Admin | Quản lý người dùng: tìm kiếm theo email, phân trang & khóa/mở khóa tài khoản (`is_active`) | Hoàn thành 100% |

---

## 2. Các hạng mục hoàn thiện trọng tâm trong Mốc 3 (Sprint S4 – S5)

1. **Chuẩn hóa Global Exception Handler (`bootstrap/app.php`):**
   * Đồng nhất toàn bộ phản hồi lỗi JSON (`success: false`, `message`, `errors`) cho các mã lỗi `400 Bad Request`, `401 Unauthorized`, `403 Forbidden`, `404 Not Found` và `422 Unprocessable Entity`.
2. **Kiểm soát chặt chẽ nghiệp vụ & bảo mật:**
   * Chặn đăng nhập ngay với mã `403` nếu tài khoản có `is_active = false`.
   * Chống truy cập chéo (**IDOR**) bằng cách truy vấn bắt buộc theo cặp `(user_id, id)` lấy từ JWT Token.
   * Tự động hoàn lại tồn kho (`products.stock`) trong `DB::transaction` khi đơn hàng chuyển sang trạng thái `cancelled`, đồng thời khóa cứng chỉnh sửa đối với đơn đã `delivered` hoặc `cancelled`.
3. **Dữ liệu mẫu chuẩn hóa (`DatabaseSeeder`):**
   * Tạo sẵn dữ liệu sạch phục vụ demo trực tiếp: 1 Admin (`admin@bookstore.vn` / `Admin@123`), 10 Customers (`khachhang1@gmail.com` / `User@123`, trong đó `khachhang10@gmail.com` ở trạng thái bị khóa `is_active = false`), 5 Danh mục, 30 Đầu sách và 10 Đơn hàng mẫu.

---

## 3. Kết quả kiểm thử & Tài liệu bàn giao

* **Kiểm thử tự động (`php artisan test` – SQLite `:memory:`):** Đạt **100% PASS** trên toàn bộ 5 bộ Feature Tests (`AuthAndRbacTest`, `ProductCatalogTest`, `CartTest`, `OrderCodTest`, `AdminManagementTest`).
* **Kiểm thử tích hợp Postman:** Hoàn thiện `BookStore Laravel API.json` (23 requests chia 4 nhóm, kèm Test Script tự động lưu `customer_token` và `admin_token`) và `test-cases.md` (26 kịch bản).
* **Nhật ký ứng dụng AI:** Ghi nhận minh bạch 6 nhật ký từ `AI-01.md` đến `AI-06.md` kèm quy trình kiểm chứng độc lập.
* **Tài liệu & Video Demo:**
  * Báo cáo đồ án chuẩn mẫu Khoa CNTT – CĐ Kỹ thuật Cao Thắng (`BaoCao_DoAn_LTWeb_NguyenThanhBinh_0306241431_ChuanMau.docx`).
  * Slide bảo vệ đồ án 12 trang (`Slide_BaoVe_DoAn_LTWeb_NguyenThanhBinh_0306241431_DayDu.pptx`).
  * Video Demo trực tiếp toàn bộ luồng hệ thống: `https://youtu.be/gY1hFO4rJuA`[cite: 13].