<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\MenuItem;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            // Calculate subtotal and validate items
            foreach ($request->items as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);

                if (!$menuItem->isAvailable()) {
                    return response()->json([
                        'message' => "Món {$menuItem->name} hiện không có sẵn",
                    ], 400);
                }

                $itemSubtotal = $menuItem->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'item_price' => $menuItem->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Calculate discount if voucher provided
            $discountAmount = 0;
            $voucher = null;
            if ($request->voucher_code) {
                $voucher = Voucher::where('code', $request->voucher_code)->first();
                if ($voucher && $voucher->isValid()) {
                    $discountAmount = $voucher->calculateDiscount($subtotal);
                }
            }

            $taxAmount = $subtotal * 0.1; // 10% VAT
            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            // Create order
            $order = Order::create([
                'user_id' => $request->user()?->id,
                'table_id' => $request->table_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'order_type' => $request->order_type,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'voucher_id' => $voucher?->id,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            // Update voucher usage
            if ($voucher) {
                $voucher->increment('used_count');
            }

            // Create notification for staff
            Notification::create([
                'type' => 'new_order',
                'title' => 'Đơn hàng mới',
                'message' => "Có đơn hàng mới #{$order->order_number}",
                'notifiable_type' => Order::class,
                'notifiable_id' => $order->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Đặt món thành công',
                'data' => $order->load('orderItems.menuItem'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Order::with(['orderItems.menuItem', 'table', 'voucher']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $query->where('customer_phone', $request->phone);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $orders,
        ]);
    }

    public function show($id, Request $request)
    {
        $query = Order::with(['orderItems.menuItem', 'table', 'voucher', 'payments']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        $order = $query->findOrFail($id);

        return response()->json([
            'data' => $order,
        ]);
    }
}
