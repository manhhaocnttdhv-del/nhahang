<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['order', 'user']);

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('staff.payments.index', compact('payments'));
    }

    public function create($orderId)
    {
        $order = Order::with(['orderItems.menuItem', 'payments'])->findOrFail($orderId);
        
        return view('staff.payments.create', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,bank_transfer,momo,vnpay,bank_card',
            'amount' => 'required|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($request->order_id);

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'status' => 'completed',
            'notes' => $request->notes,
        ]);

        // Update order status if fully paid
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        if ($totalPaid >= $order->total_amount) {
            if ($order->order_type === 'dine_in') {
                $order->update(['status' => 'served']);
            } elseif ($order->order_type === 'delivery') {
                $order->update(['status' => 'delivered']);
            } else {
                $order->update(['status' => 'served']);
            }

            // Check if booking should be completed
            if ($order->booking_id) {
                $booking = \App\Models\Booking::find($order->booking_id);
                if ($booking) {
                    $this->checkAndCompleteBooking($booking);
                }
            }

            // Free up table if no other active orders
            if ($order->table_id) {
                $hasActiveOrders = \App\Models\Order::where('table_id', $order->table_id)
                    ->whereIn('status', ['pending', 'processing', 'preparing', 'ready'])
                    ->exists();
                
                if (!$hasActiveOrders) {
                    $order->table->update(['status' => 'available']);
                }
            }
        }

        return redirect()->route('staff.payments.index')
            ->with('success', 'Thanh toán thành công');
    }

    /**
     * Hiển thị chi tiết thanh toán và form xác nhận
     */
    public function show($id)
    {
        $payment = Payment::with(['order.orderItems', 'order.user', 'order.table', 'user'])
            ->findOrFail($id);

        return view('staff.payments.show', compact('payment'));
    }

    /**
     * Xác nhận thanh toán (chuyển từ pending sang completed)
     */
    public function confirm($id, Request $request)
    {
        $payment = Payment::with('order')->findOrFail($id);

        if ($payment->status === 'completed') {
            return back()->with('error', 'Thanh toán này đã được xác nhận rồi');
        }

        // Cập nhật status
        $payment->update([
            'status' => 'completed',
            'notes' => $request->notes ?? $payment->notes,
        ]);

        // Update order status if fully paid
        $order = $payment->order;
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        
        if ($totalPaid >= $order->total_amount) {
            if ($order->order_type === 'dine_in') {
                $order->update(['status' => 'served']);
            } elseif ($order->order_type === 'delivery') {
                $order->update(['status' => 'delivered']);
            } else {
                $order->update(['status' => 'served']);
            }

            // Check if booking should be completed
            if ($order->booking_id) {
                $booking = \App\Models\Booking::find($order->booking_id);
                if ($booking) {
                    $this->checkAndCompleteBooking($booking);
                }
            }

            // Free up table if no other active orders
            if ($order->table_id) {
                $hasActiveOrders = \App\Models\Order::where('table_id', $order->table_id)
                    ->where('id', '!=', $order->id)
                    ->whereIn('status', ['pending', 'processing', 'preparing', 'ready'])
                    ->exists();
                
                if (!$hasActiveOrders) {
                    $order->table->update(['status' => 'available']);
                }
            }

            // Tạo thông báo cho khách hàng
            if ($order->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $order->user_id,
                    'type' => 'payment_confirmed',
                    'title' => 'Thanh toán đã được xác nhận',
                    'message' => "Thanh toán cho đơn hàng #{$order->order_number} đã được xác nhận. Cảm ơn bạn!",
                    'notifiable_type' => \App\Models\Order::class,
                    'notifiable_id' => $order->id,
                ]);
            }
        }

        return redirect()->route('staff.payments.show', $payment->id)
            ->with('success', 'Đã xác nhận thanh toán thành công');
    }
    
    /**
     * Check and complete booking if all orders are paid
     */
    private function checkAndCompleteBooking($booking)
    {
        // Get all orders for this booking
        $orders = \App\Models\Order::where('booking_id', $booking->id)
            ->where('status', '!=', 'cancelled')
            ->get();
        
        if ($orders->isEmpty()) {
            return;
        }
        
        // Check if all orders are fully paid
        $allPaid = true;
        foreach ($orders as $order) {
            $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
            if ($totalPaid < $order->total_amount) {
                $allPaid = false;
                break;
            }
        }
        
        // Complete booking if all orders are paid
        if ($allPaid && $booking->status === 'checked_in') {
            $booking->update(['status' => 'completed']);
            
            // Free up table if no next booking
            if ($booking->table_id) {
                $hasNextBooking = \App\Models\Booking::where('table_id', $booking->table_id)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['confirmed', 'checked_in'])
                    ->whereDate('booking_date', '>=', $booking->booking_date)
                    ->exists();
                
                if (!$hasNextBooking) {
                    $booking->table->update(['status' => 'available']);
                }
            }
            
            // Create notification for customer
            if ($booking->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => 'booking_completed',
                    'title' => 'Đặt bàn đã hoàn thành',
                    'message' => "Đặt bàn của bạn vào {$booking->booking_date->format('d/m/Y')} đã hoàn thành. Cảm ơn bạn đã sử dụng dịch vụ!",
                    'notifiable_type' => \App\Models\Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }
        }
    }
}
