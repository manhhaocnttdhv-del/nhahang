<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function process(ProcessPaymentRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->status === 'cancelled') {
            return response()->json([
                'message' => 'Không thể thanh toán đơn hàng đã bị hủy',
            ], 400);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
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

            // Free up table if dine-in
            if ($order->table_id) {
                $order->table->update(['status' => 'available']);
            }
        }

        return response()->json([
            'message' => 'Thanh toán thành công',
            'data' => $payment->load('order'),
        ], 201);
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

        $payments = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $payments,
        ]);
    }

    public function show($id)
    {
        $payment = Payment::with(['order.orderItems.menuItem', 'user'])
            ->findOrFail($id);

        return response()->json([
            'data' => $payment,
        ]);
    }
}
