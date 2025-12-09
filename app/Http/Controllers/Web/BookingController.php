<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create()
    {
        $tables = Table::where('is_active', true)
            ->with(['bookings' => function($query) {
                $query->whereDate('booking_date', today())
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
            }])
            ->get()
            ->groupBy('area');
        
        // Get today's bookings for reference
        $todayBookings = Booking::whereDate('booking_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('table')
            ->get();

        return view('bookings.create', compact('tables', 'todayBookings'));
    }

    public function store(StoreBookingRequest $request)
    {
        // Validate number of guests
        if ($request->number_of_guests < 1 || $request->number_of_guests > 50) {
            return back()->withErrors(['number_of_guests' => 'Số lượng khách phải từ 1 đến 50 người']);
        }

        // Validate booking date and time
        $bookingDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->booking_time);
        if ($bookingDateTime->isPast()) {
            return back()->withErrors(['booking_date' => 'Không thể đặt bàn trong quá khứ']);
        }

        // Check if booking time is within business hours (8:00 - 22:00)
        $bookingHour = $bookingDateTime->hour;
        if ($bookingHour < 8 || $bookingHour >= 22) {
            return back()->withErrors(['booking_time' => 'Giờ đặt bàn phải từ 8:00 đến 22:00']);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'number_of_guests' => $request->number_of_guests,
            'location_preference' => $request->location_preference,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Create notification for staff
        \App\Models\Notification::create([
            'user_id' => null, // For all staff
            'type' => 'new_booking',
            'title' => 'Đặt bàn mới',
            'message' => "Có đặt bàn mới từ {$request->customer_name} vào {$request->booking_date} lúc {$request->booking_time}",
            'notifiable_type' => Booking::class,
            'notifiable_id' => $booking->id,
        ]);

        return redirect()->route('bookings.success', $booking->id);
    }
    
    public function success($id)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->findOrFail($id);
            
        return view('bookings.success', compact('booking'));
    }

    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('table')
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->with(['table', 'orders' => function($query) {
                $query->orderBy('created_at', 'desc');
            }, 'orders.orderItems'])
            ->findOrFail($id);

        return view('bookings.show', compact('booking'));
    }

    public function orderFromTable($bookingId)
    {
        // Allow ordering even if booking is pending (pre-order)
        $booking = Booking::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('table')
            ->findOrFail($bookingId);

        $menuItems = \App\Models\MenuItem::where('is_active', true)
            ->where('status', 'available')
            ->with('category')
            ->get()
            ->groupBy('category.name');

        return view('bookings.order', compact('booking', 'menuItems'));
    }
    
    public function cancel($id, Request $request)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->withErrors(['error' => 'Chỉ có thể hủy đặt bàn đang chờ xác nhận']);
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'notes' => ($booking->notes ? $booking->notes . "\n" : '') . 'Lý do hủy: ' . ($request->cancel_reason ?: 'Khách hàng hủy'),
            ]);

            // Cancel pending pre-orders
            \App\Models\Order::where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            // Create notification for staff
            \App\Models\Notification::create([
                'user_id' => null,
                'type' => 'booking_cancelled',
                'title' => 'Đặt bàn bị hủy',
                'message' => "Đặt bàn #{$booking->id} vào " . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') . " lúc " . \Carbon\Carbon::parse($booking->booking_time)->format('H:i') . " đã bị khách hàng hủy",
                'notifiable_type' => Booking::class,
                'notifiable_id' => $booking->id,
            ]);

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Đã hủy đặt bàn thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
