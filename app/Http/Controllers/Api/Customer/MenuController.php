<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category')
            ->where('is_active', true)
            ->where('status', 'available');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->orderBy('sort_order')->get();

        return response()->json([
            'data' => $menuItems,
        ]);
    }

    public function show($id)
    {
        $menuItem = MenuItem::with('category')
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'data' => $menuItem,
        ]);
    }

    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->with(['menuItems' => function ($query) {
                $query->where('is_active', true)->where('status', 'available');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }
}
