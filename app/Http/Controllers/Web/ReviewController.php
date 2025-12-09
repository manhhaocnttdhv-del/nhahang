<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if user has ordered this item (optional validation)
        if ($request->order_id) {
            $order = Order::where('user_id', auth()->id())
                ->findOrFail($request->order_id);
            
            $hasItem = $order->orderItems()
                ->where('menu_item_id', $request->menu_item_id)
                ->exists();
            
            if (!$hasItem) {
                return back()->withErrors(['error' => 'Bạn chưa đặt món này trong đơn hàng này']);
            }
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $imagePaths[] = $path;
            }
        }

        Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'menu_item_id' => $request->menu_item_id,
                'order_id' => $request->order_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $imagePaths,
                'is_approved' => false, // Admin needs to approve
            ]
        );

        return back()->with('success', 'Đánh giá của bạn đã được gửi! Vui lòng chờ phê duyệt.');
    }

    public function destroy($id)
    {
        $review = Review::where('user_id', auth()->id())
            ->findOrFail($id);

        // Delete images
        if ($review->images) {
            foreach ($review->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá');
    }
}
