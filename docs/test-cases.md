# Kịch bản kiểm thử API (Postman) – Mốc 1

| ID | Mã chức năng | Bước thực hiện | Dữ liệu đầu vào | Kết quả mong đợi | Kết quả thực tế |
| :--- | :--- | :--- | :--- | :--- | :--- |
| TC-01 | TM-03 | Gửi `POST /api/auth/register` với thông tin hợp lệ | `name`, `email` mới, `password` | HTTP `201`, lưu user với mật khẩu đã băm | Đạt |
| TC-02 | TM-03 | Gửi `POST /api/auth/register` trùng email đã có | `email: admin@bookstore.vn` | HTTP `422`, trả về lỗi validation email trùng | Đạt |
| TC-03 | TM-03 | Gửi `POST /api/auth/login` đúng tài khoản | `khachhang1@gmail.com` / `User@123` | HTTP `200`, trả về `access_token` (JWT) | Đạt |
| TC-04 | TM-04 | Gửi `POST /api/auth/login` hoặc gọi API với tài khoản bị khóa | `khachhang10@gmail.com` (`is_active=false`) | HTTP `403 Forbidden`, chặn truy cập | Đạt |
| TC-05 | TM-04 | Gọi route `/api/admin/*` không gửi kèm Bearer Token | Header `Authorization` trống | HTTP `401 Unauthorized` | Đạt |
| TC-06 | TM-04 | Dùng token của `customer` gọi vào `/api/admin/*` | Token của `khachhang1@gmail.com` | HTTP `403 Forbidden`, chặn quyền quản trị | Đạt |
| TC-07 | TM-04 | Dùng token của `admin` gọi vào `/api/admin/*` | Token của `admin@bookstore.vn` | HTTP `200 OK`, cho phép truy cập | Đạt |