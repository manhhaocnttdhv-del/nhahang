<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $menuItems = $query->orderBy('sort_order')->get();

        return response()->json([
            'data' => $menuItems,
        ]);
    }

    public function store(StoreMenuItemRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $menuItem = MenuItem::create($data);

        return response()->json([
            'message' => 'Đã thêm món ăn thành công',
            'data' => $menuItem->load('category'),
        ], 201);
    }

    public function show($id)
    {
        $menuItem = MenuItem::with('category')->findOrFail($id);

        return response()->json([
            'data' => $menuItem,
        ]);
    }

    public function update(StoreMenuItemRequest $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $menuItem->update($data);

        return response()->json([
            'message' => 'Đã cập nhật món ăn thành công',
            'data' => $menuItem->load('category'),
        ]);
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->delete();

        return response()->json([
            'message' => 'Đã xóa món ăn thành công',
        ]);
    }

    public function toggleStatus($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update([
            'is_active' => !$menuItem->is_active,
        ]);

        return response()->json([
            'message' => 'Đã cập nhật trạng thái món ăn',
            'data' => $menuItem,
        ]);
    }

    // Category management
    public function categories()
    {
        $categories = Category::orderBy('sort_order')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = Category::create($data);

        return response()->json([
            'message' => 'Đã thêm danh mục thành công',
            'data' => $category,
        ], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return response()->json([
            'message' => 'Đã cập nhật danh mục thành công',
            'data' => $category,
        ]);
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Đã xóa danh mục thành công',
        ]);
    }
}
