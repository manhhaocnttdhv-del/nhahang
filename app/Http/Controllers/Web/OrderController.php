<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create()
    {
        $menuItems = MenuItem::where('is_active', true)
            ->where('status', 'available')
            ->with('category')
            ->get()
            ->groupBy('category.name');

        return view('orders.create', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'table_id' => 'nullable|exists:tables,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'customer_name' => 'nullable|string|max:255|required_if:order_type,delivery,takeaway',
            'customer_phone' => 'nullable|string|max:20|required_if:order_type,delivery,takeaway',
            'customer_address' => 'nullable|string|max:500|required_if:order_type,delivery',
            'voucher_code' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // If booking_id is provided, table_id is optional (can be set later)
        if ($request->booking_id && !$request->table_id) {
            $booking = \App\Models\Booking::find($request->booking_id);
            if ($booking && $booking->table_id) {
                $request->merge(['table_id' => $booking->table_id]);
            }
        } elseif ($request->order_type === 'dine_in' && !$request->booking_id && !$request->table_id) {
            return back()->withErrors(['table_id' => 'Vui lòng chọn bàn hoặc đặt bàn trước'])->withInput();
        }

        $items = json_decode($request->items, true);
        
        if (empty($items) || !is_array($items)) {
            return back()->withErrors(['items' => 'Vui lòng chọn ít nhất một món'])->withInput();
        }
        
        // Validate items structure
        foreach ($items as $item) {
            if (!isset($item['menu_item_id']) || !isset($item['quantity'])) {
                return back()->withErrors(['items' => 'Dữ liệu món ăn không hợp lệ'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItems = [];

            // Calculate subtotal and validate items
            foreach ($items as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);

                if (!$menuItem->isAvailable()) {
                    return back()->withErrors(['items' => "Món {$menuItem->name} hiện không có sẵn"]);
                }

                $itemSubtotal = $menuItem->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'item_name' => $menuItem->name,
                    'item_price' => $menuItem->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate discount if voucher provided
            $discountAmount = 0;
            $voucher = null;
            if ($request->voucher_code) {
                $voucher = \App\Models\Voucher::where('code', $request->voucher_code)->first();
                if ($voucher && $voucher->isValid()) {
                    // Check minimum order amount
                    if (!$voucher->min_order_amount || $subtotal >= $voucher->min_order_amount) {
                        $discountAmount = $voucher->calculateDiscount($subtotal);
                    } else {
                        return back()->withErrors(['voucher_code' => "Đơn hàng tối thiểu " . number_format((float)$voucher->min_order_amount) . " đ để sử dụng voucher này"])->withInput();
                    }
                } else {
                    return back()->withErrors(['voucher_code' => 'Mã voucher không hợp lệ hoặc đã hết hạn'])->withInput();
                }
            }

            $taxAmount = ($subtotal - $discountAmount) * 0.1; // 10% VAT
            $totalAmount = $subtotal - $discountAmount + $taxAmount;

            // Validate booking if provided
            $booking = null;
            if ($request->booking_id) {
                $booking = \App\Models\Booking::where('user_id', auth()->id())
                    ->findOrFail($request->booking_id);
                
                // If booking is pending, order will wait for confirmation
                // If booking is confirmed/checked_in, order can be processed
                if ($booking->status === 'rejected' || $booking->status === 'cancelled') {
                    return back()->withErrors(['booking_id' => 'Đặt bàn này đã bị hủy']);
                }
                
                // Auto assign table_id from booking if not provided
                if (!$request->table_id && $booking->table_id) {
                    $request->merge(['table_id' => $booking->table_id]);
                }
            }

            // Get customer info - use form data if provided, otherwise use auth user
            $customerName = $request->customer_name ?: auth()->user()->name;
            $customerPhone = $request->customer_phone ?: auth()->user()->phone;
            
            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
                'user_id' => auth()->id(),
                'table_id' => $request->table_id,
                'booking_id' => $request->booking_id,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
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
            
            // Update voucher usage
            if ($voucher) {
                $voucher->increment('used_count');
            }
            
            // Create notification for each staff member if booking is confirmed
            if ($booking && $booking->status === 'confirmed') {
                $staffMembers = \App\Models\User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
                foreach ($staffMembers as $staff) {
                \App\Models\Notification::create([
                        'user_id' => $staff->id,
                    'type' => 'new_order',
                    'title' => 'Đơn hàng mới từ đặt bàn',
                    'message' => "Có đơn hàng mới từ đặt bàn #{$booking->id}",
                    'notifiable_type' => Order::class,
                    'notifiable_id' => $order->id,
                ]);
                }
            }

            // Create order items
            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;
                \App\Models\OrderItem::create($item);
            }

            DB::commit();

            if ($request->booking_id) {
                // Refresh booking to load new order
                $booking = \App\Models\Booking::find($request->booking_id);
                if ($booking) {
                    $booking->load('orders.orderItems');
                }
                
                return redirect()->route('bookings.show', $request->booking_id)
                    ->with('success', 'Đặt món thành công! Đơn hàng #' . $order->order_number . ' đã được tạo.');
            }

            return redirect()->route('orders.index')
                ->with('success', 'Đặt món thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['orderItems.menuItem', 'table', 'voucher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }
    
    public function show($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->with(['orderItems.menuItem', 'table', 'voucher', 'payments'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }
    
    public function cancel($id, Request $request)
    {
        $order = Order::where('user_id', auth()->id())
            ->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => 'Chỉ có thể hủy đơn hàng đang chờ xử lý']);
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'cancelled',
                'notes' => ($order->notes ? $order->notes . "\n" : '') . 'Lý do hủy: ' . ($request->cancel_reason ?: 'Khách hàng hủy'),
            ]);

            // Refund voucher if used
            if ($order->voucher_id) {
                $order->voucher->decrement('used_count');
            }

            // Create notification for each staff member
            $staffMembers = \App\Models\User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
            foreach ($staffMembers as $staff) {
            \App\Models\Notification::create([
                    'user_id' => $staff->id,
                'type' => 'order_cancelled',
                'title' => 'Đơn hàng bị hủy',
                'message' => "Đơn hàng #{$order->order_number} đã bị khách hàng hủy",
                'notifiable_type' => Order::class,
                'notifiable_id' => $order->id,
            ]);
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Đã hủy đơn hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
