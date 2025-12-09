<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

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
            ->paginate(20);

        return view('staff.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'table', 'confirmedBy', 'orders'])->findOrFail($id);
        $tables = Table::where('is_active', true)->where('status', 'available')->get();
        
        return view('staff.bookings.show', compact('booking', 'tables'));
    }

    public function confirm($id, Request $request)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể xác nhận đặt bàn đang chờ xử lý');
        }

        DB::beginTransaction();
        try {
            $table = null;
            
            // Auto assign table if not provided
            if (!$request->has('table_id') || empty($request->table_id)) {
                $table = $this->autoAssignTable($booking);
            } else {
                $table = Table::findOrFail($request->table_id);
                
                // Validate table capacity
                if ($table->capacity < $booking->number_of_guests) {
                    return back()->with('error', "Bàn {$table->name} chỉ chứa tối đa {$table->capacity} người, nhưng đặt bàn có {$booking->number_of_guests} người");
                }
                
                // Check table availability
                if (!$table->isAvailable()) {
                    return back()->with('error', 'Bàn này không có sẵn');
                }
                
                // Check for time conflicts
                $hasConflict = Booking::where('table_id', $table->id)
                    ->whereDate('booking_date', $booking->booking_date)
                    ->where('status', '!=', 'rejected')
                    ->where('status', '!=', 'cancelled')
                    ->where(function($query) use ($booking) {
                        $bookingTime = \Carbon\Carbon::parse($booking->booking_time)->format('H:i');
                        $query->where('booking_time', '<=', $bookingTime)
                              ->whereRaw("ADDTIME(booking_time, '02:00:00') > ?", [$bookingTime]);
                    })
                    ->exists();
                
                if ($hasConflict) {
                    return back()->with('error', 'Bàn này đã có đặt bàn khác trong khoảng thời gian này');
                }
            }

            if ($table) {
                $booking->table_id = $table->id;
                $table->update(['status' => 'reserved']);
            }

            $booking->update([
                'status' => 'confirmed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            // Create notification for customer
            if ($booking->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => 'booking_confirmed',
                    'title' => 'Đặt bàn đã được xác nhận',
                    'message' => "Đặt bàn của bạn vào {$booking->booking_date->format('d/m/Y')} lúc {$booking->booking_time->format('H:i')} đã được xác nhận" . ($table ? ". Bàn: {$table->name}" : ''),
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            // Process pending pre-orders
            $pendingOrders = \App\Models\Order::where('booking_id', $booking->id)
                ->where('status', 'pending')
                ->get();
            
            foreach ($pendingOrders as $order) {
                // Orders can now be processed
                // Staff will handle them in order management
            }

            DB::commit();
            return back()->with('success', 'Đã xác nhận đặt bàn' . ($table ? " và gán bàn {$table->name}" : ''));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    private function autoAssignTable($booking)
    {
        // Find suitable table
        $query = Table::where('is_active', true)
            ->where('status', 'available')
            ->where('capacity', '>=', $booking->number_of_guests);
        
        // Match location preference if provided
        if ($booking->location_preference) {
            if (str_contains($booking->location_preference, 'Tầng 1')) {
                $query->where('area', 'Tầng 1');
            } elseif (str_contains($booking->location_preference, 'Tầng 2')) {
                $query->where('area', 'Tầng 2');
            } elseif (str_contains($booking->location_preference, 'Phòng riêng') || str_contains($booking->location_preference, 'VIP')) {
                $query->where('area', 'like', '%VIP%');
            }
        }
        
        $tables = $query->orderBy('capacity', 'asc') // Prefer smaller tables
            ->get();
        
        // Check for time conflicts
        foreach ($tables as $table) {
            $hasConflict = Booking::where('table_id', $table->id)
                ->whereDate('booking_date', $booking->booking_date)
                ->where('status', '!=', 'rejected')
                ->where('status', '!=', 'cancelled')
                ->where(function($q) use ($booking) {
                    $bookingTime = \Carbon\Carbon::parse($booking->booking_time)->format('H:i');
                    $q->where('booking_time', '<=', $bookingTime)
                      ->whereRaw("ADDTIME(booking_time, '02:00:00') > ?", [$bookingTime]);
                })
                ->exists();
            
            if (!$hasConflict) {
                return $table;
            }
        }
        
        return null; // No suitable table found
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Đã từ chối đặt bàn');
    }

    public function checkIn($id)
    {
        $booking = Booking::findOrFail($id);
        
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Chỉ có thể check-in đặt bàn đã được xác nhận');
        }

        if (!$booking->table_id) {
            return back()->with('error', 'Đặt bàn chưa được gán bàn');
        }

        $booking->update(['status' => 'checked_in']);
        $booking->table->update(['status' => 'occupied']);

        return back()->with('success', 'Check-in thành công');
    }
}
