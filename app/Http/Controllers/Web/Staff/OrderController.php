<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

    public function index(Request $request)
    {
        $query = Order::with(['orderItems.menuItem', 'user', 'table', 'booking']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('staff.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['orderItems.menuItem', 'user', 'table', 'booking', 'payments', 'voucher'])
            ->findOrFail($id);

        return view('staff.orders.show', compact('order'));
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,preparing,ready,served,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng');
    }
}
