<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Check table availability and time conflicts
        if ($request->has('table_id')) {
            $table = Table::findOrFail($request->table_id);
            
            // Validate table capacity
            if ($table->capacity < $booking->number_of_guests) {
                return response()->json([
                    'message' => "Bàn {$table->name} chỉ chứa tối đa {$table->capacity} người",
                ], 400);
            }
            
            if (!$table->isAvailable()) {
                return response()->json([
                    'message' => 'Bàn này không có sẵn',
                ], 400);
            }
            
            // Check for time conflicts using the same logic as Web controller
            $hasConflict = $this->checkTimeConflict($table->id, $booking);
            if ($hasConflict) {
                return response()->json([
                    'message' => 'Bàn này đã có đặt bàn khác trong khoảng thời gian này',
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

    public function transferTable($id, Request $request)
    {
        $request->validate([
            'new_table_id' => 'required|exists:tables,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $booking = Booking::with('table')->findOrFail($id);
        
        if (!in_array($booking->status, ['confirmed', 'checked_in'])) {
            return response()->json([
                'message' => 'Chỉ có thể chuyển bàn cho đặt bàn đã được xác nhận hoặc đã check-in',
            ], 400);
        }

        if (!$booking->table_id) {
            return response()->json([
                'message' => 'Đặt bàn chưa có bàn để chuyển',
            ], 400);
        }

        $oldTable = $booking->table;
        $newTable = Table::findOrFail($request->new_table_id);

        if ($oldTable->id === $newTable->id) {
            return response()->json([
                'message' => 'Bàn mới phải khác bàn hiện tại',
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($newTable->capacity < $booking->number_of_guests) {
                return response()->json([
                    'message' => "Bàn {$newTable->name} chỉ chứa tối đa {$newTable->capacity} người",
                ], 400);
            }

            if ($newTable->status === 'maintenance') {
                return response()->json([
                    'message' => 'Bàn này đang bảo trì, không thể chuyển',
                ], 400);
            }

            $hasConflict = $this->checkTimeConflict($newTable->id, $booking);
            if ($hasConflict) {
                return response()->json([
                    'message' => 'Bàn này đã có đặt bàn khác trong khoảng thời gian này',
                ], 400);
            }

            $conflictingBooking = Booking::where('table_id', $newTable->id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->whereDate('booking_date', $booking->booking_date)
                ->first();

            if ($conflictingBooking) {
                $conflictStart = \Carbon\Carbon::parse($conflictingBooking->booking_date . ' ' . $conflictingBooking->booking_time);
                $conflictEnd = $conflictingBooking->end_time 
                    ? \Carbon\Carbon::parse($conflictingBooking->booking_date . ' ' . $conflictingBooking->end_time)
                    : $conflictStart->copy()->addHours(2);
                
                $bookingStart = \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
                $bookingEnd = $booking->end_time 
                    ? \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->end_time)
                    : $bookingStart->copy()->addHours(2);

                if ($bookingStart->lessThan($conflictEnd) && $conflictStart->lessThan($bookingEnd)) {
                    return response()->json([
                        'message' => 'Bàn này đang được sử dụng bởi đặt bàn khác trong khoảng thời gian này',
                    ], 400);
                }
            }

            $oldTableId = $oldTable->id;
            $wasCheckedIn = $booking->status === 'checked_in';

            $booking->table_id = $newTable->id;
            $transferNote = "\n[Chuyển bàn] " . now()->format('d/m/Y H:i') . " - Từ bàn {$oldTable->name} sang bàn {$newTable->name}";
            if ($request->reason) {
                $transferNote .= " - Lý do: {$request->reason}";
            }
            $booking->notes = ($booking->notes ? $booking->notes : '') . $transferNote;
            $booking->save();

            \App\Models\Order::where('booking_id', $booking->id)
                ->where('table_id', $oldTableId)
                ->update(['table_id' => $newTable->id]);

            $otherBookingsOnOldTable = Booking::where('table_id', $oldTableId)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->whereDate('booking_date', '>=', today())
                ->exists();

            if ($otherBookingsOnOldTable) {
                $oldTable->update(['status' => 'reserved']);
            } else {
                $oldTable->update(['status' => 'available']);
            }

            if ($wasCheckedIn) {
                $newTable->update(['status' => 'occupied']);
            } else {
                $newTable->update(['status' => 'reserved']);
            }

            if ($booking->user_id) {
                Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => 'table_transferred',
                    'title' => 'Đã chuyển bàn',
                    'message' => "Đặt bàn của bạn đã được chuyển từ bàn {$oldTable->name} sang bàn {$newTable->name}" . ($request->reason ? ". Lý do: {$request->reason}" : ''),
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => "Đã chuyển bàn từ {$oldTable->name} sang {$newTable->name} thành công",
                'data' => $booking->load('table'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Có lỗi xảy ra khi chuyển bàn: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kiểm tra xung đột thời gian với buffer time
     * Buffer time: 15 phút để dọn dẹp và chuẩn bị bàn cho khách tiếp theo
     */
    private function checkTimeConflict($tableId, $newBooking)
    {
        $bufferMinutes = 15;
        
        $newStart = \Carbon\Carbon::parse($newBooking->booking_date . ' ' . $newBooking->booking_time);
        $newEnd = $newBooking->end_time 
            ? \Carbon\Carbon::parse($newBooking->booking_date . ' ' . $newBooking->end_time)
            : $newStart->copy()->addHours(2);
        
        $existingBookings = Booking::where('table_id', $tableId)
            ->whereDate('booking_date', $newBooking->booking_date)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $newBooking->id)
            ->get();
        
        foreach ($existingBookings as $existing) {
            $existingStart = \Carbon\Carbon::parse($existing->booking_date . ' ' . $existing->booking_time);
            $existingEnd = $existing->end_time 
                ? \Carbon\Carbon::parse($existing->booking_date . ' ' . $existing->end_time)
                : $existingStart->copy()->addHours(2);
            
            if ($existing->status === 'checked_in' && now()->greaterThan($existingEnd)) {
                $actualEnd = now()->addMinutes($bufferMinutes);
            } else {
                $actualEnd = $existingEnd->copy()->addMinutes($bufferMinutes);
            }
            
            if ($newStart->lessThan($actualEnd) && $existingStart->lessThan($newEnd)) {
                return true;
            }
        }
        
        return false;
    }
}
