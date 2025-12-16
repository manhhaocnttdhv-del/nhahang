<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Notification;
use App\Models\Order;
use App\Services\IngredientDeductionService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['orderItems.menuItem', 'user', 'table', 'booking', 'payments', 'voucher'])
            ->findOrFail($id);

        return response()->json([
            'data' => $order,
        ]);
    }

    public function updateStatus($id, UpdateOrderStatusRequest $request)
    {
        $order = Order::with('orderItems.menuItem.ingredients')->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Nếu không có thay đổi status, không làm gì
        if ($oldStatus === $newStatus) {
            return response()->json([
                'message' => 'Trạng thái không thay đổi',
                'data' => $order,
            ]);
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
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }
        }

        // Hoàn trả nguyên liệu khi hủy đơn (chỉ khi đã trừ rồi và chưa phục vụ)
        if ($newStatus === 'cancelled' && $order->ingredients_deducted && !in_array($oldStatus, ['served', 'delivered'])) {
            try {
                $ingredientService->returnIngredientsForOrder($order);
                // Refresh order để có ingredients_deducted mới nhất
                $order->refresh();
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Lỗi khi hoàn trả nguyên liệu: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Update status sau khi đã xử lý nguyên liệu thành công
        $order->update([
            'status' => $newStatus,
        ]);

        // Create notification for customer
        if ($order->user_id) {
            $statusMessages = [
                'processing' => 'Đơn hàng đang được xử lý',
                'preparing' => 'Đơn hàng đang được chế biến',
                'ready' => 'Đơn hàng đã sẵn sàng',
                'served' => 'Đơn hàng đã được phục vụ',
                'delivered' => 'Đơn hàng đã được giao',
                'cancelled' => 'Đơn hàng đã bị hủy',
            ];

            Notification::create([
                'user_id' => $order->user_id,
                'type' => 'order_status_update',
                'title' => 'Cập nhật trạng thái đơn hàng',
                'message' => $statusMessages[$request->status] ?? "Đơn hàng #{$order->order_number} đã được cập nhật",
                'notifiable_type' => Order::class,
                'notifiable_id' => $order->id,
            ]);
        }

        return response()->json([
            'message' => 'Đã cập nhật trạng thái đơn hàng',
            'data' => $order->fresh(),
        ]);
    }
}
