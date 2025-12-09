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

        return view('admin.reports.index', compact('revenue', 'orderCount', 'popularItems', 'period'));
    }
}
