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
        
        // Lấy danh sách bàn cho confirm (tất cả bàn available)
        $tables = Table::where('is_active', true)
            ->where('status', 'available')
            ->orderBy('name')
            ->get();
        
        // Lấy danh sách bàn có thể chuyển (loại trừ bàn hiện tại và bàn maintenance)
        $availableTables = Table::where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->where('id', '!=', $booking->table_id)
            ->where('capacity', '>=', $booking->number_of_guests)
            ->orderBy('name')
            ->get();
        
        return view('staff.bookings.show', compact('booking', 'tables', 'availableTables'));
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
                
                // Check for time conflicts with buffer time
                $hasConflict = $this->checkTimeConflict($table->id, $booking);
                
                if ($hasConflict) {
                    return back()->with('error', 'Bàn này đã có đặt bàn khác trong khoảng thời gian này. Vui lòng chọn thời gian khác hoặc bàn khác.');
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
            $hasConflict = $this->checkTimeConflict($table->id, $booking);
            
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

    /**
     * Chuyển bàn cho khách hàng
     * Cho phép nhân viên chuyển bàn khi booking đã confirmed hoặc checked_in
     */
    public function transferTable($id, Request $request)
    {
        $request->validate([
            'new_table_id' => 'required|exists:tables,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $booking = Booking::with('table')->findOrFail($id);
        
        // Chỉ cho phép chuyển bàn khi đã confirmed hoặc checked_in
        if (!in_array($booking->status, ['confirmed', 'checked_in'])) {
            return back()->with('error', 'Chỉ có thể chuyển bàn cho đặt bàn đã được xác nhận hoặc đã check-in');
        }

        if (!$booking->table_id) {
            return back()->with('error', 'Đặt bàn chưa có bàn để chuyển');
        }

        $oldTable = $booking->table;
        $newTable = Table::findOrFail($request->new_table_id);

        // Không cho phép chuyển cùng bàn
        if ($oldTable->id === $newTable->id) {
            return back()->with('error', 'Bàn mới phải khác bàn hiện tại');
        }

        DB::beginTransaction();
        try {
            // Validate bàn mới
            // 1. Kiểm tra capacity
            if ($newTable->capacity < $booking->number_of_guests) {
                return back()->with('error', "Bàn {$newTable->name} chỉ chứa tối đa {$newTable->capacity} người, nhưng đặt bàn có {$booking->number_of_guests} người");
            }

            // 2. Kiểm tra bàn mới có available không (trừ khi đang occupied bởi booking này)
            if ($newTable->status === 'maintenance') {
                return back()->with('error', 'Bàn này đang bảo trì, không thể chuyển');
            }

            // 3. Kiểm tra xung đột thời gian trên bàn mới
            $hasConflict = $this->checkTimeConflict($newTable->id, $booking);
            if ($hasConflict) {
                return back()->with('error', 'Bàn này đã có đặt bàn khác trong khoảng thời gian này. Vui lòng chọn bàn khác.');
            }

            // 4. Kiểm tra bàn mới có đang occupied bởi booking khác không
            $conflictingBooking = Booking::where('table_id', $newTable->id)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->whereDate('booking_date', $booking->booking_date)
                ->first();

            if ($conflictingBooking) {
                // Kiểm tra xem có overlap thời gian không
                $conflictStart = \Carbon\Carbon::parse($conflictingBooking->booking_date . ' ' . $conflictingBooking->booking_time);
                $conflictEnd = $conflictingBooking->end_time 
                    ? \Carbon\Carbon::parse($conflictingBooking->booking_date . ' ' . $conflictingBooking->end_time)
                    : $conflictStart->copy()->addHours(2);
                
                $bookingStart = \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
                $bookingEnd = $booking->end_time 
                    ? \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->end_time)
                    : $bookingStart->copy()->addHours(2);

                if ($bookingStart->lessThan($conflictEnd) && $conflictStart->lessThan($bookingEnd)) {
                    return back()->with('error', 'Bàn này đang được sử dụng bởi đặt bàn khác trong khoảng thời gian này');
                }
            }

            // Thực hiện chuyển bàn
            $oldTableId = $oldTable->id;
            $wasCheckedIn = $booking->status === 'checked_in';

            // Cập nhật booking
            $booking->table_id = $newTable->id;
            
            // Ghi chú về việc chuyển bàn
            $transferNote = "\n[Chuyển bàn] " . now()->format('d/m/Y H:i') . " - Từ bàn {$oldTable->name} sang bàn {$newTable->name}";
            if ($request->reason) {
                $transferNote .= " - Lý do: {$request->reason}";
            }
            $booking->notes = ($booking->notes ? $booking->notes : '') . $transferNote;
            $booking->save();

            // Cập nhật orders liên quan
            \App\Models\Order::where('booking_id', $booking->id)
                ->where('table_id', $oldTableId)
                ->update(['table_id' => $newTable->id]);

            // Cập nhật trạng thái bàn cũ
            // Kiểm tra xem bàn cũ có booking khác không
            $otherBookingsOnOldTable = Booking::where('table_id', $oldTableId)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->whereDate('booking_date', '>=', today())
                ->exists();

            if ($otherBookingsOnOldTable) {
                // Có booking khác, giữ nguyên trạng thái hoặc chuyển về reserved
                $oldTable->update(['status' => 'reserved']);
            } else {
                // Không có booking khác, chuyển về available
                $oldTable->update(['status' => 'available']);
            }

            // Cập nhật trạng thái bàn mới
            if ($wasCheckedIn) {
                $newTable->update(['status' => 'occupied']);
            } else {
                $newTable->update(['status' => 'reserved']);
            }

            // Tạo notification cho khách hàng
            if ($booking->user_id) {
                \App\Models\Notification::create([
                    'user_id' => $booking->user_id,
                    'type' => 'table_transferred',
                    'title' => 'Đã chuyển bàn',
                    'message' => "Đặt bàn của bạn đã được chuyển từ bàn {$oldTable->name} sang bàn {$newTable->name}" . ($request->reason ? ". Lý do: {$request->reason}" : ''),
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            DB::commit();
            return back()->with('success', "Đã chuyển bàn từ {$oldTable->name} sang {$newTable->name} thành công");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi chuyển bàn: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra xung đột thời gian với buffer time
     * Buffer time: 15 phút để dọn dẹp và chuẩn bị bàn cho khách tiếp theo
     * 
     * @param int $tableId
     * @param Booking $newBooking
     * @return bool
     */
    private function checkTimeConflict($tableId, $newBooking)
    {
        $bufferMinutes = 15; // Buffer time 15 phút giữa các đặt bàn
        
        // Lấy thời gian bắt đầu và kết thúc của đặt bàn mới
        $newStart = \Carbon\Carbon::parse($newBooking->booking_date . ' ' . $newBooking->booking_time);
        $newEnd = $newBooking->end_time 
            ? \Carbon\Carbon::parse($newBooking->booking_date . ' ' . $newBooking->end_time)
            : $newStart->copy()->addHours(2); // Fallback nếu chưa có end_time
        
        // Tìm các đặt bàn đã có trên bàn này trong cùng ngày
        $existingBookings = Booking::where('table_id', $tableId)
            ->whereDate('booking_date', $newBooking->booking_date)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $newBooking->id) // Loại trừ chính đặt bàn này nếu đang cập nhật
            ->get();
        
        foreach ($existingBookings as $existing) {
            $existingStart = \Carbon\Carbon::parse($existing->booking_date . ' ' . $existing->booking_time);
            
            // Xác định thời gian kết thúc thực tế của đặt bàn cũ
            $existingEnd = $existing->end_time 
                ? \Carbon\Carbon::parse($existing->booking_date . ' ' . $existing->end_time)
                : $existingStart->copy()->addHours(2); // Fallback
            
            // Nếu đặt bàn cũ đang checked_in và quá giờ, vẫn tính là đang sử dụng
            // (có thể khách quá giờ chưa về)
            if ($existing->status === 'checked_in' && now()->greaterThan($existingEnd)) {
                // Nếu quá giờ nhưng vẫn checked_in, coi như đang sử dụng đến hiện tại + buffer
                $actualEnd = now()->addMinutes($bufferMinutes);
            } else {
                // Thêm buffer time sau thời gian kết thúc
                $actualEnd = $existingEnd->copy()->addMinutes($bufferMinutes);
            }
            
            // Kiểm tra xung đột: hai khung thời gian overlap nếu:
            // newStart < actualEnd AND existingStart < newEnd
            if ($newStart->lessThan($actualEnd) && $existingStart->lessThan($newEnd)) {
                return true; // Có xung đột
            }
        }
        
        return false; // Không có xung đột
    }
}
