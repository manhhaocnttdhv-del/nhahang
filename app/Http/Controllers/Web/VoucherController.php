<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('used_count < usage_limit');
            })
            ->orderBy('end_date', 'asc')
            ->get()
            ->filter(function($voucher) {
                return $voucher->isValid();
            });

        // Get user's voucher usage history
        $usedVouchers = Order::where('user_id', auth()->id())
            ->whereNotNull('voucher_id')
            ->with('voucher')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vouchers.index', compact('vouchers', 'usedVouchers'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã voucher không tồn tại',
            ]);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã voucher không còn hiệu lực',
            ]);
        }

        if ($voucher->min_order_amount && $request->subtotal < $voucher->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Đơn hàng tối thiểu " . number_format((float)$voucher->min_order_amount) . " đ để sử dụng voucher này",
            ]);
        }

        $discount = $voucher->calculateDiscount($request->subtotal);
        $taxAmount = ($request->subtotal - $discount) * 0.1;
        $totalAmount = $request->subtotal - $discount + $taxAmount;

        return response()->json([
            'valid' => true,
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'value' => $voucher->value,
            ],
            'discount' => $discount,
            'subtotal' => $request->subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }
}

