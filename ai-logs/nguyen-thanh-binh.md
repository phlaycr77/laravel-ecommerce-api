# AI LOG - BRAINSTORM CHỨC NĂNG VÀ RỦI RO

## 1. Prompt đã hỏi AI
"Với website bán sách/thương mại điện tử, 3 tuần, làm 1 mình chỉ làm Backend Laravel, hãy liệt kê chức năng theo Must/Could và 3 rủi ro lớn nhất."

## 2. Kết quả gợi ý từ AI
### Danh sách chức năng:
- **Must (Bắt buộc):**
  - Đăng ký, Đăng nhập (JWT Authentication).
  - CRUD Danh mục sản phẩm & Sản phẩm (Phân trang, Tìm kiếm).
  - Quản lý Giỏ hàng (Thêm, sửa, xóa sản phẩm).
  - Đặt hàng & Quản lý trạng thái Đơn hàng.
- **Could (Mở rộng nếu kịp):**
  - Đánh giá / Phản hồi sản phẩm.
  - Mã giảm giá (Coupon).

### 3 Rủi ro lớn nhất:
1. Sai sót trong thiết kế CSDL dẫn đến việc sửa lại code logic nhiều lần.
2. Không quản lý tốt thời gian dẫn đến không kịp chuẩn bị Postman Collection chỉn chu.
3. Logic xử lý giỏ hàng và đơn hàng bị lỗi đồng bộ tồn kho (stock).

## 3. Quyết định của bản thân
- **Giữ lại:** Toàn bộ chức năng trong nhóm **Must** và 3 nhận diện rủi ro để tập trung làm tốt Postman demo.
- **Bỏ qua:** Các chức năng trong nhóm **Could** để đảm bảo tiến độ đồ án 1 mình.