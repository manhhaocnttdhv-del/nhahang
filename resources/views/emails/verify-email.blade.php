<x-mail::message>
# Xác thực email đăng ký tài khoản

Xin chào {{ $user->name }},

Cảm ơn bạn đã đăng ký tài khoản tại {{ config('app.name') }}!

Vui lòng nhấn vào nút bên dưới để xác thực địa chỉ email của bạn:

<x-mail::button :url="$verificationUrl">
Xác thực email
</x-mail::button>

Link này sẽ hết hạn sau 60 phút.

Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email này.

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
