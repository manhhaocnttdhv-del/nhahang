<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Table;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'table', 'confirmedBy']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->get();

        return response()->json([
            'data' => $bookings,
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'table', 'confirmedBy', 'orders'])
            ->findOrFail($id);

        return response()->json([
            'data' => $booking,
        ]);
    }

    public function confirm($id, Request $request)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Chỉ có thể xác nhận đặt bàn đang chờ xử lý',
            ], 400);
        }

        // Check table availability
        if ($request->has('table_id')) {
            $table = Table::findOrFail($request->table_id);
            if (!$table->isAvailable()) {
                return response()->json([
                    'message' => 'Bàn này không có sẵn',
                ], 400);
            }
            $booking->table_id = $request->table_id;
            $table->update(['status' => 'reserved']);
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_by' => $request->user()->id,
            'confirmed_at' => now(),
        ]);

        // Create notification for customer
        if ($booking->user_id) {
            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'booking_confirmed',
                'title' => 'Đặt bàn đã được xác nhận',
                'message' => "Đặt bàn của bạn vào {$booking->booking_date} lúc {$booking->booking_time} đã được xác nhận",
                'notifiable_type' => Booking::class,
                'notifiable_id' => $booking->id,
            ]);
        }

        return response()->json([
            'message' => 'Đã xác nhận đặt bàn',
            'data' => $booking->load('table'),
        ]);
    }

    public function reject($id, Request $request)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Chỉ có thể từ chối đặt bàn đang chờ xử lý',
            ], 400);
        }

        $booking->update([
            'status' => 'rejected',
            'confirmed_by' => $request->user()->id,
            'confirmed_at' => now(),
        ]);

        // Create notification for customer
        if ($booking->user_id) {
            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'booking_rejected',
                'title' => 'Đặt bàn bị từ chối',
                'message' => "Đặt bàn của bạn vào {$booking->booking_date} lúc {$booking->booking_time} đã bị từ chối. Vui lòng liên hệ nhà hàng để biết thêm chi tiết.",
                'notifiable_type' => Booking::class,
                'notifiable_id' => $booking->id,
            ]);
        }

        return response()->json([
            'message' => 'Đã từ chối đặt bàn',
            'data' => $booking,
        ]);
    }

    public function checkIn($id, Request $request)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return response()->json([
                'message' => 'Chỉ có thể check-in đặt bàn đã được xác nhận',
            ], 400);
        }

        if (!$booking->table_id) {
            return response()->json([
                'message' => 'Đặt bàn chưa được gán bàn',
            ], 400);
        }

        $booking->update([
            'status' => 'checked_in',
        ]);

        $booking->table->update([
            'status' => 'occupied',
        ]);

        return response()->json([
            'message' => 'Check-in thành công',
            'data' => $booking->load('table'),
        ]);
    }
}
