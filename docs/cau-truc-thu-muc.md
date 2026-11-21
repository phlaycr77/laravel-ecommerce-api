# CẤU TRÚC THƯ MỤC DỰ ÁN

* `app/Http/Controllers/Api/`: Tiếp nhận HTTP Request, gọi Service và trả về JSON Response chuẩn hóa.
* `app/Services/`: Chứa toàn bộ nghiệp vụ cốt lõi (`AuthService`, `ProductService`, `CartService`, `OrderService`, `AdminService`) và xử lý `DB::transaction`.
* `app/Repositories/`: Đóng gói các truy vấn Eloquent tới CSDL.
* `app/Models/`: Định nghĩa 6 thực thể (`User`, `Category`, `Product`, `CartItem`, `Order`, `OrderItem`) và các quan hệ `hasMany`/`belongsTo`.
* `app/Http/Requests/`: Kiểm tra tính hợp lệ dữ liệu đầu vào (FormRequest Validation).
* `app/Http/Middleware/`: Kiểm tra xác thực JWT và phân quyền `admin`.
* `database/migrations/` & `database/seeders/`: Khởi tạo lược đồ 6 bảng và dữ liệu mẫu ban đầu.
* `tests/Feature/`: Bộ kiểm thử tự động PHPUnit cho toàn bộ API.
* `docs/` & `ai-logs/`: Tài liệu thiết kế, Postman Collection, test cases và nhật ký sử dụng AI.