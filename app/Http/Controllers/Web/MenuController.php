<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $menuItems = MenuItem::with('category')
            ->where('is_active', true)
            ->where('status', 'available')
            ->orderBy('sort_order')
            ->get();

        // Get user favorites if authenticated
        $userFavorites = [];
        if (auth()->check()) {
            $userFavorites = \App\Models\Favorite::where('user_id', auth()->id())
                ->pluck('menu_item_id')
                ->toArray();
        }

        return view('menu.index', compact('categories', 'menuItems', 'userFavorites'));
    }
}
