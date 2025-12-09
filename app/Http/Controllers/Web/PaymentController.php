<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị trang thanh toán QR
     */
    public function showQr($orderId)
    {
        $order = Order::with(['orderItems', 'user', 'voucher'])
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        // Kiểm tra xem đơn hàng đã được thanh toán chưa
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        $remainingAmount = $order->total_amount - $totalPaid;

        if ($remainingAmount <= 0) {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Đơn hàng này đã được thanh toán đầy đủ');
        }

        // Tạo dữ liệu QR code
        $qrData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $remainingAmount,
            'merchant' => config('app.name', 'Nhà Hàng'),
            'timestamp' => now()->timestamp,
        ];

        // Tạo chuỗi JSON để mã hóa vào QR
        $qrString = json_encode($qrData);

        // Tạo QR code dạng SVG (không cần imagick)
        $qrCodeSvg = QrCode::size(300)
            ->format('svg')
            ->generate($qrString);

        return view('payments.qr', compact('order', 'remainingAmount', 'qrCodeSvg', 'qrData'));
    }

    /**
     * Xử lý thanh toán QR (khi khách hàng quét và thanh toán)
     */
    public function processQr(Request $request, $orderId)
    {
        $request->validate([
            'transaction_id' => 'required|string|max:255',
            'payment_method' => 'required|in:qr_momo,qr_vnpay,qr_bank',
        ]);

        $order = Order::where('user_id', auth()->id())
            ->findOrFail($orderId);

        // Kiểm tra xem đơn hàng đã được thanh toán chưa
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        $remainingAmount = $order->total_amount - $totalPaid;

        if ($remainingAmount <= 0) {
            return back()->with('error', 'Đơn hàng này đã được thanh toán đầy đủ');
        }

        // Map QR payment methods to database enum values
        $paymentMethodMap = [
            'qr_momo' => 'momo',
            'qr_vnpay' => 'vnpay',
            'qr_bank' => 'bank_transfer',
        ];

        $paymentMethod = $paymentMethodMap[$request->payment_method] ?? 'bank_transfer';
        
        // Tạo payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'payment_method' => $paymentMethod,
            'amount' => $remainingAmount,
            'transaction_id' => $request->transaction_id,
            'status' => 'pending', // Chờ xác nhận từ staff
            'notes' => 'Thanh toán qua QR code - ' . $request->payment_method,
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Đã gửi thông tin thanh toán. Vui lòng chờ xác nhận từ nhà hàng.');
    }
}

