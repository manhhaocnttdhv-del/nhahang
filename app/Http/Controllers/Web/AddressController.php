<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('addresses.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // If this is set as default, unset other defaults
            if ($request->is_default) {
                Address::where('user_id', auth()->id())
                    ->update(['is_default' => false]);
            }

            Address::create([
                'user_id' => auth()->id(),
                'label' => $request->label,
                'recipient_name' => $request->recipient_name,
                'phone' => $request->phone,
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'ward' => $request->ward,
                'district' => $request->district,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'notes' => $request->notes,
                'is_default' => $request->is_default ?? false,
            ]);

            DB::commit();

            return redirect()->route('addresses.index')
                ->with('success', 'Đã thêm địa chỉ mới');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'label' => 'nullable|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // If this is set as default, unset other defaults
            if ($request->is_default) {
                Address::where('user_id', auth()->id())
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->all());

            DB::commit();

            return redirect()->route('addresses.index')
                ->with('success', 'Đã cập nhật địa chỉ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Đã xóa địa chỉ');
    }

    public function setDefault($id)
    {
        $address = Address::where('user_id', auth()->id())
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            Address::where('user_id', auth()->id())
                ->update(['is_default' => false]);

            $address->update(['is_default' => true]);

            DB::commit();

            return redirect()->route('addresses.index')
                ->with('success', 'Đã đặt làm địa chỉ mặc định');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
