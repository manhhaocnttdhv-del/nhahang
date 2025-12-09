<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

    public function index()
    {
        $pendingBookings = Booking::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $preparingOrders = Order::where('status', 'preparing')->count();
        
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $recentBookings = Booking::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = Order::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('staff.dashboard', compact(
            'pendingBookings',
            'processingOrders',
            'preparingOrders',
            'todayRevenue',
            'recentBookings',
            'recentOrders'
        ));
    }
}
