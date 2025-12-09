<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::where('user_id', auth()->id())
            ->with('menuItem.category')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
        ]);

        $favorite = Favorite::where('user_id', auth()->id())
            ->where('menu_item_id', $request->menu_item_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'Đã xóa khỏi yêu thích',
            ]);
        } else {
            Favorite::create([
                'user_id' => auth()->id(),
                'menu_item_id' => $request->menu_item_id,
            ]);
            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'Đã thêm vào yêu thích',
            ]);
        }
    }

    public function destroy($id)
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->findOrFail($id);

        $favorite->delete();

        return redirect()->route('favorites.index')
            ->with('success', 'Đã xóa khỏi yêu thích');
    }
}
