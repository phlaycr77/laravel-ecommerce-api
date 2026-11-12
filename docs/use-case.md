# Đặc tả Use Case – Hệ thống Bán Sách Trực Tuyến (Backend API)

## 1. Sơ đồ Use Case tổng quát

```mermaid
flowchart LR
    Guest([Khách vãng lai])
    Customer([Khách hàng])
    Admin([Quản trị viên])

    subgraph HeThong [Hệ thống RESTful API Bán Sách COD]
        UC1(UC-01: Đăng ký / Đăng nhập & Phân quyền)
        UC2(UC-02: Xem danh sách, tìm kiếm & lọc sách)
        UC3(UC-03: Quản lý giỏ hàng)
        UC4(UC-04: Đặt hàng thanh toán COD)
        UC5(UC-05: Xem lịch sử & hủy đơn hàng)
        UC6(UC-06: Quản lý danh mục & sách)
        UC7(UC-07: Quản lý đơn hàng & tài khoản)
    end

    Guest --> UC1
    Guest --> UC2
    Customer --> UC1
    Customer --> UC2
    Customer --> UC3
    Customer --> UC4
    Customer --> UC5
    Admin --> UC1
    Admin --> UC6
    Admin --> UC7
```

## 2. Đặc tả 3 Use Case cốt lõi

### Use Case 1: Đăng nhập & Phân quyền (TM-03, TM-04)
* **Tác nhân**: Khách hàng, Quản trị viên.
* **Tiền điều kiện**: Người dùng đã có tài khoản trong cơ sở dữ liệu (`users`).
* **Luồng chính**:
  1. Tác nhân gửi request `POST /api/auth/login` kèm `email` và `password`.
  2. Hệ thống xác thực thông tin đăng nhập và kiểm tra trạng thái tài khoản (`is_active = true`).
  3. Hệ thống sinh JWT Token (`auth:api`) và trả về mã `200 OK` kèm thông tin người dùng (`role`).
  4. Khi người dùng gọi các endpoint thuộc nhóm `/api/admin/*`, `CheckAdminMiddleware` kiểm tra điều kiện `role === 'admin'` trước khi cho phép đi tiếp vào Controller.
* **Luồng ngoại lệ**:
  * Sai email hoặc mật khẩu $\rightarrow$ trả về `401 Unauthorized`.
  * Tài khoản bị khóa (`is_active = false`) $\rightarrow$ từ chối truy cập, trả về `403 Forbidden`.
  * Tài khoản khách hàng (`role = customer`) gọi API `/api/admin/*` $\rightarrow$ middleware chặn và trả về `403 Forbidden` kèm thông báo `"Không có quyền truy cập khu vực quản trị"`.

### Use Case 2: Xem danh sách, tìm kiếm & lọc sách (TM-01, TM-02)
* **Tác nhân**: Khách vãng lai, Khách hàng.
* **Tiền điều kiện**: Hệ thống có dữ liệu danh mục và sách trong CSDL.
* **Luồng chính**:
  1. Tác nhân gửi request `GET /api/books` kèm các tham số tùy chọn: `keyword`, `category_id`, `min_price`, `max_price`, `page`.
  2. Hệ thống lọc dữ liệu theo từ khóa (tên sách/tác giả), danh mục, khoảng giá và thực hiện phân trang.
  3. Hệ thống trả về mã `200 OK` cùng danh sách sách và thông tin phân trang.
* **Luồng ngoại lệ**:
  * Không tìm thấy sách phù hợp $\rightarrow$ trả về `200 OK` với mảng `data: []`.

### Use Case 3: Đặt hàng thanh toán khi nhận hàng - COD (TM-06)
* **Tác nhân**: Khách hàng.
* **Tiền điều kiện**: Khách hàng đã đăng nhập (có JWT hợp lệ) và giỏ hàng (`cart_items`) có ít nhất 1 sản phẩm.
* **Luồng chính**:
  1. Khách hàng gửi request `POST /api/orders` kèm thông tin giao hàng (`receiver_name`, `phone`, `shipping_address`, `note`).
  2. Hệ thống lấy toàn bộ sản phẩm trong giỏ hàng của khách hàng.
  3. Hệ thống mở Database Transaction (`DB::beginTransaction()`): kiểm tra số lượng tồn kho (`stock`) của từng sách.
  4. Hệ thống tạo bản ghi mới trong bảng `orders` (`payment_method = 'COD'`, `status = 'PENDING'`), lưu chi tiết vào `order_items`, trừ tồn kho tương ứng ở bảng sách và xóa giỏ hàng.
  5. Commit transaction và trả về mã `201 Created` kèm thông tin đơn hàng vừa tạo.
* **Luồng ngoại lệ**:
  * Giỏ hàng trống $\rightarrow$ trả về `400 Bad Request`.
  * Số lượng đặt mua vượt quá tồn kho hiện tại $\rightarrow$ Rollback transaction và trả về `400 Bad Request` thông báo sản phẩm không đủ số lượng.

## 3. Sequence Diagram – Luồng Đặt hàng (COD)

```mermaid
sequenceDiagram
    actor Client as Khách hàng (Postman)
    participant Auth as Auth & JWT Middleware
    participant Ctrl as OrderController
    participant Svc as OrderService
    participant DB as MySQL Database

    Client->>Auth: POST /api/orders (Bearer Token + Địa chỉ nhận)
    Auth->>Auth: Xác thực JWT & trạng thái is_active
    Auth->>Ctrl: Chuyển tiếp request (kèm user)
    Ctrl->>Svc: createOrderCOD(user, shippingData)
    
    Svc->>DB: Lấy danh sách sản phẩm trong cart_items
    alt Giỏ hàng trống
        DB-->>Svc: Danh sách rỗng
        Svc-->>Ctrl: Ném ngoại lệ giỏ hàng trống
        Ctrl-->>Client: 400 Bad Request
    else Giỏ hàng có sản phẩm
        Svc->>DB: BEGIN TRANSACTION
        Svc->>DB: Kiểm tra tồn kho (stock) từng sách
        alt Không đủ tồn kho
            DB-->>Svc: stock < quantity
            Svc->>DB: ROLLBACK
            Svc-->>Ctrl: Ném ngoại lệ hết hàng
            Ctrl-->>Client: 400 Bad Request (Sách không đủ tồn kho)
        else Đủ tồn kho
            Svc->>DB: INSERT INTO orders (status='PENDING', payment_method='COD')
            Svc->>DB: INSERT INTO order_items
            Svc->>DB: UPDATE books/products SET stock = stock - quantity
            Svc->>DB: DELETE FROM cart_items WHERE user_id = ?
            Svc->>DB: COMMIT TRANSACTION
            DB-->>Svc: Trả về dữ liệu đơn hàng
            Svc-->>Ctrl: Order Resource
            Ctrl-->>Client: 201 Created (Đặt hàng thành công)
        end
    end
```