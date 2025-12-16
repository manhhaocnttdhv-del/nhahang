<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['order.user']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(30);

        $totalAmount = (clone $query)->where('status', 'completed')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalAmount'));
    }

    public function show($id)
    {
        $payment = Payment::with(['order.user', 'order.orderItems.menuItem'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }
}
