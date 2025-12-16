<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientStockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = IngredientStock::with(['ingredient', 'createdBy']);

        if ($request->has('ingredient_id') && $request->ingredient_id) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('stock_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('stock_date', '<=', $request->date_to);
        }

        $stocks = $query->orderBy('stock_date', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        $ingredients = Ingredient::where('status', 'active')->orderBy('name')->get();

        return view('admin.ingredient-stocks.index', compact('stocks', 'ingredients'));
    }

    public function create(Request $request)
    {
        $ingredientId = $request->get('ingredient_id');
        $ingredient = null;
        
        if ($ingredientId) {
            $ingredient = Ingredient::findOrFail($ingredientId);
        }
        
        $ingredients = Ingredient::where('status', 'active')->orderBy('name')->get();
        
        return view('admin.ingredient-stocks.create', compact('ingredient', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'type' => 'required|in:import,export,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'stock_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $ingredient = Ingredient::findOrFail($request->ingredient_id);
            
            // Tính total_amount nếu có unit_price
            $totalAmount = $request->unit_price ? $request->quantity * $request->unit_price : null;
            
            $stock = IngredientStock::create([
                'ingredient_id' => $request->ingredient_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price ?? 0,
                'stock_date' => $request->stock_date ?? now()->format('Y-m-d'),
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Cập nhật current_stock trong bảng ingredients (nếu có)
            // Hoặc có thể tính từ stocks như đã làm trong model
            // Ở đây ta không cập nhật current_stock vì model đã có method getCurrentStock()

            DB::commit();
            
            return redirect()->route('admin.ingredients.show', $request->ingredient_id)
                ->with('success', 'Đã thêm phiếu nhập/xuất thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }
}

