# Tóm Tắt Implementation - Quản Lý Lương, Nguyên Liệu và Đặt Bàn Theo Buổi

## Đã Hoàn Thành

### 1. Quản Lý Lương (Full-time/Part-time)

#### Migration:
- ✅ `create_salaries_table.php` - Bảng lương với các trường:
  - `employment_type`: full_time/part_time
  - `period_start`, `period_end`: Kỳ lương
  - `base_salary`: Lương cơ bản (full-time)
  - `working_hours`, `hourly_rate`: Giờ làm và lương/giờ (part-time)
  - `overtime_hours`, `overtime_rate`: Làm thêm
  - `bonus`, `deduction`: Thưởng và khấu trừ
  - `total_salary`: Tổng lương
  - `status`: pending/approved/paid

- ✅ `add_employment_type_to_users_table.php` - Thêm vào bảng users:
  - `employment_type`: full_time/part_time
  - `base_salary`: Lương cơ bản/tháng
  - `hourly_rate`: Lương/giờ

#### Model:
- ✅ `Salary.php` - Model với:
  - Relationships: `user()`, `approvedBy()`
  - Method `calculateTotal()`: Tự động tính tổng lương

- ✅ `User.php` - Cập nhật:
  - Thêm `salaries()` relationship
  - Methods: `isFullTime()`, `isPartTime()`

### 2. Quản Lý Nguyên Liệu

#### Migration:
- ✅ `create_ingredients_table.php` - Bảng nguyên liệu:
  - `name`, `code`: Tên và mã nguyên liệu
  - `unit`: Đơn vị tính (kg, lít, gói...)
  - `unit_price`: Giá mua/đơn vị
  - `min_stock`, `max_stock`: Tồn kho min/max
  - `status`: active/inactive

- ✅ `create_ingredient_stocks_table.php` - Bảng nhập/xuất:
  - `type`: import/export/adjustment
  - `quantity`: Số lượng
  - `unit_price`, `total_amount`: Giá và tổng tiền
  - `stock_date`: Ngày nhập/xuất

#### Model:
- ✅ `Ingredient.php` - Model với:
  - Relationship: `stocks()`
  - Methods: `getCurrentStock()`, `isLowStock()`, `isOverStock()`

- ✅ `IngredientStock.php` - Model với:
  - Relationships: `ingredient()`, `createdBy()`
  - Auto calculate `total_amount` trong boot()

### 3. Đặt Bàn Theo Buổi (Sáng/Trưa/Chiều/Tối)

#### Migration:
- ✅ `change_booking_to_sessions_table.php` - Thêm trường `session` vào bảng bookings:
  - `session`: enum('morning', 'lunch', 'afternoon', 'dinner')

#### Helper:
- ✅ `SessionHelper.php` - Helper class với:
  - `getSessionTimeRange()`: Map session → time range
  - `getSessionName()`: Tên buổi bằng tiếng Việt
  - `getAllSessions()`: Danh sách tất cả buổi

#### Request:
- ✅ `StoreBookingRequest.php` - Cập nhật validation:
  - Thay `booking_time`, `end_time` → `session`
  - Validation: `session` required, in: morning,lunch,afternoon,dinner

#### Controller:
- ✅ `BookingController.php` - Cập nhật:
  - Method `store()`: Sử dụng session thay vì time
  - Method `checkAvailableTablesForSession()`: Kiểm tra có bàn trống trong buổi
  - Method `checkTableSessionConflict()`: Kiểm tra trùng buổi
  - Method `autoAssignTableForSession()`: Tự động gán bàn theo buổi

#### Model:
- ✅ `Booking.php` - Thêm `session` vào `$fillable`

## Cần Làm Tiếp

