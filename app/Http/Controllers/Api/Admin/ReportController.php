<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $query = Payment::where('status', 'completed');

        if ($request->has('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                    }
                    break;
            }
        } else {
            // Default to today
            $query->whereDate('created_at', today());
        }

        $revenue = $query->sum('amount');
        $orderCount = $query->distinct('order_id')->count('order_id');

        return response()->json([
            'revenue' => $revenue,
            'order_count' => $orderCount,
            'period' => $request->period ?? 'today',
        ]);
    }

    public function orders(Request $request)
    {
        $query = Order::query();

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->with(['orderItems.menuItem', 'user', 'table'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $orders,
            'total' => $orders->count(),
            'total_amount' => $orders->sum('total_amount'),
        ]);
    }

    public function popularItems(Request $request)
    {
        $query = OrderItem::select('menu_item_id', 'item_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('menu_item_id', 'item_name');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
            });
        }

        $popularItems = $query->orderBy('total_quantity', 'desc')
            ->limit($request->limit ?? 10)
            ->get();

        return response()->json([
            'data' => $popularItems,
        ]);
    }

    public function tableRevenue(Request $request)
    {
        $query = Order::where('order_type', 'dine_in')
            ->whereHas('payments', function ($q) {
                $q->where('status', 'completed');
            })
            ->with('table');

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $tableRevenue = $query->select('table_id', DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('table_id')
            ->get();

        return response()->json([
            'data' => $tableRevenue,
        ]);
    }

    public function statistics(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $stats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'total_bookings' => \App\Models\Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_bookings' => \App\Models\Booking::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'average_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->avg('total_amount'),
        ];

        return response()->json([
            'data' => $stats,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
