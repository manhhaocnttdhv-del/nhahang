<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $staff = $query->orderBy('name')->paginate(20);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffRequest $request)
    {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Đã tạo tài khoản nhân viên thành công');
    }

    public function edit($id)
    {
        $staff = User::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff,cashier,kitchen_manager',
            'phone' => 'nullable|string|max:20',
        ]);

        $staff = User::findOrFail($id);
        $staff->update($request->only(['name', 'email', 'role', 'phone']));

        return redirect()->route('admin.staff.index')
            ->with('success', 'Đã cập nhật thông tin nhân viên thành công');
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản của chính mình');
        }

        $staff->delete();
        return back()->with('success', 'Đã xóa nhân viên thành công');
    }

    public function resetPassword($id, Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $staff = User::findOrFail($id);
        $staff->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Đã đặt lại mật khẩu thành công');
    }
}
