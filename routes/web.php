<?php

use App\Http\Controllers\Web\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Web\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Web\Admin\IngredientStockController as AdminIngredientStockController;
use App\Http\Controllers\Web\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Web\Admin\SalaryController as AdminSalaryController;
use App\Http\Controllers\Web\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Web\Admin\TableController as AdminTableController;
use App\Http\Controllers\Web\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Web\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Web\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Web\Staff\PaymentController as StaffPaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Public routes
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Email verification routes
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->name('verification.resend');
    
    // Password reset routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/date/{date}', [BookingController::class, 'getBookingsByDate'])->name('date');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{id}/success', [BookingController::class, 'success'])->name('success');
        Route::get('/{id}/order', [BookingController::class, 'orderFromTable'])->name('order');
        Route::get('/{id}', [BookingController::class, 'show'])->name('show');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });
    
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/qr/{orderId}', [\App\Http\Controllers\Web\PaymentController::class, 'showQr'])->name('qr');
        Route::post('/qr/{orderId}', [\App\Http\Controllers\Web\PaymentController::class, 'processQr'])->name('qr.process');
    });
    
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\VoucherController::class, 'index'])->name('index');
        Route::post('/check', [\App\Http\Controllers\Web\VoucherController::class, 'check'])->name('check');
    });
    
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    });
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\ProfileController::class, 'index'])->name('index');
        Route::put('/', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\Web\ProfileController::class, 'updatePassword'])->name('update-password');
    });
    
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('index');
        Route::put('/{id}/read', [\App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('read');
        Route::put('/read-all', [\App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [\App\Http\Controllers\Web\NotificationController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Web\ReviewController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\Web\ReviewController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\FavoriteController::class, 'index'])->name('index');
        Route::post('/toggle', [\App\Http\Controllers\Web\FavoriteController::class, 'toggle'])->name('toggle');
        Route::delete('/{id}', [\App\Http\Controllers\Web\FavoriteController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\AddressController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Web\AddressController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Web\AddressController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Web\AddressController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/default', [\App\Http\Controllers\Web\AddressController::class, 'setDefault'])->name('set-default');
    });
});

// Staff routes
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [StaffBookingController::class, 'index'])->name('index');
        Route::get('/{id}', [StaffBookingController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [StaffBookingController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/reject', [StaffBookingController::class, 'reject'])->name('reject');
        Route::post('/{id}/check-in', [StaffBookingController::class, 'checkIn'])->name('check-in');
        Route::post('/{id}/transfer-table', [StaffBookingController::class, 'transferTable'])->name('transfer-table');
    });
    
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [StaffOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [StaffOrderController::class, 'show'])->name('show');
        Route::put('/{id}/status', [StaffOrderController::class, 'updateStatus'])->name('update-status');
    });
    
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [StaffPaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [StaffPaymentController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [StaffPaymentController::class, 'confirm'])->name('confirm');
        Route::get('/create/{orderId}', [StaffPaymentController::class, 'create'])->name('create');
        Route::post('/', [StaffPaymentController::class, 'store'])->name('store');
    });
    
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Staff\AttendanceController::class, 'index'])->name('index');
        Route::post('/check-in', [\App\Http\Controllers\Web\Staff\AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [\App\Http\Controllers\Web\Staff\AttendanceController::class, 'checkOut'])->name('check-out');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [AdminMenuController::class, 'index'])->name('index');
        Route::get('/create', [AdminMenuController::class, 'create'])->name('create');
        Route::post('/', [AdminMenuController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminMenuController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminMenuController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminMenuController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    Route::prefix('tables')->name('tables.')->group(function () {
        Route::get('/', [AdminTableController::class, 'index'])->name('index');
        Route::get('/create', [AdminTableController::class, 'create'])->name('create');
        Route::post('/', [AdminTableController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminTableController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminTableController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminTableController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [AdminStaffController::class, 'index'])->name('index');
        Route::get('/create', [AdminStaffController::class, 'create'])->name('create');
        Route::post('/', [AdminStaffController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminStaffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminStaffController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminStaffController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reset-password', [AdminStaffController::class, 'resetPassword'])->name('reset-password');
    });
    
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
    });
    
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/', [AdminVoucherController::class, 'index'])->name('index');
        Route::get('/create', [AdminVoucherController::class, 'create'])->name('create');
        Route::post('/', [AdminVoucherController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminVoucherController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminVoucherController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminVoucherController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/toggle-status', [AdminVoucherController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    Route::prefix('salaries')->name('salaries.')->group(function () {
        Route::get('/', [AdminSalaryController::class, 'index'])->name('index');
        Route::get('/create', [AdminSalaryController::class, 'create'])->name('create');
        Route::post('/', [AdminSalaryController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminSalaryController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminSalaryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminSalaryController::class, 'update'])->name('update');
    });
    
    Route::prefix('ingredients')->name('ingredients.')->group(function () {
        Route::get('/', [AdminIngredientController::class, 'index'])->name('index');
        Route::get('/create', [AdminIngredientController::class, 'create'])->name('create');
        Route::post('/', [AdminIngredientController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminIngredientController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminIngredientController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminIngredientController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminIngredientController::class, 'destroy'])->name('destroy');
    });
    
    Route::prefix('ingredient-stocks')->name('ingredient-stocks.')->group(function () {
        Route::get('/', [AdminIngredientStockController::class, 'index'])->name('index');
        Route::get('/create', [AdminIngredientStockController::class, 'create'])->name('create');
        Route::post('/', [AdminIngredientStockController::class, 'store'])->name('store');
    });
    
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/{userId}', [\App\Http\Controllers\Web\Admin\AttendanceController::class, 'show'])->name('show');
    });
    
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Admin\BookingController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Web\Admin\BookingController::class, 'show'])->name('show');
    });
    
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Admin\OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Web\Admin\OrderController::class, 'show'])->name('show');
    });
    
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Web\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Web\Admin\PaymentController::class, 'show'])->name('show');
    });
});
