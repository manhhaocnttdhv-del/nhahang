<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Table;
use App\Helpers\SessionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        // Get selected date from request or default to today
        $selectedDate = $request->get('date', today()->format('Y-m-d'));
        
        $tables = Table::where('is_active', true)
            ->with(['bookings' => function($query) use ($selectedDate) {
                $query->whereDate('booking_date', $selectedDate)
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
            }])
            ->get()
            ->groupBy('area');
        
        // Get bookings for selected date
        $selectedDateBookings = Booking::whereDate('booking_date', $selectedDate)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('table')
            ->get();

        return view('bookings.create', compact('tables', 'selectedDateBookings', 'selectedDate'));
    }

    public function getBookingsByDate($date)
    {
        $bookings = Booking::whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->with('table')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'customer_name' => $booking->customer_name,
                    'booking_date' => $booking->booking_date instanceof \Carbon\Carbon 
                        ? $booking->booking_date->format('Y-m-d') 
                        : $booking->booking_date,
                    'session' => $booking->session, // Thêm session
                    'booking_time' => $booking->booking_time, // Giữ lại để tương thích
                    'end_time' => $booking->end_time, // Giữ lại để tương thích
                    'status' => $booking->status,
                    'table_id' => $booking->table_id,
                    'table' => $booking->table ? [
                        'id' => $booking->table->id,
                        'name' => $booking->table->name,
                    ] : null,
                ];
            });

        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        // Validate number of guests
        if ($request->number_of_guests < 1 || $request->number_of_guests > 50) {
            return back()->withErrors(['number_of_guests' => 'Số lượng khách phải từ 1 đến 50 người']);
        }

        // Validate booking date
        $bookingDate = \Carbon\Carbon::parse($request->booking_date);
        if ($bookingDate->isPast() && !$bookingDate->isToday()) {
            return back()->withErrors(['booking_date' => 'Không thể đặt bàn trong quá khứ'])->withInput();
        }

        // Lấy thời gian từ session
        $sessionTimeRange = SessionHelper::getSessionTimeRange($request->session);
        $bookingTime = $sessionTimeRange['start'];
        $endTime = $sessionTimeRange['end'];
        $durationMinutes = \Carbon\Carbon::parse($request->booking_date . ' ' . $bookingTime)
            ->diffInMinutes(\Carbon\Carbon::parse($request->booking_date . ' ' . $endTime));

        // Kiểm tra trùng buổi với bookings hiện có
        DB::beginTransaction();
        try {
            $assignedTable = null;
            // Nếu có table_id từ request (user đã chọn bàn), kiểm tra bàn đó trước
            if ($request->has('table_id') && $request->table_id) {
                $selectedTable = Table::where('id', $request->table_id)
                    ->where('is_active', true)
                    ->where('status', '!=', 'maintenance')
                    ->first();
                
                if ($selectedTable) {
                    // Kiểm tra capacity
                    if ($selectedTable->capacity >= $request->number_of_guests) {
                        // Kiểm tra xem bàn này có trống trong buổi này không
                        $hasConflict = $this->checkTableSessionConflict(
                            $selectedTable->id,
                            $request->booking_date,
                            $request->session
                        );
                        
                        if (!$hasConflict) {
                            $assignedTable = $selectedTable;
                        }
                    }
                }
            }
            
            // Nếu không có bàn được chọn hoặc bàn đã chọn không khả dụng, tự động gán
            if (!$assignedTable) {
                // Kiểm tra xem có bàn trống trong buổi này không
                $hasAvailableTable = $this->checkAvailableTablesForSession(
                    $request->booking_date,
                    $request->session,
                    $request->number_of_guests
                );

                if (!$hasAvailableTable) {
                    DB::rollBack();
                    $sessionName = SessionHelper::getSessionName($request->session);
                    return back()->withErrors([
                        'session' => "Không có bàn trống trong buổi {$sessionName} này. Tất cả bàn phù hợp đã được đặt. Vui lòng chọn buổi khác."
                    ])->withInput();
                }

                // Tự động gán bàn
                $assignedTable = $this->autoAssignTableForSession(
                    $request->booking_date,
                    $request->session,
                    $request->number_of_guests,
                    $request->location_preference
                );

                if (!$assignedTable) {
                    DB::rollBack();
                    $sessionName = SessionHelper::getSessionName($request->session);
                    return back()->withErrors([
                        'session' => "Không có bàn phù hợp trong buổi {$sessionName} này. Vui lòng chọn buổi khác."
                    ])->withInput();
                }
            }

            // Tạo booking với bàn đã được gán
            $bookingData = [
                'user_id' => auth()->id(),
                'table_id' => $assignedTable->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'booking_date' => $request->booking_date,
                'session' => $request->session,
                'booking_time' => $bookingTime, // Giữ lại để tương thích
                'end_time' => $endTime, // Giữ lại để tương thích
                'duration_minutes' => $durationMinutes, // Giữ lại để tương thích
                'number_of_guests' => $request->number_of_guests,
                'location_preference' => $request->location_preference,
                'notes' => $request->notes,
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ];
            
            \Log::info('Creating booking', $bookingData);
            
            $booking = Booking::create($bookingData);
            
            if (!$booking) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Không thể tạo đặt bàn. Vui lòng thử lại.'])->withInput();
            }
            
            \Log::info('Booking created successfully', ['booking_id' => $booking->id]);

            // Cập nhật trạng thái bàn
            $assignedTable->update(['status' => 'reserved']);

            // Create notification for each staff member
            $staffMembers = \App\Models\User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
            $sessionName = SessionHelper::getSessionName($request->session);
            foreach ($staffMembers as $staff) {
                \App\Models\Notification::create([
                    'user_id' => $staff->id,
                    'type' => 'new_booking',
                    'title' => 'Đặt bàn mới',
                    'message' => "Có đặt bàn mới từ {$request->customer_name} vào {$request->booking_date} buổi {$sessionName}",
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            DB::commit();
            return redirect()->route('bookings.success', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating booking', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi đặt bàn: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function success($id)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->with('table')
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

            // Create notification for each staff member
            $staffMembers = \App\Models\User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
            foreach ($staffMembers as $staff) {
                \App\Models\Notification::create([
                    'user_id' => $staff->id,
                    'type' => 'booking_cancelled',
                    'title' => 'Đặt bàn bị hủy',
                    'message' => "Đặt bàn #{$booking->id} vào " . \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') . " lúc " . \Carbon\Carbon::parse($booking->booking_time)->format('H:i') . " đã bị khách hàng hủy",
                    'notifiable_type' => Booking::class,
                    'notifiable_id' => $booking->id,
                ]);
            }

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Đã hủy đặt bàn thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Kiểm tra xem có bàn nào còn trống trong khung giờ đã chọn không
     * 
     * @param string $bookingDate
     * @param \Carbon\Carbon $startTime
     * @param \Carbon\Carbon $endTime
     * @param int $numberOfGuests
     * @param int $bufferMinutes Buffer time giữa các đặt bàn (15-30 phút)
     * @return bool
     */
    private function checkAvailableTablesForTimeSlot($bookingDate, $startTime, $endTime, $numberOfGuests, $bufferMinutes = 15)
    {
        // Tìm tất cả các bàn phù hợp (capacity >= số khách)
        $suitableTables = Table::where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->where('capacity', '>=', $numberOfGuests)
            ->lockForUpdate() // Lock để tránh race condition
            ->get();

        if ($suitableTables->isEmpty()) {
            return false; // Không có bàn nào phù hợp
        }

        // Logic đơn giản: Kiểm tra từng bàn xem có booking nào trùng khung giờ không
        // Nếu có ít nhất 1 bàn không có booking trùng khung giờ → cho đặt
        foreach ($suitableTables as $table) {
            // Kiểm tra booking đã có table_id = bàn này (đã được gán bàn cụ thể)
            $hasConflict = $this->checkTableTimeConflict(
                $table->id,
                $bookingDate,
                $startTime,
                $endTime,
                $bufferMinutes
            );

            // Nếu bàn này không có xung đột → có thể đặt được
            if (!$hasConflict) {
                return true; // Có ít nhất 1 bàn trống → cho đặt
            }
        }

        // Tất cả bàn đều bị chiếm → không cho đặt
        return false;
    }

    /**
     * Kiểm tra xung đột thời gian cho một bàn cụ thể
     * 
     * @param int $tableId
     * @param string $bookingDate
     * @param \Carbon\Carbon $newStart
     * @param \Carbon\Carbon $newEnd
     * @param int $bufferMinutes
     * @return bool true nếu có xung đột, false nếu không
     */
    private function checkTableTimeConflict($tableId, $bookingDate, $newStart, $newEnd, $bufferMinutes)
    {
        // Tìm các booking đã có trên bàn này trong cùng ngày
        $existingBookings = Booking::where('table_id', $tableId)
            ->whereDate('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->get();

        // Nếu không có booking nào → không có xung đột
        if ($existingBookings->isEmpty()) {
            return false;
        }

        foreach ($existingBookings as $existing) {
            // Parse đúng cách: booking_date là date, booking_time là time
            $existingDate = $existing->booking_date instanceof \Carbon\Carbon 
                ? $existing->booking_date->format('Y-m-d') 
                : $existing->booking_date;
            
            // booking_time có thể là string "18:00:00" hoặc Carbon object
            if ($existing->booking_time instanceof \Carbon\Carbon) {
                $existingTimeStr = $existing->booking_time->format('H:i:s');
            } elseif (is_string($existing->booking_time)) {
                $existingTimeStr = strlen($existing->booking_time) == 5 ? $existing->booking_time . ':00' : $existing->booking_time;
            } else {
                $existingTimeStr = '00:00:00';
            }
            
            $existingStart = \Carbon\Carbon::parse($existingDate . ' ' . $existingTimeStr);
            
            if ($existing->end_time) {
                if ($existing->end_time instanceof \Carbon\Carbon) {
                    $existingEndStr = $existing->end_time->format('H:i:s');
                } elseif (is_string($existing->end_time)) {
                    $existingEndStr = strlen($existing->end_time) == 5 ? $existing->end_time . ':00' : $existing->end_time;
                } else {
                    $existingEndStr = '00:00:00';
                }
                $existingEnd = \Carbon\Carbon::parse($existingDate . ' ' . $existingEndStr);
            } else {
                $existingEnd = $existingStart->copy()->addHours(2);
            }

            // Nếu booking đang checked_in và quá giờ, vẫn tính là đang sử dụng
            if ($existing->status === 'checked_in' && now()->greaterThan($existingEnd)) {
                $actualEnd = now()->addMinutes($bufferMinutes);
            } else {
                // Thêm buffer time sau thời gian kết thúc
                $actualEnd = $existingEnd->copy()->addMinutes($bufferMinutes);
            }

            // Kiểm tra xung đột: hai khung thời gian overlap nếu:
            // newStart < actualEnd AND existingStart < newEnd
            // 
            // Ví dụ:
            // - Existing: 18:00-20:00 (actualEnd = 20:15 với buffer 15 phút)
            // - New: 20:15-22:15 → newStart (20:15) < actualEnd (20:15)? → false → KHÔNG xung đột ✓
            // - New: 19:00-21:00 → newStart (19:00) < actualEnd (20:15)? → true AND existingStart (18:00) < newEnd (21:00)? → true → CÓ xung đột ✓
            // - New: 14:00-16:00 → newStart (14:00) < actualEnd (20:15)? → true AND existingStart (18:00) < newEnd (16:00)? → false → KHÔNG xung đột ✓
            // 
            // Lưu ý: Dùng lessThan (không dùng <=) để cho phép đặt ngay sau buffer time
            // Ví dụ: Booking 18:00-20:00 → actualEnd = 20:15 → Booking mới 20:15-22:15 được phép
            if ($newStart->lessThan($actualEnd) && $existingStart->lessThan($newEnd)) {
                return true; // Có xung đột
            }
        }

        return false; // Không có xung đột
    }

    /**
     * Tự động gán bàn phù hợp cho booking
     * 
     * @param string $bookingDate
     * @param \Carbon\Carbon $startTime
     * @param \Carbon\Carbon $endTime
     * @param int $numberOfGuests
     * @param string|null $locationPreference
     * @param int $bufferMinutes
     * @return Table|null
     */
    private function autoAssignTable($bookingDate, $startTime, $endTime, $numberOfGuests, $locationPreference = null, $bufferMinutes = 15)
    {
        // Tìm bàn phù hợp - không filter theo location_preference quá strict
        // Vì có thể location_preference không match nhưng vẫn có bàn trống
        $query = Table::where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->where('capacity', '>=', $numberOfGuests);

        // Ưu tiên location preference nếu có, nhưng không bắt buộc
        $preferredTables = collect();
        if ($locationPreference) {
            $preferredQuery = clone $query;
            if (str_contains($locationPreference, 'Tầng 1')) {
                $preferredQuery->where('area', 'Tầng 1');
            } elseif (str_contains($locationPreference, 'Tầng 2')) {
                $preferredQuery->where('area', 'Tầng 2');
            } elseif (str_contains($locationPreference, 'Phòng riêng') || str_contains($locationPreference, 'VIP')) {
                $preferredQuery->where('area', 'like', '%VIP%');
            } elseif (str_contains($locationPreference, 'cửa sổ') || str_contains($locationPreference, 'Gần cửa sổ')) {
                $preferredQuery->where('area', 'like', '%cửa sổ%');
            }
            $preferredTables = $preferredQuery->orderBy('capacity', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        }

        // Nếu có preferred tables, kiểm tra chúng trước
        if ($preferredTables->isNotEmpty()) {
            foreach ($preferredTables as $table) {
                $hasConflict = $this->checkTableTimeConflict(
                    $table->id,
                    $bookingDate,
                    $startTime,
                    $endTime,
                    $bufferMinutes
                );

                if (!$hasConflict) {
                    return $table;
                }
            }
        }

        // Nếu không tìm thấy preferred table, tìm tất cả bàn phù hợp
        $allTables = $query->orderBy('capacity', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        foreach ($allTables as $table) {
            $hasConflict = $this->checkTableTimeConflict(
                $table->id,
                $bookingDate,
                $startTime,
                $endTime,
                $bufferMinutes
            );

            // Nếu bàn này không có xung đột → gán bàn này
            if (!$hasConflict) {
                return $table;
            }
        }

        // Không tìm thấy bàn phù hợp
        return null;
    }

    /**
     * Kiểm tra xem có bàn nào còn trống trong buổi đã chọn không
     * 
     * @param string $bookingDate
     * @param string $session morning, lunch, afternoon, dinner
     * @param int $numberOfGuests
     * @return bool
     */
    private function checkAvailableTablesForSession($bookingDate, $session, $numberOfGuests)
    {
        // Tìm tất cả các bàn phù hợp
        $suitableTables = Table::where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->where('capacity', '>=', $numberOfGuests)
            ->lockForUpdate()
            ->get();

        if ($suitableTables->isEmpty()) {
            return false;
        }

        // Kiểm tra từng bàn xem có booking nào trùng buổi không
        foreach ($suitableTables as $table) {
            $hasConflict = $this->checkTableSessionConflict(
                $table->id,
                $bookingDate,
                $session
            );

            if (!$hasConflict) {
                return true; // Có ít nhất 1 bàn trống
            }
        }

        return false; // Tất cả bàn đều bị chiếm
    }

    /**
     * Kiểm tra xung đột buổi cho một bàn cụ thể
     * 
     * @param int $tableId
     * @param string $bookingDate
     * @param string $session
     * @return bool true nếu có xung đột, false nếu không
     */
    private function checkTableSessionConflict($tableId, $bookingDate, $session)
    {
        // Tìm các booking đã có trên bàn này trong cùng ngày và cùng buổi
        $existingBooking = Booking::where('table_id', $tableId)
            ->whereDate('booking_date', $bookingDate)
            ->where('session', $session)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->first();

        // Nếu có booking trùng buổi → có xung đột
        return $existingBooking !== null;
    }

    /**
     * Tự động gán bàn phù hợp cho booking theo buổi
     * 
     * @param string $bookingDate
     * @param string $session
     * @param int $numberOfGuests
     * @param string|null $locationPreference
     * @return Table|null
     */
    private function autoAssignTableForSession($bookingDate, $session, $numberOfGuests, $locationPreference = null)
    {
        // Tìm bàn phù hợp
        $query = Table::where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->where('capacity', '>=', $numberOfGuests);

        // Ưu tiên location preference nếu có
        $preferredTables = collect();
        if ($locationPreference) {
            $preferredQuery = clone $query;
            if (str_contains($locationPreference, 'Tầng 1')) {
                $preferredQuery->where('area', 'Tầng 1');
            } elseif (str_contains($locationPreference, 'Tầng 2')) {
                $preferredQuery->where('area', 'Tầng 2');
            } elseif (str_contains($locationPreference, 'Phòng riêng') || str_contains($locationPreference, 'VIP')) {
                $preferredQuery->where('area', 'like', '%VIP%');
            } elseif (str_contains($locationPreference, 'cửa sổ') || str_contains($locationPreference, 'Gần cửa sổ')) {
                $preferredQuery->where('area', 'like', '%cửa sổ%');
            }
            $preferredTables = $preferredQuery->orderBy('capacity', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        }

        // Kiểm tra preferred tables trước
        if ($preferredTables->isNotEmpty()) {
            foreach ($preferredTables as $table) {
                $hasConflict = $this->checkTableSessionConflict(
                    $table->id,
                    $bookingDate,
                    $session
                );

                if (!$hasConflict) {
                    return $table;
                }
            }
        }

        // Nếu không tìm thấy, tìm tất cả bàn phù hợp
        $allTables = $query->orderBy('capacity', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        foreach ($allTables as $table) {
            $hasConflict = $this->checkTableSessionConflict(
                $table->id,
                $bookingDate,
                $session
            );

            if (!$hasConflict) {
                return $table;
            }
        }

        return null;
    }
}
