<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Salary::with('user');

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('employment_type') && $request->employment_type) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $salaries = $query->orderBy('period_end', 'desc')->paginate(20);
        $staffList = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->orderBy('name')->get();

        return view('admin.salaries.index', compact('salaries', 'staffList'));
    }

    public function create()
    {
        $staff = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->orderBy('name')->get();
        return view('admin.salaries.create', compact('staff'));
    }

    /**
     * Tự động tính lương từ điểm danh
     */
    public function calculateFromAttendance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $user = User::findOrFail($request->user_id);
        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);

        // Lấy tất cả điểm danh trong kỳ
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();

        if ($attendances->isEmpty()) {
            return back()->withErrors(['error' => 'Không có dữ liệu điểm danh trong kỳ này']);
        }

        // Tính tổng giờ làm và giờ làm thêm
        $totalWorkingHours = $attendances->sum('working_hours');
        $totalOvertimeHours = $attendances->sum('overtime_hours');
        $workingDays = $attendances->where('status', '!=', 'absent')->count();

        // Tính lương dựa trên loại nhân viên
        $employmentType = $user->employment_type ?? 'full_time';
        $baseSalary = 0;
        $hourlyRate = $user->hourly_rate ?? 0;
        $overtimeRate = $hourlyRate * 1.5; // Làm thêm = 1.5x lương giờ

        if ($employmentType === 'full_time') {
            // Full-time: lương cơ bản theo tháng
            $baseSalary = $user->base_salary ?? 0;
            // Tính lương theo số ngày làm việc
            $expectedDays = 22; // Số ngày làm việc chuẩn trong tháng
            $baseSalary = ($baseSalary / $expectedDays) * $workingDays;
        } else {
            // Part-time: lương theo giờ
            $baseSalary = $totalWorkingHours * $hourlyRate;
        }

        // Tính lương làm thêm
        $overtimeSalary = $totalOvertimeHours * $overtimeRate;

        // Tổng lương
        $totalSalary = $baseSalary + $overtimeSalary;

        // Trả về dữ liệu để điền vào form
        return response()->json([
            'employment_type' => $employmentType,
            'base_salary' => round($baseSalary, 2),
            'working_days' => $workingDays,
            'working_hours' => round($totalWorkingHours, 2),
            'hourly_rate' => $hourlyRate,
            'overtime_hours' => round($totalOvertimeHours, 2),
            'overtime_rate' => round($overtimeRate, 2),
            'total_salary' => round($totalSalary, 2),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'employment_type' => 'required|in:full_time,part_time',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'base_salary' => 'nullable|numeric|min:0',
            'working_days' => 'nullable|integer|min:0|max:31',
            'working_hours' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'total_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,paid',
        ]);

        // Validate based on employment type
        if ($request->employment_type === 'full_time') {
            $request->validate([
                'base_salary' => 'required|numeric|min:0',
            ]);
        } else {
            $request->validate([
                'working_hours' => 'required|numeric|min:0',
                'hourly_rate' => 'required|numeric|min:0',
            ]);
        }

        $salary = Salary::create([
            'user_id' => $request->user_id,
            'employment_type' => $request->employment_type,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'base_salary' => $request->base_salary ?? 0,
            'working_days' => $request->working_days ?? 0,
            'working_hours' => $request->working_hours ?? 0,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'overtime_rate' => $request->overtime_rate ?? 0,
            'bonus' => $request->bonus ?? 0,
            'deduction' => $request->deduction ?? 0,
            'total_salary' => $request->total_salary,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        if ($request->status === 'approved') {
            $salary->update([
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Đã tạo bảng lương thành công');
    }

    public function show($id)
    {
        $salary = Salary::with('user')->findOrFail($id);
        return view('admin.salaries.show', compact('salary'));
    }

    public function edit($id)
    {
        $salary = Salary::with('user')->findOrFail($id);
        $staff = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->orderBy('name')->get();
        return view('admin.salaries.edit', compact('salary', 'staff'));
    }

    public function update(Request $request, $id)
    {
        $salary = Salary::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'employment_type' => 'required|in:full_time,part_time',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'base_salary' => 'nullable|numeric|min:0',
            'working_days' => 'nullable|integer|min:0|max:31',
            'working_hours' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'total_salary' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,paid',
        ]);

        // Validate based on employment type
        if ($request->employment_type === 'full_time') {
            $request->validate([
                'base_salary' => 'required|numeric|min:0',
            ]);
        } else {
            $request->validate([
                'working_hours' => 'required|numeric|min:0',
                'hourly_rate' => 'required|numeric|min:0',
            ]);
        }

        $updateData = [
            'user_id' => $request->user_id,
            'employment_type' => $request->employment_type,
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'base_salary' => $request->base_salary ?? 0,
            'working_days' => $request->working_days ?? 0,
            'working_hours' => $request->working_hours ?? 0,
            'hourly_rate' => $request->hourly_rate ?? 0,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'overtime_rate' => $request->overtime_rate ?? 0,
            'bonus' => $request->bonus ?? 0,
            'deduction' => $request->deduction ?? 0,
            'total_salary' => $request->total_salary,
            'notes' => $request->notes,
            'status' => $request->status,
        ];

        // Nếu status chuyển sang approved và chưa có approved_by
        if ($request->status === 'approved' && !$salary->approved_by) {
            $updateData['approved_by'] = auth()->id();
            $updateData['approved_at'] = now();
        }

        $salary->update($updateData);

        return redirect()->route('admin.salaries.show', $salary->id)
            ->with('success', 'Đã cập nhật bảng lương thành công');
    }
}

