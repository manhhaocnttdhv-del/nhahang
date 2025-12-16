<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        
        $query = Payment::where('status', 'completed');

        switch ($period) {
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
        }

        $revenue = $query->sum('amount');
        $orderCount = Order::whereHas('payments', function($q) use ($query) {
            $q->where('status', 'completed');
        })->count();

        $popularItems = OrderItem::select('item_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', function($q) use ($period) {
                switch ($period) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $q->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->groupBy('item_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Doanh thu theo phương thức thanh toán
        $revenueByPaymentMethod = Payment::select('payment_method', DB::raw('SUM(amount) as total'))
            ->where('status', 'completed')
            ->where(function($q) use ($period) {
                switch ($period) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $q->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->groupBy('payment_method')
            ->get();

        // Doanh thu theo loại đơn
        $revenueByOrderType = Order::select('order_type', DB::raw('SUM(total_amount) as total'))
            ->whereHas('payments', function($q) use ($period) {
                $q->where('status', 'completed');
                switch ($period) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $q->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->groupBy('order_type')
            ->get();

        // Thống kê bàn - Chỉ tính các order đã thanh toán
        $tableStats = \App\Models\Table::select('tables.name', DB::raw('COUNT(DISTINCT orders.id) as order_count'), DB::raw('COALESCE(SUM(orders.total_amount), 0) as revenue'))
            ->leftJoin('orders', 'tables.id', '=', 'orders.table_id')
            ->leftJoin('payments', function($join) {
                $join->on('orders.id', '=', 'payments.order_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->where(function($q) use ($period) {
                switch ($period) {
                    case 'today':
                        $q->whereDate('orders.created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('orders.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('orders.created_at', now()->month)->whereYear('orders.created_at', now()->year);
                        break;
                    case 'year':
                        $q->whereYear('orders.created_at', now()->year);
                        break;
                }
            })
            ->whereNotNull('payments.id') // Chỉ lấy orders đã có payment completed
            ->groupBy('tables.id', 'tables.name')
            ->orderBy('revenue', 'desc')
            ->get();

        // Thống kê khách hàng
        $customerStats = Order::select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->whereHas('payments', function($q) use ($period) {
                $q->where('status', 'completed');
                switch ($period) {
                    case 'today':
                        $q->whereDate('created_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $q->whereYear('created_at', now()->year);
                        break;
                }
            })
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->with('user')
            ->get();

        // Doanh thu theo ngày (cho biểu đồ)
        $dailyRevenue = [];
        if ($period === 'week' || $period === 'month') {
            $startDate = $period === 'week' ? now()->startOfWeek() : now()->startOfMonth();
            $endDate = $period === 'week' ? now()->endOfWeek() : now()->endOfMonth();
            
            $dailyData = Payment::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            foreach ($dailyData as $data) {
                $dailyRevenue[] = [
                    'date' => $data->date,
                    'revenue' => (float) $data->total
                ];
            }
        }

        return view('admin.reports.index', compact(
            'revenue', 
            'orderCount', 
            'popularItems', 
            'period',
            'revenueByPaymentMethod',
            'revenueByOrderType',
            'tableStats',
            'customerStats',
            'dailyRevenue'
        ));
    }
}
