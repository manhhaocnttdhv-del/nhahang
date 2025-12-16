<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\IngredientDeductionService;
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

        $order = Order::with('orderItems.menuItem.ingredients')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Nếu không có thay đổi status, không làm gì
        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Trạng thái không thay đổi');
        }

        // Xử lý trừ/hoàn trả nguyên liệu TRƯỚC khi update status
        $ingredientService = new IngredientDeductionService();

        // Trừ nguyên liệu khi bắt đầu chế biến
        if (in_array($newStatus, ['processing', 'preparing']) && !$order->ingredients_deducted) {
            try {
                $ingredientService->deductIngredientsForOrder($order);
                // Refresh order để có ingredients_deducted mới nhất
                $order->refresh();
            } catch (\Exception $e) {
                // Nếu không đủ nguyên liệu, không update status
                return back()->withErrors(['error' => $e->getMessage()]);
            }
        }

        // Hoàn trả nguyên liệu khi hủy đơn (chỉ khi đã trừ rồi và chưa phục vụ)
        if ($newStatus === 'cancelled' && $order->ingredients_deducted && !in_array($oldStatus, ['served', 'delivered'])) {
            try {
                $ingredientService->returnIngredientsForOrder($order);
                // Refresh order để có ingredients_deducted mới nhất
                $order->refresh();
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Lỗi khi hoàn trả nguyên liệu: ' . $e->getMessage()]);
            }
        }

        // Update status sau khi đã xử lý nguyên liệu thành công
        $order->update(['status' => $newStatus]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng');
    }
}
