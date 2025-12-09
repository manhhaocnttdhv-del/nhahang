<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $menuItems = MenuItem::with('category')->orderBy('sort_order')->paginate(20);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menu.create', compact('categories'));
    }

    public function store(StoreMenuItemRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('menu-items', 'public');
            $data['image'] = $path;
        } else {
            // Don't set image if no file uploaded
            unset($data['image']);
        }

        MenuItem::create($data);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Đã thêm món ăn thành công');
    }

    public function edit($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.menu.edit', compact('menuItem', 'categories'));
    }

    public function update(StoreMenuItemRequest $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $data = $request->validated();
        
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
                Storage::disk('public')->delete($menuItem->image);
            }

            $image = $request->file('image');
            $path = $image->store('menu-items', 'public');
            $data['image'] = $path;
        } else {
            // Remove image from data if no new image uploaded
            unset($data['image']);
        }

        $menuItem->update($data);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Đã cập nhật món ăn thành công');
    }

    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        
        // Delete image if exists
        if ($menuItem->image && Storage::disk('public')->exists($menuItem->image)) {
            Storage::disk('public')->delete($menuItem->image);
        }
        
        $menuItem->delete();
        return back()->with('success', 'Đã xóa món ăn thành công');
    }

    public function toggleStatus($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update(['is_active' => !$menuItem->is_active]);
        
        return back()->with('success', 'Đã cập nhật trạng thái món ăn');
    }
}
