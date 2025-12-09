<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $query = Table::query();

        if ($request->has('area')) {
            $query->where('area', $request->area);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->orderBy('number')->get();

        return response()->json([
            'data' => $tables,
        ]);
    }

    public function store(StoreTableRequest $request)
    {
        $table = Table::create($request->validated());

        return response()->json([
            'message' => 'Đã thêm bàn thành công',
            'data' => $table,
        ], 201);
    }

    public function show($id)
    {
        $table = Table::with(['bookings', 'orders'])->findOrFail($id);

        return response()->json([
            'data' => $table,
        ]);
    }

    public function update(UpdateTableRequest $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->update($request->validated());

        return response()->json([
            'message' => 'Đã cập nhật bàn thành công',
            'data' => $table,
        ]);
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return response()->json([
            'message' => 'Đã xóa bàn thành công',
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:available,reserved,occupied,maintenance',
        ]);

        $table = Table::findOrFail($id);
        $table->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Đã cập nhật trạng thái bàn',
            'data' => $table,
        ]);
    }
}
