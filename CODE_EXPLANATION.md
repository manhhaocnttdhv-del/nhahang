# Giải Thích Chi Tiết Source Code - Hệ Thống Đặt Bàn

## Mục Lục
1. [BookingController.php - Logic Backend](#1-bookingcontrollerphp---logic-backend)
2. [StoreBookingRequest.php - Validation](#2-storebookingrequestphp---validation)
3. [Booking.php - Model](#3-bookingphp---model)
4. [create.blade.php - JavaScript Frontend](#4-createbladephp---javascript-frontend)

---

## 1. BookingController.php - Logic Backend

### 1.1. Method `create()` - Hiển Thị Form Đặt Bàn

```php
public function create(Request $request)
{
    // Lấy ngày được chọn từ request, nếu không có thì dùng hôm nay
    $selectedDate = $request->get('date', today()->format('Y-m-d'));
    
    // Lấy tất cả bàn đang active, kèm theo bookings trong ngày được chọn
    $tables = Table::where('is_active', true)
        ->with(['bookings' => function($query) use ($selectedDate) {
            // Chỉ lấy bookings trong ngày được chọn và có status active
            $query->whereDate('booking_date', $selectedDate)
                ->whereIn('status', ['pending', 'confirmed', 'checked_in']);
        }])
        ->get()
        ->groupBy('area'); // Nhóm bàn theo khu vực (area)
    
    // Lấy tất cả bookings trong ngày để hiển thị thông tin
    $selectedDateBookings = Booking::whereDate('booking_date', $selectedDate)
        ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
        ->with('table') // Eager load table để tránh N+1 query
        ->get();

    return view('bookings.create', compact('tables', 'selectedDateBookings', 'selectedDate'));
}
```

**Giải thích:**
- **Mục đích**: Chuẩn bị dữ liệu để hiển thị form đặt bàn
- **`$selectedDate`**: Cho phép filter bookings theo ngày (qua query string `?date=2025-12-12`)
- **`with(['bookings' => ...])`**: Eager loading để load bookings của mỗi bàn, tránh N+1 query problem
- **`groupBy('area')`**: Nhóm bàn theo khu vực để hiển thị có tổ chức
- **`whereIn('status', ...)`**: Chỉ lấy bookings còn active (pending, confirmed, checked_in), bỏ qua cancelled

---

### 1.2. Method `getBookingsByDate($date)` - API Endpoint

```php
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
                'booking_time' => $booking->booking_time,
                'end_time' => $booking->end_time,
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
```

**Giải thích:**
- **Mục đích**: Cung cấp API endpoint cho JavaScript load bookings động qua AJAX
- **`whereDate('booking_date', $date)`**: Filter bookings theo ngày cụ thể
- **`map()`**: Transform dữ liệu để chỉ trả về các field cần thiết, format chuẩn
- **Xử lý Carbon**: Kiểm tra nếu `booking_date` là Carbon object thì format thành string, nếu không thì dùng trực tiếp
- **Trả về JSON**: Frontend JavaScript sẽ dùng dữ liệu này để kiểm tra xung đột

---

### 1.3. Method `store()` - Xử Lý Đặt Bàn

#### Phần 1: Validation Cơ Bản

```php
// Validate số lượng khách
if ($request->number_of_guests < 1 || $request->number_of_guests > 50) {
    return back()->withErrors(['number_of_guests' => 'Số lượng khách phải từ 1 đến 50 người']);
}
```

**Giải thích:**
- Kiểm tra số lượng khách hợp lệ (1-50 người)
- `back()`: Quay lại trang trước
- `withErrors()`: Gửi kèm thông báo lỗi để hiển thị trong view

#### Phần 2: Parse và Validate Thời Gian

```php
try {
    // Parse booking_date + booking_time thành Carbon object
    $bookingDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->booking_time);
    $endDateTime = \Carbon\Carbon::parse($request->booking_date . ' ' . $request->end_time);
} catch (\Exception $e) {
    return back()->withErrors(['booking_time' => 'Thời gian không hợp lệ'])->withInput();
}

// Kiểm tra end_time phải sau start_time
if ($endDateTime->lte($bookingDateTime)) {
    return back()->withErrors(['end_time' => 'Thời gian kết thúc phải sau thời gian bắt đầu'])->withInput();
}

// Không cho đặt bàn trong quá khứ
if ($bookingDateTime->isPast()) {
    return back()->withErrors(['booking_date' => 'Không thể đặt bàn trong quá khứ'])->withInput();
}
```

**Giải thích:**
- **`Carbon::parse()`**: Chuyển đổi string thành Carbon object để tính toán thời gian
- **`lte()`**: Less Than or Equal - kiểm tra `end_time` có <= `start_time` không
- **`isPast()`**: Kiểm tra thời gian có trong quá khứ không
- **`withInput()`**: Giữ lại dữ liệu đã nhập để người dùng không phải nhập lại

#### Phần 3: Validate Duration

```php
// Tính thời lượng bằng phút
$durationMinutes = $bookingDateTime->diffInMinutes($endDateTime);

if ($durationMinutes < 30) {
    return back()->withErrors(['end_time' => 'Thời gian đặt bàn tối thiểu là 30 phút'])->withInput();
}
if ($durationMinutes > 240) {
    return back()->withErrors(['end_time' => 'Thời gian đặt bàn tối đa là 4 giờ'])->withInput();
}
```

**Giải thích:**
- **`diffInMinutes()`**: Tính khoảng cách giữa 2 thời điểm bằng phút
- **30 phút**: Thời lượng tối thiểu (để đảm bảo khách có đủ thời gian)
- **240 phút = 4 giờ**: Thời lượng tối đa (giới hạn thời gian sử dụng bàn)

#### Phần 4: Validate Business Hours

```php
$bookingHour = $bookingDateTime->hour;
$endHour = $endDateTime->hour;
if ($bookingHour < 8 || $endHour > 22) {
    return back()->withErrors(['booking_time' => 'Giờ đặt bàn phải từ 8:00 đến 22:00'])->withInput();
}
```

**Giải thích:**
- **`->hour`**: Lấy giờ từ Carbon object (0-23)
- **8:00 - 22:00**: Giờ hoạt động của nhà hàng
- Kiểm tra cả `bookingHour` (bắt đầu) và `endHour` (kết thúc)

#### Phần 5: Kiểm Tra Xung Đột và Tự Động Gán Bàn

```php
$bufferMinutes = 15; // Buffer 15 phút để dọn dẹp

DB::beginTransaction(); // Bắt đầu transaction
try {
    // Bước 1: Kiểm tra có bàn trống không
    $hasAvailableTable = $this->checkAvailableTablesForTimeSlot(
        $request->booking_date,
        $bookingDateTime,
        $endDateTime,
        $request->number_of_guests,
        $bufferMinutes
    );

    if (!$hasAvailableTable) {
        DB::rollBack();
        return back()->withErrors([
            'booking_time' => 'Không có bàn trống trong khung giờ này...'
        ])->withInput();
    }

    // Bước 2: Tự động gán bàn
    $assignedTable = $this->autoAssignTable(
        $request->booking_date,
        $bookingDateTime,
        $endDateTime,
        $request->number_of_guests,
        $request->location_preference,
        $bufferMinutes
    );

    if (!$assignedTable) {
        DB::rollBack();
        return back()->withErrors([
            'booking_time' => 'Không có bàn phù hợp...'
        ])->withInput();
    }

    // Bước 3: Tạo booking
    $booking = Booking::create([...]);

    // Bước 4: Cập nhật trạng thái bàn
    $assignedTable->update(['status' => 'reserved']);

    // Bước 5: Tạo notification cho staff
    // ...

    DB::commit(); // Commit transaction
    return redirect()->route('bookings.success', $booking->id);
} catch (\Exception $e) {
    DB::rollBack(); // Rollback nếu có lỗi
    return back()->withErrors(['error' => 'Có lỗi xảy ra...'])->withInput();
}
```

**Giải thích:**
- **`DB::beginTransaction()`**: Bắt đầu transaction để đảm bảo tính nhất quán dữ liệu
- **Buffer 15 phút**: Thời gian dọn dẹp giữa các đặt bàn
- **2 bước kiểm tra**: 
  1. `checkAvailableTablesForTimeSlot()`: Kiểm tra có bàn trống không
  2. `autoAssignTable()`: Tự động gán bàn cụ thể
- **Transaction**: Nếu có lỗi ở bất kỳ bước nào, rollback để không lưu dữ liệu không nhất quán
- **Status 'confirmed'**: Vì đã gán bàn nên tự động confirmed, không cần staff xác nhận

---

### 1.4. Method `checkAvailableTablesForTimeSlot()` - Kiểm Tra Có Bàn Trống

```php
private function checkAvailableTablesForTimeSlot($bookingDate, $startTime, $endTime, $numberOfGuests, $bufferMinutes = 15)
{
    // Tìm tất cả bàn phù hợp
    $suitableTables = Table::where('is_active', true)
        ->where('status', '!=', 'maintenance')
        ->where('capacity', '>=', $numberOfGuests)
        ->lockForUpdate() // Lock để tránh race condition
        ->get();

    if ($suitableTables->isEmpty()) {
        return false; // Không có bàn nào phù hợp
    }

    // Kiểm tra từng bàn xem có xung đột không
    foreach ($suitableTables as $table) {
        $hasConflict = $this->checkTableTimeConflict(
            $table->id,
            $bookingDate,
            $startTime,
            $endTime,
            $bufferMinutes
        );

        // Nếu bàn này không có xung đột → có thể đặt được
        if (!$hasConflict) {
            return true; // Có ít nhất 1 bàn trống
        }
    }

    return false; // Tất cả bàn đều bị chiếm
}
```

**Giải thích:**
- **Mục đích**: Kiểm tra xem có ít nhất 1 bàn trống trong khung giờ không
- **`where('capacity', '>=', $numberOfGuests)`**: Chỉ lấy bàn có sức chứa >= số khách
- **`lockForUpdate()`**: Lock bàn trong database để tránh race condition (2 người cùng đặt 1 bàn)
- **Logic**: Nếu có ít nhất 1 bàn không có xung đột → return true (cho phép đặt)
- **Return false**: Nếu tất cả bàn đều có xung đột → không cho đặt

---

### 1.5. Method `checkTableTimeConflict()` - Kiểm Tra Xung Đột Thời Gian

```php
private function checkTableTimeConflict($tableId, $bookingDate, $newStart, $newEnd, $bufferMinutes)
{
    // Lấy tất cả bookings của bàn này trong cùng ngày
    $existingBookings = Booking::where('table_id', $tableId)
        ->whereDate('booking_date', $bookingDate)
        ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
        ->get();

    if ($existingBookings->isEmpty()) {
        return false; // Không có booking nào → không xung đột
    }

    foreach ($existingBookings as $existing) {
        // Parse booking_date
        $existingDate = $existing->booking_date instanceof \Carbon\Carbon 
            ? $existing->booking_date->format('Y-m-d') 
            : $existing->booking_date;
        
        // Parse booking_time (hỗ trợ cả string và Carbon)
        if ($existing->booking_time instanceof \Carbon\Carbon) {
            $existingTimeStr = $existing->booking_time->format('H:i:s');
        } elseif (is_string($existing->booking_time)) {
            // Nếu là "18:00" thì thêm ":00" thành "18:00:00"
            $existingTimeStr = strlen($existing->booking_time) == 5 
                ? $existing->booking_time . ':00' 
                : $existing->booking_time;
        } else {
            $existingTimeStr = '00:00:00';
        }
        
        $existingStart = \Carbon\Carbon::parse($existingDate . ' ' . $existingTimeStr);
        
        // Parse end_time tương tự
        if ($existing->end_time) {
            // ... parse end_time ...
            $existingEnd = \Carbon\Carbon::parse($existingDate . ' ' . $existingEndStr);
        } else {
            // Nếu không có end_time, mặc định 2 giờ
            $existingEnd = $existingStart->copy()->addHours(2);
        }

        // Xử lý đặc biệt: nếu checked_in và quá giờ
        if ($existing->status === 'checked_in' && now()->greaterThan($existingEnd)) {
            $actualEnd = now()->addMinutes($bufferMinutes);
        } else {
            // Thêm buffer time sau thời gian kết thúc
            $actualEnd = $existingEnd->copy()->addMinutes($bufferMinutes);
        }

        // Kiểm tra xung đột: newStart < actualEnd AND existingStart < newEnd
        if ($newStart->lessThan($actualEnd) && $existingStart->lessThan($newEnd)) {
            return true; // CÓ xung đột
        }
    }

    return false; // Không có xung đột
}
```

**Giải thích:**

**Parse thời gian:**
- Hỗ trợ cả Carbon object và string
- Format "18:00" → "18:00:00" để parse đúng
- Nếu không có `end_time`, mặc định 2 giờ

**Xử lý đặc biệt:**
- Nếu booking đang `checked_in` và quá giờ → tính `actualEnd = now() + buffer`
- Điều này đảm bảo bàn vẫn được tính là đang sử dụng nếu khách chưa rời

**Logic kiểm tra xung đột:**
```
Hai khung thời gian overlap nếu:
newStart < actualEnd AND existingStart < newEnd
```

**Ví dụ:**
- Existing: 18:00-20:00 (actualEnd = 20:15 với buffer 15 phút)
- New: 20:15-22:15 → `20:15 < 20:15?` → false → **KHÔNG xung đột** ✓
- New: 19:00-21:00 → `19:00 < 20:15?` true AND `18:00 < 21:00?` true → **CÓ xung đột** ✗
- New: 14:00-16:00 → `14:00 < 20:15?` true BUT `18:00 < 16:00?` false → **KHÔNG xung đột** ✓

---

### 1.6. Method `autoAssignTable()` - Tự Động Gán Bàn

```php
private function autoAssignTable($bookingDate, $startTime, $endTime, $numberOfGuests, $locationPreference = null, $bufferMinutes = 15)
{
    // Query cơ bản: tìm bàn phù hợp
    $query = Table::where('is_active', true)
        ->where('status', '!=', 'maintenance')
        ->where('capacity', '>=', $numberOfGuests);

    // Ưu tiên location preference nếu có
    $preferredTables = collect();
    if ($locationPreference) {
        $preferredQuery = clone $query;
        
        // Map location preference thành area filter
        if (str_contains($locationPreference, 'Tầng 1')) {
            $preferredQuery->where('area', 'Tầng 1');
        } elseif (str_contains($locationPreference, 'Tầng 2')) {
            $preferredQuery->where('area', 'Tầng 2');
        } elseif (str_contains($locationPreference, 'Phòng riêng') || str_contains($locationPreference, 'VIP')) {
            $preferredQuery->where('area', 'like', '%VIP%');
        } elseif (str_contains($locationPreference, 'cửa sổ') || str_contains($locationPreference, 'Gần cửa sổ')) {
            $preferredQuery->where('area', 'like', '%cửa sổ%');
        }
        
        // Ưu tiên bàn nhỏ hơn (tối ưu sử dụng)
        $preferredTables = $preferredQuery->orderBy('capacity', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    // Bước 1: Kiểm tra preferred tables trước
    if ($preferredTables->isNotEmpty()) {
        foreach ($preferredTables as $table) {
            $hasConflict = $this->checkTableTimeConflict(...);
            if (!$hasConflict) {
                return $table; // Tìm thấy bàn ưu tiên không xung đột
            }
        }
    }

    // Bước 2: Nếu không tìm thấy, tìm tất cả bàn phù hợp
    $allTables = $query->orderBy('capacity', 'asc')
        ->orderBy('name', 'asc')
        ->get();

    foreach ($allTables as $table) {
        $hasConflict = $this->checkTableTimeConflict(...);
        if (!$hasConflict) {
            return $table; // Tìm thấy bàn không xung đột
        }
    }

    return null; // Không tìm thấy bàn phù hợp
}
```

**Giải thích:**

**Logic gán bàn:**
1. **Ưu tiên location preference**: Nếu có, tìm bàn trong khu vực ưa thích trước
2. **Tối ưu sử dụng**: `orderBy('capacity', 'asc')` - ưu tiên bàn nhỏ hơn để tối ưu
3. **Fallback**: Nếu không tìm thấy bàn ưu tiên, tìm tất cả bàn phù hợp

**Ví dụ:**
- Khách muốn "Tầng 1" → tìm bàn ở Tầng 1 trước
- Nếu không có bàn Tầng 1 trống → tìm bàn khác
- Ưu tiên bàn 4 người thay vì bàn 8 người (nếu khách chỉ 3 người)

---

## 2. StoreBookingRequest.php - Validation

```php
public function rules(): array
{
    return [
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
        'booking_date' => 'required|date|after_or_equal:today',
        'booking_time' => 'required|date_format:H:i',
        'end_time' => [
            'required',
            'date_format:H:i',
            function ($attribute, $value, $fail) {
                // Custom validation logic
                $bookingTime = $this->input('booking_time');
                $bookingDate = $this->input('booking_date');
                
                if (!$bookingTime || !$bookingDate) {
                    return; // Skip validation nếu thiếu dữ liệu
                }
                
                try {
                    $start = \Carbon\Carbon::parse($bookingDate . ' ' . $bookingTime);
                    $end = \Carbon\Carbon::parse($bookingDate . ' ' . $value);
                    
                    // Kiểm tra end_time phải sau start_time
                    if ($end->lte($start)) {
                        $fail('Thời gian kết thúc phải sau thời gian bắt đầu.');
                        return;
                    }
                    
                    // Kiểm tra duration
                    $durationMinutes = $start->diffInMinutes($end);
                    if ($durationMinutes < 30) {
                        $fail('Thời gian đặt bàn tối thiểu là 30 phút.');
                        return;
                    }
                    if ($durationMinutes > 240) {
                        $fail('Thời gian đặt bàn tối đa là 4 giờ.');
                        return;
                    }
                } catch (\Exception $e) {
                    $fail('Thời gian không hợp lệ.');
                }
            },
        ],
        'number_of_guests' => 'required|integer|min:1|max:50',
        'location_preference' => 'nullable|string|max:500',
        'notes' => 'nullable|string|max:1000',
    ];
}
```

**Giải thích:**

**Standard Rules:**
- `required`: Bắt buộc phải có
- `string|max:255`: Kiểu string, tối đa 255 ký tự
- `date|after_or_equal:today`: Phải là ngày hợp lệ và >= hôm nay
- `date_format:H:i`: Format phải là "HH:MM" (ví dụ: "18:00")

**Custom Validation:**
- Closure function cho phép validation phức tạp
- `$fail()`: Gọi khi validation fail, truyền message lỗi
- Kiểm tra logic nghiệp vụ: duration 30 phút - 4 giờ

**Lợi ích:**
- Validation ở tầng Request, giảm tải Controller
- Thông báo lỗi rõ ràng bằng tiếng Việt
- Tự động return 422 nếu validation fail

---

## 3. Booking.php - Model

```php
protected $fillable = [
    'user_id',
    'table_id',
    'customer_name',
    'customer_phone',
    'booking_date',
    'booking_time',
    'end_time',              // ← MỚI
    'duration_minutes',       // ← MỚI
    'number_of_guests',
    'location_preference',
    'notes',
    'status',
    'confirmed_by',
    'confirmed_at',
];

protected $casts = [
    'booking_date' => 'date',      // Tự động cast thành Carbon date
    'booking_time' => 'string',     // Giữ nguyên string "18:00"
    'end_time' => 'string',        // Giữ nguyên string "20:00"
    'confirmed_at' => 'datetime',  // Tự động cast thành Carbon datetime
];
```

**Giải thích:**

**`$fillable`:**
- Danh sách các field có thể mass assign
- Bảo vệ khỏi mass assignment vulnerability
- Thêm `end_time` và `duration_minutes` để có thể tạo booking với các field này

**`$casts`:**
- **`'booking_date' => 'date'`**: Tự động convert string → Carbon date object
- **`'booking_time' => 'string'`**: Giữ nguyên string để dễ format "H:i"
- **`'end_time' => 'string'`**: Tương tự booking_time
- **`'confirmed_at' => 'datetime'`**: Tự động convert → Carbon datetime

**Relationships:**
```php
public function user() {
    return $this->belongsTo(User::class);
}

public function table() {
    return $this->belongsTo(Table::class);
}
```

- **`belongsTo`**: Booking thuộc về 1 User và 1 Table
- Cho phép `$booking->user` và `$booking->table`

---

## 4. create.blade.php - JavaScript Frontend

### 4.1. Function `calculateEndTimeModal()` - Tự Động Tính End Time

```javascript
function calculateEndTimeModal() {
    const bookingTime = $('#modal_booking_time').val(); // "18:00"
    const duration = parseInt($('#modal_duration').val()) || 120; // 120 phút
    
    if (!bookingTime) {
        return; // Không có booking_time thì không tính
    }
    
    // Chuyển đổi "18:00" → 1080 phút (18 * 60)
    const [hours, minutes] = bookingTime.split(':').map(Number);
    const startMinutes = hours * 60 + minutes;
    
    // Tính end_time: 1080 + 120 = 1200 phút
    const endMinutes = startMinutes + duration;
    const endHours = Math.floor(endMinutes / 60); // 20
    const endMins = endMinutes % 60; // 0
    
    // Đảm bảo không vượt quá 22:00
    if (endHours > 22 || (endHours === 22 && endMins > 0)) {
        $('#modal_end_time').val('22:00');
        // Điều chỉnh duration nếu cần
        const maxEndMinutes = 22 * 60; // 1320
        const adjustedDuration = maxEndMinutes - startMinutes;
        if (adjustedDuration >= 30) {
            $('#modal_duration').val(adjustedDuration);
        }
    } else {
        // Format lại thành "20:00"
        const endTimeStr = String(endHours).padStart(2, '0') + ':' + 
                          String(endMins).padStart(2, '0');
        $('#modal_end_time').val(endTimeStr);
    }
    
    // Load lại bookings khi thời gian thay đổi
    loadTableBookings();
}
```

**Giải thích:**
- **Mục đích**: Tự động tính `end_time` từ `booking_time` + `duration`
- **`split(':').map(Number)`**: Parse "18:00" → [18, 0]
- **`padStart(2, '0')`**: Đảm bảo format "20:00" không phải "20:0"
- **Giới hạn 22:00**: Nếu vượt quá, đặt về 22:00 và điều chỉnh duration
- **Tự động load bookings**: Khi thời gian thay đổi, load lại danh sách bookings

---

### 4.2. Function `loadTableBookings()` - Hiển Thị Bookings Của Bàn

```javascript
function loadTableBookings() {
    const tableId = $('#selected_table_id').val();
    const bookingDate = $('#modal_booking_date').val() || '{{ date('Y-m-d') }}';
    
    if (!tableId) {
        $('#tableBookingsContent').html('Chưa chọn bàn');
        return;
    }
    
    // Lấy bookings từ window.currentBookings (đã load qua AJAX)
    const bookings = window.currentBookings || [];
    
    // Normalize date format để so sánh
    const normalizeDate = function(dateStr) {
        if (!dateStr) return '';
        if (dateStr.includes('T')) {
            return dateStr.split('T')[0]; // "2025-12-12T00:00:00" → "2025-12-12"
        }
        return dateStr;
    };
    
    // Filter bookings của bàn này trong ngày này
    const tableBookings = bookings.filter(function(booking) {
        const bookingDateNormalized = normalizeDate(booking.booking_date);
        const selectedDateNormalized = normalizeDate(bookingDate);
        const matchesDate = bookingDateNormalized === selectedDateNormalized;
        
        const bookingTableId = booking.table_id || (booking.table ? booking.table.id : null);
        const matchesTable = bookingTableId && bookingTableId == tableId;
        
        return matchesDate && matchesTable;
    });
    
    // Hiển thị kết quả
    if (tableBookings.length === 0) {
        $('#tableBookingsContent').html('Bàn này chưa có đặt bàn nào');
    } else {
        let html = '<div class="list-group">';
        tableBookings.forEach(function(booking) {
            const statusBadge = booking.status === 'pending' 
                ? '<span class="badge bg-warning">Chờ xác nhận</span>'
                : booking.status === 'confirmed'
                ? '<span class="badge bg-success">Đã xác nhận</span>'
                : '<span class="badge bg-info">Đã đến</span>';
            
            const timeRange = booking.booking_time.substring(0, 5) + 
                            (booking.end_time ? ' - ' + booking.end_time.substring(0, 5) : '');
            
            html += `<div class="list-group-item">
                <strong>${booking.customer_name}</strong>
                <small>${timeRange}</small>
                ${statusBadge}
            </div>`;
        });
        html += '</div>';
        $('#tableBookingsContent').html(html);
    }
}
```

**Giải thích:**
- **Mục đích**: Hiển thị danh sách bookings của bàn đã chọn trong ngày đã chọn
- **`window.currentBookings`**: Dữ liệu đã load qua AJAX, lưu trong global variable
- **Normalize date**: Xử lý format date khác nhau (có thể có "T" hoặc không)
- **Filter**: Lọc bookings theo `table_id` và `booking_date`
- **Render HTML**: Tạo HTML động để hiển thị danh sách với status badges

---

### 4.3. Function `checkModalTimeConflicts()` - Kiểm Tra Xung Đột Real-time

```javascript
function checkModalTimeConflicts() {
    const bookingDate = $('#modal_booking_date').val();
    const bookingTime = $('#modal_booking_time').val();
    const endTime = $('#modal_end_time').val();
    const selectedTableId = $('#selected_table_id').val();
    
    if (!bookingDate || !bookingTime || !endTime) {
        $('#conflictInfo').hide();
        $('#submitBookingBtn').prop('disabled', false);
        return;
    }
    
    const bookings = window.currentBookings || [];
    const bufferMinutes = 15;
    
    // Convert time to minutes
    function timeToMinutes(timeStr) {
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }
    
    const selectedStart = timeToMinutes(bookingTime); // 1080 (18:00)
    const selectedEnd = timeToMinutes(endTime);       // 1200 (20:00)
    
    // Find conflicting bookings
    const conflictingBookings = [];
    bookings.forEach(function(booking) {
        if (booking.booking_date !== bookingDate) return;
        
        // Chỉ kiểm tra với bàn đã chọn
        if (selectedTableId && booking.table && booking.table.id != selectedTableId) {
            return; // Skip bookings from other tables
        }
        
        const bookingStart = timeToMinutes(booking.booking_time.substring(0, 5));
        const bookingEnd = booking.end_time 
            ? timeToMinutes(booking.end_time.substring(0, 5)) 
            : bookingStart + 120; // Default 2 hours
        const bookingEndWithBuffer = bookingEnd + bufferMinutes;
        
        // Check conflict: selectedStart < bookingEndWithBuffer && bookingStart < selectedEnd
        if (selectedStart < bookingEndWithBuffer && bookingStart < selectedEnd) {
            conflictingBookings.push(booking);
        }
    });
    
    // Display result
    if (conflictingBookings.length > 0) {
        // Show warning and disable button
        $('#conflictInfo').addClass('alert-warning').fadeIn();
        $('#submitBookingBtn').prop('disabled', true)
            .html('Không thể đặt (trùng khung giờ)');
    } else {
        // Show success info and enable button
        $('#conflictInfo').removeClass('alert-warning').addClass('alert-success').fadeIn();
        $('#submitBookingBtn').prop('disabled', false)
            .html('Đặt Bàn Ngay');
    }
}
```

**Giải thích:**
- **Mục đích**: Kiểm tra xung đột real-time khi người dùng chọn thời gian
- **Logic giống backend**: Sử dụng cùng logic kiểm tra xung đột
- **Buffer 15 phút**: Giống backend
- **Disable button**: Nếu có xung đột, disable nút submit
- **Real-time feedback**: Hiển thị cảnh báo ngay khi có xung đột

---

### 4.4. Function `loadBookingsForDate()` - Load Bookings Qua AJAX

```javascript
function loadBookingsForDate(date) {
    $.ajax({
        url: '/bookings/date/' + date,
        method: 'GET',
        success: function(bookings) {
            // Lưu vào global variable để các function khác dùng
            window.currentBookings = bookings;
            
            // Load lại danh sách bookings của bàn
            loadTableBookings();
            
            // Kiểm tra xung đột
            checkModalTimeConflicts();
        },
        error: function() {
            console.error('Error loading bookings');
            window.currentBookings = [];
            loadTableBookings();
        }
    });
}
```

**Giải thích:**
- **Mục đích**: Load bookings của ngày được chọn qua AJAX
- **AJAX call**: Gọi API endpoint `/bookings/date/{date}`
- **Lưu global**: Lưu vào `window.currentBookings` để các function khác dùng
- **Auto refresh**: Tự động gọi `loadTableBookings()` và `checkModalTimeConflicts()`

---

### 4.5. Event Handler - Click Bàn

```javascript
$(document).on('click', '.table-card', function(e) {
    const $card = $(this);
    
    // Không cho click vào bàn maintenance
    if ($card.hasClass('table-maintenance')) {
        return;
    }
    
    e.preventDefault();
    e.stopPropagation();
    
    // Lấy thông tin bàn
    const tableName = $card.find('h6').text();
    const capacity = $card.data('capacity');
    const tableId = $card.data('table-id');
    
    // Fill form bên phải
    $('#number_of_guests').val(capacity);
    $('#selectedTableName').text(tableName);
    $('#selectedTableInfo').fadeIn();
    
    // Fill modal
    $('#modalTableName').text(tableName);
    $('#selected_table_id').val(tableId);
    $('#modal_number_of_guests').val(capacity);
    
    // Reset date/time
    const today = new Date().toISOString().split('T')[0];
    $('#modal_booking_date').val(today);
    $('#modal_booking_time').val('18:00');
    $('#modal_duration').val('120');
    calculateEndTimeModal();
    
    // Load bookings for today
    loadBookingsForDate(today);
    
    // Open modal
    const modalElement = document.getElementById('bookingModal');
    const bookingModal = new bootstrap.Modal(modalElement);
    bookingModal.show();
});
```

**Giải thích:**
- **Event delegation**: `$(document).on('click', '.table-card', ...)` - hoạt động với cả element được thêm động
- **Lấy thông tin**: Từ data attributes (`data-capacity`, `data-table-id`)
- **Fill form**: Điền thông tin vào form bên phải và modal
- **Reset**: Reset date/time về mặc định (hôm nay, 18:00, 2 giờ)
- **Load bookings**: Load bookings của hôm nay
- **Open modal**: Mở Bootstrap modal

---

## Tổng Kết

### Flow Hoàn Chỉnh:

1. **User click bàn** → JavaScript fill form và mở modal
2. **User chọn ngày/giờ** → JavaScript tính `end_time` và kiểm tra xung đột
3. **User submit** → Form gửi request đến `BookingController@store`
4. **Backend validate** → `StoreBookingRequest` kiểm tra dữ liệu
5. **Backend kiểm tra xung đột** → `checkAvailableTablesForTimeSlot()` và `checkTableTimeConflict()`
6. **Backend gán bàn** → `autoAssignTable()` tự động gán bàn phù hợp
7. **Backend tạo booking** → Lưu vào database với status `confirmed`
8. **Backend tạo notification** → Thông báo cho staff
9. **Redirect** → Chuyển đến trang success

### Điểm Mạnh:

- ✅ **Validation 2 tầng**: Frontend (real-time) + Backend (bảo mật)
- ✅ **Transaction**: Đảm bảo data integrity
- ✅ **Lock tables**: Tránh race condition
- ✅ **Buffer time**: Đảm bảo thời gian dọn dẹp
- ✅ **Auto assign**: Tự động gán bàn, không cần staff xác nhận
- ✅ **Real-time feedback**: Hiển thị xung đột ngay lập tức

---

*Tài liệu này giải thích chi tiết cách code hoạt động trong hệ thống đặt bàn.*

