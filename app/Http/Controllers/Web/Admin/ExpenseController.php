<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Expense::with('creator')->orderBy('expense_date', 'desc');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->where('expense_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        // Filter by month
        if ($request->has('month') && $request->month) {
            $query->whereMonth('expense_date', date('m', strtotime($request->month)))
                  ->whereYear('expense_date', date('Y', strtotime($request->month)));
        }

        $expenses = $query->paginate(20);
        
        // Tính tổng từ query gốc (không phân trang)
        $totalAmount = (clone $query)->sum('amount');
        $categories = [
            'rent' => 'Tiền thuê mặt bằng',
            'utilities' => 'Điện nước',
            'marketing' => 'Marketing/Quảng cáo',
            'equipment' => 'Thiết bị/Máy móc',
            'maintenance' => 'Bảo trì/Sửa chữa',
            'insurance' => 'Bảo hiểm',
            'tax' => 'Thuế',
            'other' => 'Chi phí khác',
        ];

        return view('admin.expenses.index', compact('expenses', 'totalAmount', 'categories'));
    }

    public function create()
    {
        $categories = [
            'rent' => 'Tiền thuê mặt bằng',
            'utilities' => 'Điện nước',
            'marketing' => 'Marketing/Quảng cáo',
            'equipment' => 'Thiết bị/Máy móc',
            'maintenance' => 'Bảo trì/Sửa chữa',
            'insurance' => 'Bảo hiểm',
            'tax' => 'Thuế',
            'other' => 'Chi phí khác',
        ];

        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|in:rent,utilities,marketing,equipment,maintenance,insurance,tax,other',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name' => $request->name,
            'category' => $request->category ?? 'other',
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
        ];

        // Upload receipt file if provided
        if ($request->hasFile('receipt_file')) {
            $data['receipt_file'] = $request->file('receipt_file')->store('expenses/receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Đã thêm chi phí thành công');
    }

    public function show($id)
    {
        $expense = Expense::with('creator')->findOrFail($id);
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = [
            'rent' => 'Tiền thuê mặt bằng',
            'utilities' => 'Điện nước',
            'marketing' => 'Marketing/Quảng cáo',
            'equipment' => 'Thiết bị/Máy móc',
            'maintenance' => 'Bảo trì/Sửa chữa',
            'insurance' => 'Bảo hiểm',
            'tax' => 'Thuế',
            'other' => 'Chi phí khác',
        ];

        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|in:rent,utilities,marketing,equipment,maintenance,insurance,tax,other',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name' => $request->name,
            'category' => $request->category ?? 'other',
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'notes' => $request->notes,
        ];

        // Upload new receipt file if provided
        if ($request->hasFile('receipt_file')) {
            // Delete old file if exists
            if ($expense->receipt_file) {
                Storage::disk('public')->delete($expense->receipt_file);
            }
            $data['receipt_file'] = $request->file('receipt_file')->store('expenses/receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Đã cập nhật chi phí thành công');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Delete receipt file if exists
        if ($expense->receipt_file) {
            Storage::disk('public')->delete($expense->receipt_file);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Đã xóa chi phí thành công');
    }
}

