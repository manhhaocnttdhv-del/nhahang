<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVoucherRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $vouchers = Voucher::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(StoreVoucherRequest $request)
    {
        Voucher::create($request->validated());

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Đã thêm voucher thành công');
    }

    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(StoreVoucherRequest $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $data = $request->validated();

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Đã cập nhật voucher thành công');
    }

    public function destroy($id)
    {
        Voucher::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa voucher thành công');
    }

    public function toggleStatus($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update(['is_active' => !$voucher->is_active]);
        
        return back()->with('success', 'Đã cập nhật trạng thái voucher');
    }
}

