<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create([
            'user_id' => $request->user()?->id,
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
        Notification::create([
            'type' => 'new_booking',
            'title' => 'Đặt bàn mới',
            'message' => "Có đặt bàn mới từ {$request->customer_name} vào {$request->booking_date} lúc {$request->booking_time}",
            'notifiable_type' => Booking::class,
            'notifiable_id' => $booking->id,
        ]);

        return response()->json([
            'message' => 'Đặt bàn thành công. Vui lòng chờ xác nhận.',
            'data' => $booking,
        ], 201);
    }

    public function index(Request $request)
    {
        $query = Booking::with(['table', 'confirmedBy']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        } else {
            $query->where('customer_phone', $request->phone);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();

        return response()->json([
            'data' => $bookings,
        ]);
    }

    public function show($id, Request $request)
    {
        $query = Booking::with(['table', 'confirmedBy', 'orders']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        $booking = $query->findOrFail($id);

        return response()->json([
            'data' => $booking,
        ]);
    }
}