### 1. Cập Nhật Composer Autoload
Thêm vào `composer.json`:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/SessionHelper.php"
    ]
}
```
Sau đó chạy: `composer dump-autoload`

### 2. Cập Nhật View Đặt Bàn
File: `resources/views/bookings/create.blade.php`

**Thay đổi cần làm:**
- Thay time picker → session selector (radio buttons hoặc dropdown)
- Hiển thị 4 buổi: Sáng (8:00-11:00), Trưa (11:00-14:00), Chiều (14:00-17:00), Tối (17:00-22:00)
- Cập nhật JavaScript để:
  - Load bookings theo session thay vì time
  - Check conflict theo session
  - Hiển thị bookings đã đặt trong buổi

**Ví dụ code cần thêm:**
```blade
<div class="mb-3">
    <label class="form-label fw-bold">Buổi <span class="text-danger">*</span></label>
    <div class="row g-2">
        <div class="col-md-6">
            <input type="radio" name="session" id="session_morning" value="morning" class="btn-check" required>
            <label class="btn btn-outline-primary w-100" for="session_morning">
                <i class="bi bi-sunrise"></i> Sáng<br>
                <small>8:00 - 11:00</small>
            </label>
        </div>
        <div class="col-md-6">
            <input type="radio" name="session" id="session_lunch" value="lunch" class="btn-check" required>
            <label class="btn btn-outline-primary w-100" for="session_lunch">
                <i class="bi bi-sun"></i> Trưa<br>
                <small>11:00 - 14:00</small>
            </label>
        </div>
        <div class="col-md-6">
            <input type="radio" name="session" id="session_afternoon" value="afternoon" class="btn-check" required>
            <label class="btn btn-outline-primary w-100" for="session_afternoon">
                <i class="bi bi-cloud-sun"></i> Chiều<br>
                <small>14:00 - 17:00</small>
            </label>
        </div>
        <div class="col-md-6">
            <input type="radio" name="session" id="session_dinner" value="dinner" class="btn-check" required>
            <label class="btn btn-outline-primary w-100" for="session_dinner">
                <i class="bi bi-moon"></i> Tối<br>
                <small>17:00 - 22:00</small>
            </label>
        </div>
    </div>
</div>
```

### 3. Tạo Controllers và Views Cho Quản Lý Lương
- `SalaryController.php` - CRUD lương
- Views: `admin/salaries/index.blade.php`, `create.blade.php`, `edit.blade.php`
- Logic tính lương:
  - Full-time: `base_salary + overtime + bonus - deduction`
  - Part-time: `working_hours * hourly_rate + overtime + bonus - deduction`

### 4. Tạo Controllers và Views Cho Quản Lý Nguyên Liệu
- `IngredientController.php` - CRUD nguyên liệu
- `IngredientStockController.php` - Nhập/xuất nguyên liệu
- Views: `admin/ingredients/`, `admin/ingredient-stocks/`
- Tính tồn kho tự động
- Cảnh báo khi tồn kho thấp

### 5. Cập Nhật Routes
Thêm vào `routes/web.php`:
```php
// Admin routes - Salaries
Route::prefix('salaries')->name('salaries.')->group(function () {
    Route::get('/', [SalaryController::class, 'index'])->name('index');
    Route::get('/create', [SalaryController::class, 'create'])->name('create');
    Route::post('/', [SalaryController::class, 'store'])->name('store');
    // ...
});

// Admin routes - Ingredients
Route::prefix('ingredients')->name('ingredients.')->group(function () {
    Route::get('/', [IngredientController::class, 'index'])->name('index');
    // ...
});
```

### 6. Chạy Migrations
```bash
php artisan migrate
```

## Lưu Ý

1. **Tương thích ngược**: Giữ lại `booking_time`, `end_time`, `duration_minutes` trong bảng bookings để tương thích với dữ liệu cũ
2. **Session mapping**: 
   - morning: 8:00-11:00
   - lunch: 11:00-14:00
   - afternoon: 14:00-17:00
   - dinner: 17:00-22:00
3. **Check trùng**: Kiểm tra trùng buổi (cùng ngày + cùng buổi + cùng bàn)
4. **Auto assign**: Tự động gán bàn khi đặt, ưu tiên location preference

## Files Đã Tạo/Sửa

### Migrations:
- `2025_12_15_140442_create_salaries_table.php`
- `2025_12_15_140446_create_ingredients_table.php`
- `2025_12_15_140449_create_ingredient_stocks_table.php`
- `2025_12_15_140451_add_employment_type_to_users_table.php`
- `2025_12_15_140454_change_booking_to_sessions_table.php`

### Models:
- `Salary.php`
- `Ingredient.php`
- `IngredientStock.php`
- `User.php` (updated)
- `Booking.php` (updated)

### Helpers:
- `app/Helpers/SessionHelper.php`

### Controllers:
- `BookingController.php` (updated)

### Requests:
- `StoreBookingRequest.php` (updated)

