# Hướng dẫn cấu hình Email xác thực với Gmail

## Cấu hình Gmail SMTP

Để sử dụng tính năng xác thực email qua Gmail, bạn cần cấu hình các biến môi trường sau trong file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Tạo App Password cho Gmail

1. Đăng nhập vào tài khoản Google của bạn
2. Truy cập: https://myaccount.google.com/security
3. Bật **Xác minh 2 bước** (nếu chưa bật)
4. Vào phần **Mật khẩu ứng dụng** (App passwords)
5. Chọn ứng dụng: **Thư** (Mail)
6. Chọn thiết bị: **Máy tính khác** (Other) và nhập tên: "Laravel App"
7. Nhấn **Tạo** và sao chép mật khẩu 16 ký tự
8. Dán mật khẩu này vào biến `MAIL_PASSWORD` trong file `.env`

**Lưu ý:** Không sử dụng mật khẩu Gmail thông thường, bạn phải sử dụng App Password.

## Kiểm tra cấu hình

Sau khi cấu hình xong, bạn có thể test bằng cách:

1. Đăng ký tài khoản mới
2. Kiểm tra email để nhận link xác thực
3. Nhấn vào link để xác thực tài khoản

## Gửi lại email xác thực

Nếu bạn không nhận được email xác thực, có thể:

- **Web:** Truy cập trang đăng nhập và sử dụng form gửi lại email xác thực
- **API:** Gửi POST request đến `/api/email/resend` với body:
  ```json
  {
    "email": "your-email@gmail.com"
  }
  ```

## Xử lý lỗi thường gặp

### Lỗi: "Connection could not be established"
- Kiểm tra `MAIL_HOST` và `MAIL_PORT` đã đúng chưa
- Đảm bảo firewall không chặn port 587

### Lỗi: "Authentication failed"
- Kiểm tra `MAIL_USERNAME` và `MAIL_PASSWORD` đã đúng chưa
- Đảm bảo đang sử dụng App Password, không phải mật khẩu Gmail thông thường
- Kiểm tra xem **Xác minh 2 bước** đã được bật chưa

### Email không được gửi
- Kiểm tra logs trong `storage/logs/laravel.log`
- Kiểm tra spam folder trong Gmail
- Đảm bảo `MAIL_FROM_ADDRESS` khớp với `MAIL_USERNAME`
