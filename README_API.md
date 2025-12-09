# Hệ Thống Quản Lý Nhà Hàng - API Documentation

## Tổng Quan

Hệ thống quản lý nhà hàng với 3 nhóm người dùng:
- **Khách hàng (Customer)**: Đặt bàn, đặt món, xem thông báo
- **Nhân viên (Staff)**: Xử lý đặt bàn, đơn hàng, thanh toán
- **Quản trị viên (Admin)**: Quản lý menu, bàn, nhân viên, báo cáo

## Cài Đặt

1. Cài đặt dependencies:
```bash
composer install
npm install
```

2. Cấu hình môi trường:
```bash
cp .env.example .env
php artisan key:generate
```

3. Chạy migrations:
```bash
php artisan migrate
```

4. Khởi chạy server:
```bash
php artisan serve
```

## Authentication

Hệ thống sử dụng Laravel Sanctum cho API authentication.

### Đăng ký
```
POST /api/register
Body: {
    "name": "Tên người dùng",
    "email": "email@example.com",
    "password": "password123",
    "phone": "0123456789"
}
```

### Đăng nhập
```
POST /api/login
Body: {
    "email": "email@example.com",
    "password": "password123"
}
Response: {
    "user": {...},
    "token": "token_string"
}
```

### Sử dụng Token
Thêm header vào các request:
```
Authorization: Bearer {token}
```

## API Endpoints

### 1. Khách Hàng (Customer)

#### Menu
- `GET /api/menu` - Xem danh sách món ăn
- `GET /api/menu/{id}` - Xem chi tiết món ăn
- `GET /api/categories` - Xem danh sách danh mục

#### Đặt Bàn
- `POST /api/customer/bookings` - Đặt bàn
  ```json
  {
    "customer_name": "Nguyễn Văn A",
    "customer_phone": "0123456789",
    "booking_date": "2024-12-10",
    "booking_time": "18:00",
    "number_of_guests": 4,
    "location_preference": "Gần cửa sổ",
    "notes": "Có trẻ em"
  }
  ```
- `GET /api/customer/bookings` - Lịch sử đặt bàn
- `GET /api/customer/bookings/{id}` - Chi tiết đặt bàn

#### Đặt Món
- `POST /api/customer/orders` - Đặt món
  ```json
  {
    "order_type": "dine_in|takeaway|delivery",
    "items": [
      {
        "menu_item_id": 1,
        "quantity": 2,
        "notes": "Không cay"
      }
    ],
    "table_id": 1,
    "customer_name": "Nguyễn Văn A",
    "customer_phone": "0123456789",
    "customer_address": "123 Đường ABC",
    "voucher_code": "DISCOUNT10",
    "notes": "Ghi chú"
  }
  ```
- `GET /api/customer/orders` - Lịch sử đơn hàng
- `GET /api/customer/orders/{id}` - Chi tiết đơn hàng

#### Thông Báo
- `GET /api/customer/notifications` - Danh sách thông báo
- `GET /api/customer/notifications/unread` - Thông báo chưa đọc
- `PUT /api/customer/notifications/{id}/read` - Đánh dấu đã đọc
- `PUT /api/customer/notifications/read-all` - Đánh dấu tất cả đã đọc

### 2. Nhân Viên (Staff)

Yêu cầu: Token với role `staff`, `cashier`, `kitchen_manager`, hoặc `admin`

#### Quản Lý Đặt Bàn
- `GET /api/staff/bookings` - Danh sách đặt bàn
- `GET /api/staff/bookings/{id}` - Chi tiết đặt bàn
- `POST /api/staff/bookings/{id}/confirm` - Xác nhận đặt bàn
  ```json
  {
    "table_id": 1
  }
  ```
- `POST /api/staff/bookings/{id}/reject` - Từ chối đặt bàn
- `POST /api/staff/bookings/{id}/check-in` - Check-in khách

#### Quản Lý Đơn Hàng
- `GET /api/staff/orders` - Danh sách đơn hàng
- `GET /api/staff/orders/{id}` - Chi tiết đơn hàng
- `PUT /api/staff/orders/{id}/status` - Cập nhật trạng thái
  ```json
  {
    "status": "processing|preparing|ready|served|delivered|cancelled"
  }
  ```

