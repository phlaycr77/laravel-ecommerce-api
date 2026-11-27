#### Tệp 2: `docs/sequence.md` (Tách riêng và khớp 4 lớp với `architecture.jpg`)

```markdown
# Sequence Diagram – Luồng Đặt hàng COD có Transaction (`TM-06`)

```mermaid
sequenceDiagram
    actor Client as Khách hàng (Postman)
    participant Auth as Auth & JWT Middleware
    participant Ctrl as OrderController
    participant Svc as OrderService
    participant Repo as Cart / Product / Order Repository
    participant DB as MySQL Database

    Client->>Auth: POST /api/orders (Bearer JWT + Địa chỉ, SĐT)
    Auth->>Auth: Xác thực JWT & kiểm tra is_active = true
    Auth->>Ctrl: Chuyển tiếp request hợp lệ
    Ctrl->>Ctrl: Validate dữ liệu giao hàng
    Ctrl->>Svc: createOrderCOD(userId, shippingData)

    Svc->>Repo: getCartItemsByUser(userId)
    Repo->>DB: SELECT * FROM cart_items WHERE user_id = ?
    DB-->>Repo: Danh sách sản phẩm trong giỏ
    Repo-->>Svc: CartItems

    alt Giỏ hàng trống
        Svc-->>Ctrl: Ném ngoại lệ giỏ hàng trống
        Ctrl-->>Client: 400 Bad Request
    else Giỏ hàng có sản phẩm
        Svc->>DB: BEGIN TRANSACTION
        Svc->>Repo: checkAndLockStock(cartItems)
        Repo->>DB: SELECT stock FROM products WHERE id IN (...) FOR UPDATE
        DB-->>Repo: Số lượng tồn kho hiện tại

        alt Không đủ tồn kho (stock < quantity)
            Svc->>DB: ROLLBACK
            Svc-->>Ctrl: Ném ngoại lệ vượt quá tồn kho
            Ctrl-->>Client: 400 Bad Request (Sản phẩm không đủ tồn kho)
        else Đủ tồn kho
            Svc->>Repo: createOrder(orderData)
            Repo->>DB: INSERT INTO orders (status='pending', payment_method='COD')
            Svc->>Repo: createOrderItems(orderId, itemsWithUnitPrice)
            Repo->>DB: INSERT INTO order_items
            Svc->>Repo: decrementStock(cartItems)
            Repo->>DB: UPDATE products SET stock = stock - quantity
            Svc->>Repo: clearCart(userId)
            Repo->>DB: DELETE FROM cart_items WHERE user_id = ?
            Svc->>DB: COMMIT TRANSACTION
            Svc-->>Ctrl: Trả về Order đã tạo
            Ctrl-->>Client: 201 Created (Đặt hàng thành công)
        end
    end
### 3. Thao tác trên Trello & GitHub để ghi điểm ở Buổi 3

Theo quy định chấm điểm ở **Phiếu 2 và Phiếu 3**, mọi góp ý của giảng viên sau buổi bảo vệ Mốc 1 **bắt buộc phải được tạo thành thẻ trên Trello** trước khi sửa[cite: 36, 38]:

1. **Trên Trello**: Tạo một thẻ mới ở cột **`Sprint hiện tại (To do)`** với tiêu đề **`[DOC] Tách Use Case & Sequence Diagram theo góp ý Mốc 1`**, gắn nhãn **`Docs`** (Xám) và **`S2`** (Đen)[cite: 7, 13].
2. **Trên GitHub**: Tạo nhánh `docs/sua-use-case-sequence` từ `develop`, cập nhật 2 tệp `docs/use-case.md` và `docs/sequence.md` ở trên, commit với thông điệp `docs: tach use case va sequence diagram theo gop y moc 1` rồi mở Pull Request merge vào `develop`[cite: 2, 3]. Sau khi merge xong thì kéo thẻ Trello sang cột **`Hoàn thành (Done)`**[cite: 6].