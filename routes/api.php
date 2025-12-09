<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Api\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Api\Customer\NotificationController as CustomerNotificationController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Api\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Api\Staff\PaymentController;
use App\Http\Controllers\Api\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Api\Admin\TableController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\Admin\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public menu routes (no authentication required)
Route::get('/menu', [CustomerMenuController::class, 'index']);
Route::get('/menu/{id}', [CustomerMenuController::class, 'show']);
Route::get('/categories', [CustomerMenuController::class, 'categories']);
Route::get('/tables/available', [\App\Http\Controllers\Api\Customer\TableController::class, 'getAvailable']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Customer routes
    Route::prefix('customer')->group(function () {
        // Bookings
        Route::post('/bookings', [CustomerBookingController::class, 'store']);
        Route::get('/bookings', [CustomerBookingController::class, 'index']);
        Route::get('/bookings/{id}', [CustomerBookingController::class, 'show']);

        // Orders
        Route::post('/orders', [CustomerOrderController::class, 'store']);
        Route::get('/orders', [CustomerOrderController::class, 'index']);
        Route::get('/orders/{id}', [CustomerOrderController::class, 'show']);

        // Notifications
        Route::get('/notifications', [CustomerNotificationController::class, 'index']);
        Route::get('/notifications/unread', [CustomerNotificationController::class, 'unread']);
        Route::put('/notifications/{id}/read', [CustomerNotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [CustomerNotificationController::class, 'markAllAsRead']);
    });

    // Staff routes
    Route::middleware('staff')->prefix('staff')->group(function () {
        // Bookings
        Route::get('/bookings', [StaffBookingController::class, 'index']);
        Route::get('/bookings/{id}', [StaffBookingController::class, 'show']);
        Route::post('/bookings/{id}/confirm', [StaffBookingController::class, 'confirm']);
        Route::post('/bookings/{id}/reject', [StaffBookingController::class, 'reject']);
        Route::post('/bookings/{id}/check-in', [StaffBookingController::class, 'checkIn']);

        // Orders
        Route::get('/orders', [StaffOrderController::class, 'index']);
        Route::get('/orders/{id}', [StaffOrderController::class, 'show']);
        Route::put('/orders/{id}/status', [StaffOrderController::class, 'updateStatus']);

        // Payments
        Route::post('/payments', [PaymentController::class, 'process']);
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);
    });

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Menu management
        Route::get('/menu', [AdminMenuController::class, 'index']);
        Route::post('/menu', [AdminMenuController::class, 'store']);
        Route::get('/menu/{id}', [AdminMenuController::class, 'show']);
        Route::put('/menu/{id}', [AdminMenuController::class, 'update']);
        Route::delete('/menu/{id}', [AdminMenuController::class, 'destroy']);
        Route::put('/menu/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus']);

        // Categories
        Route::get('/categories', [AdminMenuController::class, 'categories']);
        Route::post('/categories', [AdminMenuController::class, 'storeCategory']);
        Route::put('/categories/{id}', [AdminMenuController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [AdminMenuController::class, 'destroyCategory']);

        // Tables
        Route::get('/tables', [TableController::class, 'index']);
        Route::post('/tables', [TableController::class, 'store']);
        Route::get('/tables/{id}', [TableController::class, 'show']);
        Route::put('/tables/{id}', [TableController::class, 'update']);
        Route::delete('/tables/{id}', [TableController::class, 'destroy']);
        Route::put('/tables/{id}/status', [TableController::class, 'updateStatus']);

        // Staff management
        Route::get('/staff', [StaffController::class, 'index']);
        Route::post('/staff', [StaffController::class, 'store']);
        Route::get('/staff/{id}', [StaffController::class, 'show']);
        Route::put('/staff/{id}', [StaffController::class, 'update']);
        Route::delete('/staff/{id}', [StaffController::class, 'destroy']);
        Route::post('/staff/{id}/reset-password', [StaffController::class, 'resetPassword']);

        // Reports
        Route::get('/reports/revenue', [ReportController::class, 'revenue']);
        Route::get('/reports/orders', [ReportController::class, 'orders']);
        Route::get('/reports/popular-items', [ReportController::class, 'popularItems']);
        Route::get('/reports/table-revenue', [ReportController::class, 'tableRevenue']);
        Route::get('/reports/statistics', [ReportController::class, 'statistics']);
    });
});
