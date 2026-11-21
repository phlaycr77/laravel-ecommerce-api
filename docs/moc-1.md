# BÁO CÁO TỔNG HỢP MỐC 1 (TUẦN 1 – NGÀY 1 ĐẾN NGÀY 5)

## 1. Kiến trúc hệ thống 3 lớp (TV2)
Luồng xử lý một HTTP Request đi qua các lớp độc lập:
`Client (Postman / Frontend)` $\rightarrow$ `Routes (routes/api.php)` $\rightarrow$ `Middleware (jwt.auth, admin)` $\rightarrow$ `FormRequest (Validation)` $\rightarrow$ `Controller` $\rightarrow$ `Service (Business Logic & Transaction)` $\rightarrow$ `Repository / Eloquent Model` $\rightarrow$ `MySQL Database`.

## 2. Sơ đồ ERD (6 bảng – TV4)
```mermaid
erDiagram
    USERS ||--o{ CART_ITEMS : "has"
    USERS ||--o{ ORDERS : "places"
    CATEGORIES ||--o{ PRODUCTS : "contains"
    PRODUCTS ||--o{ CART_ITEMS : "added_to"
    PRODUCTS ||--o{ ORDER_ITEMS : "included_in"
    ORDERS ||--|{ ORDER_ITEMS : "consists_of"