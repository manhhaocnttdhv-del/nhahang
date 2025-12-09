# Đề Xuất Tính Năng Bổ Sung Cho User (Khách Hàng)

## Tính Năng Hiện Có
1. ✅ Xem menu
2. ✅ Đặt bàn
3. ✅ Đặt món
4. ✅ Xem lịch sử đặt bàn
5. ✅ Xem lịch sử đơn hàng
6. ✅ Thông báo (có API, chưa có web view)

## Đề Xuất Tính Năng Mới

### 1. **Quản Lý Tài Khoản** ⭐ QUAN TRỌNG
- Xem thông tin cá nhân
- Cập nhật thông tin (tên, email, SĐT)
- Đổi mật khẩu
- Upload avatar

**Lợi ích:** User có thể tự quản lý thông tin, không cần liên hệ admin

---

### 2. **Xem Thông Báo** ⭐ QUAN TRỌNG
- Danh sách thông báo
- Đánh dấu đã đọc
- Xóa thông báo
- Thông báo real-time (WebSocket hoặc polling)

**Lợi ích:** User biết được trạng thái đặt bàn, đơn hàng ngay lập tức

---

### 3. **Sử Dụng Voucher/Khuyến Mãi** ⭐ QUAN TRỌNG
- Xem danh sách voucher khả dụng
- Áp dụng voucher khi đặt món
- Lịch sử sử dụng voucher
- Mã giảm giá tự động

**Lợi ích:** Tăng doanh thu, khuyến khích user quay lại

---

### 4. **Hủy Đặt Bàn**
- Hủy đặt bàn (nếu chưa được confirm)
- Xem lý do hủy
- Thông báo cho staff

**Lợi ích:** User có thể tự hủy, giảm công việc cho staff

---

### 5. **Hủy Đơn Hàng**
- Hủy đơn hàng (nếu status = pending)
- Xem lý do hủy
- Hoàn tiền (nếu đã thanh toán)

**Lợi ích:** User có thể tự hủy, giảm công việc cho staff

---

### 6. **Xem Chi Tiết Đơn Hàng**
- Chi tiết từng món
- Trạng thái đơn hàng real-time
- Thời gian ước tính
- Mã QR để theo dõi

**Lợi ích:** User biết được đơn hàng đang ở đâu

---

### 7. **Theo Dõi Đơn Hàng Real-time**
- Cập nhật trạng thái tự động
- Thông báo khi đơn sẵn sàng
- Bản đồ theo dõi (nếu delivery)

**Lợi ích:** Trải nghiệm tốt hơn, giảm câu hỏi cho staff

---

### 8. **Đánh Giá Món Ăn** (Tùy chọn)
- Đánh giá sao (1-5)
- Viết review
- Upload ảnh món ăn
- Xem review của người khác

**Lợi ích:** Tăng tương tác, giúp user khác chọn món

---

### 9. **Yêu Thích Món Ăn** (Tùy chọn)
- Lưu món yêu thích
- Xem danh sách món yêu thích
- Đặt lại món yêu thích nhanh

**Lợi ích:** Tăng trải nghiệm, user đặt món nhanh hơn

---

### 10. **Địa Chỉ Giao Hàng** (Tùy chọn)
- Lưu nhiều địa chỉ
- Địa chỉ mặc định
- Chỉnh sửa/xóa địa chỉ

**Lợi ích:** User không cần nhập lại địa chỉ mỗi lần

---

## Ưu Tiên Triển Khai

### Phase 1 (Quan trọng nhất):
1. Quản lý tài khoản
2. Xem thông báo
3. Sử dụng voucher

### Phase 2 (Cải thiện trải nghiệm):
4. Hủy đặt bàn
5. Hủy đơn hàng
6. Xem chi tiết đơn hàng
7. Theo dõi đơn hàng real-time

### Phase 3 (Tùy chọn):
8. Đánh giá món ăn
9. Yêu thích món ăn
10. Địa chỉ giao hàng

---

## Gợi Ý Triển Khai

### 1. Profile Page
- Route: `/profile`
- View: `profile/index.blade.php`
- Controller: `ProfileController`
- Features:
  - Xem thông tin
  - Cập nhật thông tin
  - Đổi mật khẩu
  - Upload avatar

### 2. Notifications Page
- Route: `/notifications`
- View: `notifications/index.blade.php`
- Controller: `NotificationController`
- Features:
  - Danh sách thông báo
  - Đánh dấu đã đọc
  - Xóa thông báo
  - Badge số thông báo chưa đọc

### 3. Vouchers Page
- Route: `/vouchers`
- View: `vouchers/index.blade.php`
- Controller: `VoucherController`
- Features:
  - Danh sách voucher khả dụng
  - Áp dụng voucher khi đặt món
  - Lịch sử sử dụng

### 4. Cancel Booking/Order
- Route: `/bookings/{id}/cancel`, `/orders/{id}/cancel`
- Controller: Thêm method `cancel()` vào BookingController và OrderController
- Features:
  - Hủy đặt bàn/đơn hàng
  - Lý do hủy
  - Thông báo cho staff

### 5. Order Tracking
- Route: `/orders/{id}`
- View: `orders/show.blade.php`
- Features:
  - Chi tiết đơn hàng
  - Trạng thái real-time
  - Timeline đơn hàng

