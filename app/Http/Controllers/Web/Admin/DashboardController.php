<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayBookings = Booking::whereDate('created_at', today())->count();
        $totalStaff = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->count();

        $monthRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $monthOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $avgOrderValue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->avg('total_amount');

        $monthBookings = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $popularItems = OrderItem::select('item_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function($query) {
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            })
            ->groupBy('item_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'todayRevenue',
            'todayOrders',
            'todayBookings',
            'totalStaff',
            'monthRevenue',
            'monthOrders',
            'avgOrderValue',
            'monthBookings',
            'popularItems'
        ));
    }
}
