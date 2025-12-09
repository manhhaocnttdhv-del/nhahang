<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Table::query();

        if ($request->has('area')) {
            $query->where('area', $request->area);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->orderBy('number')->paginate(20);

        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(StoreTableRequest $request)
    {
        Table::create($request->validated());

        return redirect()->route('admin.tables.index')
            ->with('success', 'Đã thêm bàn thành công');
    }

    public function edit($id)
    {
        $table = Table::findOrFail($id);
        return view('admin.tables.edit', compact('table'));
    }

    public function update(UpdateTableRequest $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->update($request->validated());

        return redirect()->route('admin.tables.index')
            ->with('success', 'Đã cập nhật bàn thành công');
    }

    public function destroy($id)
    {
        Table::findOrFail($id)->delete();
        return back()->with('success', 'Đã xóa bàn thành công');
    }
}
