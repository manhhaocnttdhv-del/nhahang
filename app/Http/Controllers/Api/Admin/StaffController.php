<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $staff = $query->orderBy('name')->get();

        return response()->json([
            'data' => $staff,
        ]);
    }

    public function store(StoreStaffRequest $request)
    {
        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'Đã tạo tài khoản nhân viên thành công',
            'data' => $staff,
        ], 201);
    }

    public function show($id)
    {
        $staff = User::findOrFail($id);

        return response()->json([
            'data' => $staff,
        ]);
    }

    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff,cashier,kitchen_manager',
            'phone' => 'nullable|string|max:20',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'message' => 'Đã cập nhật thông tin nhân viên thành công',
            'data' => $staff,
        ]);
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);

        if ($staff->id === auth()->id()) {
            return response()->json([
                'message' => 'Không thể xóa tài khoản của chính mình',
            ], 400);
        }

        $staff->delete();

        return response()->json([
            'message' => 'Đã xóa nhân viên thành công',
        ]);
    }

    public function resetPassword($id, Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $staff = User::findOrFail($id);
        $staff->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Đã đặt lại mật khẩu thành công',
        ]);
    }
}
