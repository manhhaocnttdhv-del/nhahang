# Nghiệp Vụ Đặt Bàn & Đặt Món

## 1. LUỒNG ĐẶT BÀN

### 1.1. Khách hàng đặt bàn
- **Bước 1:** Khách chọn bàn trên sơ đồ (tùy chọn)
- **Bước 2:** Điền thông tin: tên, SĐT, ngày, giờ, số khách, vị trí (auto từ bàn), ghi chú
- **Bước 3:** Submit → Tạo booking với status `pending`
- **Validation:**
  - Số khách phải ≤ capacity của bàn (nếu chọn bàn)
  - Ngày đặt phải >= hôm nay
  - Giờ đặt phải hợp lý (VD: 8h-22h)

### 1.2. Staff xử lý đặt bàn
- **Xem danh sách:** Lọc theo ngày, status
- **Xác nhận (Confirm):**
  - Chọn bàn phù hợp (hoặc để tự động)
  - Gán `table_id` → table status = `reserved`
  - Booking status = `confirmed`
  - Gửi notification cho khách
- **Từ chối (Reject):**
  - Booking status = `rejected`
  - Gửi notification cho khách
  - Nếu có pre-order → hủy order

### 1.3. Check-in
- Chỉ khi booking status = `confirmed` và có `table_id`
- Booking status = `checked_in`
- Table status = `occupied`
- Khách có thể đặt món tại bàn

### 1.4. Hoàn thành
- Khi thanh toán xong tất cả orders
- Booking status = `completed`
- Table status = `available` (nếu không có booking khác)

---

## 2. LUỒNG ĐẶT MÓN

### 2.1. Pre-order (Đặt trước)
- **Khi nào:** Sau khi đặt bàn (pending hoặc confirmed)
- **Cách:** Vào booking detail → "Chọn Món Ngay"
- **Xử lý:**
  - Tạo order với `booking_id`
  - Order status = `pending`
  - Nếu booking chưa confirm → order chờ xử lý
  - Khi booking được confirm → order có thể xử lý

### 2.2. Đặt món tại quán
- **Khi nào:** Sau khi check-in (booking status = `checked_in`)
- **Cách:** Vào booking detail → "Chọn Món"
- **Xử lý:**
  - Tạo order với `booking_id` và `table_id`
  - Order status = `pending`
  - Staff xử lý ngay

### 2.3. Đặt món không có bàn (takeaway/delivery)
- **Cách:** Vào Menu → Đặt Món
- **Xử lý:**
  - Tạo order không có `booking_id`
  - Order type = `takeaway` hoặc `delivery`
  - Xử lý bình thường

---

## 3. LOGIC XỬ LÝ

### 3.1. Tự động gán bàn
```php
// Khi confirm booking, nếu không chọn bàn:
1. Tìm bàn phù hợp:
   - Status = available
   - Capacity >= number_of_guests
   - Area match location_preference (nếu có)
   - Không có booking conflict (cùng ngày, giờ)
2. Gán bàn đầu tiên phù hợp
3. Nếu không có → để staff chọn thủ công
```

### 3.2. Validation số khách
```php
// Khi đặt bàn:
- Nếu chọn bàn: số khách ≤ capacity
- Nếu không chọn: số khách ≤ max capacity (VD: 20)
```

### 3.3. Xử lý Order theo Booking Status
```php
// Pre-order khi booking pending:
- Order status = pending
- Chưa xử lý (chờ booking confirm)

// Pre-order khi booking confirmed:
- Order status = pending
- Có thể xử lý ngay

// Order khi booking checked_in:
- Order status = pending
- Ưu tiên xử lý
```

### 3.4. Hoàn thành Booking
```php
// Khi tất cả orders đã thanh toán:
1. Check tất cả orders của booking
2. Nếu tất cả đã paid → booking status = completed
3. Table status = available (nếu không có booking tiếp theo)
```

---

## 4. TRẠNG THÁI (STATUS)

### Booking Status:
- `pending`: Chờ xác nhận
- `confirmed`: Đã xác nhận, đã gán bàn
- `checked_in`: Khách đã đến
- `completed`: Đã hoàn thành (thanh toán xong)
- `rejected`: Bị từ chối
- `cancelled`: Khách hủy

### Table Status:
- `available`: Trống
- `reserved`: Đã đặt (có booking confirmed)
- `occupied`: Đang dùng (đã check-in)
- `maintenance`: Bảo trì

### Order Status:
- `pending`: Chờ xử lý
- `processing`: Đang xử lý
- `preparing`: Đang chế biến
- `ready`: Sẵn sàng
- `served`: Đã phục vụ (dine_in)
- `delivered`: Đã giao (delivery)
- `cancelled`: Đã hủy

---

## 5. CẢI THIỆN ĐỀ XUẤT

1. **Tự động gán bàn:** Gợi ý bàn phù hợp khi confirm
2. **Validation tốt hơn:** Check conflict thời gian
3. **Pre-order logic:** Chỉ xử lý khi booking confirm
4. **Auto complete:** Tự động complete booking khi thanh toán xong
5. **Notification:** Thông báo real-time cho khách
6. **QR Code:** Tạo QR code cho booking để check-in nhanh