#### Thanh Toán
- `POST /api/staff/payments` - Xử lý thanh toán
  ```json
  {
    "order_id": 1,
    "payment_method": "cash|bank_transfer|momo|vnpay|bank_card",
    "amount": 500000,
    "transaction_id": "TXN123456",
    "notes": "Ghi chú"
  }
  ```
- `GET /api/staff/payments` - Danh sách thanh toán
- `GET /api/staff/payments/{id}` - Chi tiết thanh toán

### 3. Quản Trị Viên (Admin)

Yêu cầu: Token với role `admin`

#### Quản Lý Menu
- `GET /api/admin/menu` - Danh sách món ăn
- `POST /api/admin/menu` - Thêm món ăn
- `GET /api/admin/menu/{id}` - Chi tiết món ăn
- `PUT /api/admin/menu/{id}` - Cập nhật món ăn
- `DELETE /api/admin/menu/{id}` - Xóa món ăn
- `PUT /api/admin/menu/{id}/toggle-status` - Ẩn/hiện món

#### Quản Lý Danh Mục
- `GET /api/admin/categories` - Danh sách danh mục
- `POST /api/admin/categories` - Thêm danh mục
- `PUT /api/admin/categories/{id}` - Cập nhật danh mục
- `DELETE /api/admin/categories/{id}` - Xóa danh mục

#### Quản Lý Bàn
- `GET /api/admin/tables` - Danh sách bàn
- `POST /api/admin/tables` - Thêm bàn
- `GET /api/admin/tables/{id}` - Chi tiết bàn
- `PUT /api/admin/tables/{id}` - Cập nhật bàn
- `DELETE /api/admin/tables/{id}` - Xóa bàn
- `PUT /api/admin/tables/{id}/status` - Cập nhật trạng thái bàn

#### Quản Lý Nhân Viên
- `GET /api/admin/staff` - Danh sách nhân viên
- `POST /api/admin/staff` - Tạo tài khoản nhân viên
- `GET /api/admin/staff/{id}` - Chi tiết nhân viên
- `PUT /api/admin/staff/{id}` - Cập nhật nhân viên
- `DELETE /api/admin/staff/{id}` - Xóa nhân viên
- `POST /api/admin/staff/{id}/reset-password` - Đặt lại mật khẩu

#### Báo Cáo & Thống Kê
- `GET /api/admin/reports/revenue?period=today|week|month|year` - Doanh thu
- `GET /api/admin/reports/orders?date=2024-12-10` - Danh sách đơn hàng
- `GET /api/admin/reports/popular-items?limit=10` - Món bán chạy
- `GET /api/admin/reports/table-revenue` - Doanh thu theo bàn
- `GET /api/admin/reports/statistics?start_date=2024-12-01&end_date=2024-12-31` - Thống kê tổng quan

## Trạng Thái Đơn Hàng

- `pending` - Đang chờ xử lý
- `processing` - Đang xử lý
- `preparing` - Đang chế biến
- `ready` - Đã sẵn sàng
- `served` - Đã phục vụ
- `delivered` - Đã giao hàng
- `cancelled` - Đã hủy

## Trạng Thái Đặt Bàn

- `pending` - Đang chờ xác nhận
- `confirmed` - Đã xác nhận
- `rejected` - Đã từ chối
- `checked_in` - Đã check-in
- `completed` - Đã hoàn thành
- `cancelled` - Đã hủy

## Trạng Thái Bàn

- `available` - Trống
- `reserved` - Đã đặt trước
- `occupied` - Đang phục vụ
- `maintenance` - Bảo trì

## Phương Thức Thanh Toán

- `cash` - Tiền mặt
- `bank_transfer` - Chuyển khoản
- `momo` - Momo
- `vnpay` - VNPay
- `bank_card` - Thẻ ngân hàng

## Vai Trò Người Dùng

- `customer` - Khách hàng
- `staff` - Nhân viên phục vụ
- `cashier` - Thu ngân
- `kitchen_manager` - Quản lý bếp
- `admin` - Quản trị viên

## Lưu Ý

1. Tất cả các endpoint yêu cầu authentication (trừ đăng ký, đăng nhập, và xem menu)
2. Staff endpoints yêu cầu role là staff, cashier, kitchen_manager, hoặc admin
3. Admin endpoints yêu cầu role là admin
4. Thời gian sử dụng định dạng `H:i` (ví dụ: "18:00")
5. Ngày tháng sử dụng định dạng `Y-m-d` (ví dụ: "2024-12-10")

