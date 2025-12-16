<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $ingredients = Ingredient::orderBy('name')->paginate(12);
        return view('admin.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('admin.ingredients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name',
            'code' => 'nullable|string|max:50|unique:ingredients,code',
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'unit_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Ingredient::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'max_stock' => $request->max_stock ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Đã thêm nguyên liệu thành công');
    }

    public function show($id)
    {
        $ingredient = Ingredient::with(['stocks.createdBy', 'menuItems'])->findOrFail($id);
        return view('admin.ingredients.show', compact('ingredient'));
    }

    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
            'code' => 'nullable|string|max:50|unique:ingredients,code,' . $id,
            'description' => 'nullable|string|max:1000',
            'unit' => 'required|string|max:20',
            'unit_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $ingredient->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'unit' => $request->unit,
            'unit_price' => $request->unit_price ?? 0,
            'min_stock' => $request->min_stock ?? 0,
            'max_stock' => $request->max_stock ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.ingredients.show', $ingredient->id)
            ->with('success', 'Đã cập nhật nguyên liệu thành công');
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();
        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Đã xóa nguyên liệu thành công');
    }
}

