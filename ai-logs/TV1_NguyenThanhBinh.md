# NHẬT KÝ SỬ DỤNG AI – TV1_NguyenThanhBinh (Kiêm nhiệm: LE, BE1, BE2, GT)

- **Sinh viên thực hiện:** Nguyễn Thành Bình – MSSV: 0306241431
- **Đề tài:** BookStore Laravel eCommerce API
- **Chi tiết 6 phiên làm việc:** Xem thêm tại AI-01.md đến AI-06.md.

## Tổng hợp các phiên sử dụng AI & Kiểm chứng
1. **AI-01 (Thiết kế CSDL 3NF):** Gợi ý lược đồ 6 bảng & Index -> Kiểm chứng bằng php artisan migrate:fresh --seed.
2. **AI-02 (Xác thực JWT & Phân quyền):** Gợi ý luồng JWT -> Tự bổ sung chặn đăng nhập khi is_active = false (HTTP 403).
3. **AI-03 (Đặt hàng COD an toàn kho):** Đề xuất DB::transaction + lockForUpdate() -> Kiểm chứng bằng OrderCodTest.
4. **AI-04 (Kiểm thử tự động & Postman):** Sinh khung 5 bộ Feature Test & 23 API Postman -> Chạy php artisan test PASS 100%.
5. **AI-05 (Bảo mật chống IDOR):** Rà soát truy cập chéo đơn hàng -> Ràng buộc truy vấn theo cặp (user_id, id).
6. **AI-06 (Tài liệu & Báo cáo):** Chuẩn hóa báo cáo mẫu Cao Thắng, slide thuyết trình & kiểm tra chéo mã nguồn.
