# Đặc tả Use Case – Hệ thống Bán Sách Trực Tuyến (Backend API)

## 1. Sơ đồ Use Case tổng quát

```mermaid
flowchart LR
    Guest([Khách vãng lai])
    Customer([Khách hàng])
    Admin([Quản trị viên])

    subgraph HeThong [Hệ thống RESTful API Bán Sách COD]
        UC01(UC-01: Đăng ký tài khoản)
        UC02(UC-02: Xem danh sách & chi tiết sách)
        UC03(UC-03: Tìm kiếm & lọc sách)
        UC04(UC-04: Đăng nhập)
        UC05(UC-05: Đăng xuất)
        UC06(UC-06: Quản lý giỏ hàng)
        UC07(UC-07: Đặt hàng thanh toán COD)
        UC08(UC-08: Xem lịch sử đơn hàng)
        UC09(UC-09: Hủy đơn hàng)
        UC10(UC-10: Quản lý danh mục)
        UC11(UC-11: Quản lý sản phẩm)
        UC12(UC-12: Quản lý đơn hàng)
        UC13(UC-13: Quản lý người dùng)
    end

    Guest --> UC01
    Guest --> UC02
    Guest --> UC03

    Customer --> UC02
    Customer --> UC03
    Customer --> UC04
    Customer --> UC05
    Customer --> UC06
    Customer --> UC07
    Customer --> UC08
    Customer --> UC09

    Admin --> UC04
    Admin --> UC05
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13