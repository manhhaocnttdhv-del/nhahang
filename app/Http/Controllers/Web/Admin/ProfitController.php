<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngredientStock;
use App\Models\Salary;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        // Lấy tháng cần tính (mặc định tháng hiện tại)
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $monthParts = explode('-', $selectedMonth);
        $year = (int) $monthParts[0];
        $month = (int) $monthParts[1];
        
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        
        // Tính từ đầu tháng đến ngày hiện tại (nếu tháng đang chọn là tháng hiện tại)
        // Nếu chọn tháng khác thì tính đến cuối tháng đó
        $today = now();
        if ($monthStart->year == $today->year && $monthStart->month == $today->month) {
            // Tháng hiện tại: tính đến hôm qua (ngày hiện tại - 1)
            $endDate = $today->copy()->subDay()->endOfDay();
            $periodLabel = $monthStart->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
        } else {
            // Tháng khác: tính đến cuối tháng
            $endDate = $monthStart->copy()->endOfMonth();
            $periodLabel = 'Tháng ' . $monthStart->format('m/Y');
        }
        
        // Doanh thu: Tổng các payment completed từ đầu tháng đến endDate
        $revenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$monthStart, $endDate])
            ->sum('amount');
        
        // Chi phí nguyên vật liệu: Tính các nhập kho (import) từ đầu tháng đến endDate
        $ingredientCost = IngredientStock::where('type', 'import')
            ->whereBetween('created_at', [$monthStart, $endDate])
            ->sum('total_amount');
        
        // Chi phí nhân viên: Tính từ bảng Attendance (điểm danh)
        // Lấy tất cả nhân viên (admin, staff, cashier, kitchen_manager)
        $staff = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
        
        $salaryCost = 0;
        $salaryDetails = [];
        
        foreach ($staff as $user) {
            // Lấy điểm danh của nhân viên từ đầu tháng đến endDate
            // Sử dụng whereDate với >= và <= thay vì whereBetween cho date field
            $attendances = Attendance::where('user_id', $user->id)
                ->where('date', '>=', $monthStart->format('Y-m-d'))
                ->where('date', '<=', $endDate->format('Y-m-d'))
                ->whereIn('status', ['present', 'late', 'half_day'])
                ->get();
            
            if ($attendances->isEmpty()) {
                continue; // Không có điểm danh thì không tính lương
            }
            
            $userSalary = 0;
            $employmentType = $user->employment_type ?? 'full_time'; // Mặc định là full_time nếu null
            
            if ($employmentType === 'full_time') {
                // Full-time: Tính theo số ngày có điểm danh
                // Lương = (base_salary / số ngày làm việc chuẩn) × số ngày có điểm danh
                $standardDays = 22; // Số ngày làm việc chuẩn trong tháng
                $workingDays = $attendances->count(); // Số ngày thực tế có điểm danh
                $baseSalary = $user->base_salary ?? 0;
                
                if ($baseSalary > 0) {
                    $dailySalary = $baseSalary / $standardDays;
                    $userSalary = $dailySalary * $workingDays;
                    
                    // Cộng thêm overtime nếu có
                    $totalOvertimeHours = $attendances->sum('overtime_hours');
                    if ($totalOvertimeHours > 0 && $user->hourly_rate) {
                        $userSalary += $totalOvertimeHours * ($user->hourly_rate * 1.5); // Overtime = 1.5x
                    }
                }
            } else {
                // Part-time: Tính theo từng ngày rồi cộng lại
                // Mỗi ngày: (Giờ thường × hourly_rate) + (Overtime giờ × hourly_rate × 1.5)
                $hourlyRate = $user->hourly_rate ?? 0;
                
                if ($hourlyRate > 0) {
                    foreach ($attendances as $attendance) {
                        $workingHours = $attendance->working_hours ?? 0;
                        $overtimeHours = $attendance->overtime_hours ?? 0;
                        
                        // Giờ làm việc thường (trừ phần overtime)
                        $normalHours = $workingHours - $overtimeHours;
                        
                        // Lương giờ thường của ngày
                        $daySalary = $normalHours * $hourlyRate;
                        
                        // Lương overtime của ngày (1.5x)
                        $dayOvertimeSalary = $overtimeHours * ($hourlyRate * 1.5);
                        
                        // Cộng vào tổng lương
                        $userSalary += $daySalary + $dayOvertimeSalary;
                    }
                }
            }
            
            if ($userSalary > 0) {
                $salaryCost += $userSalary;
                $salaryDetails[] = [
                    'user' => $user,
                    'employment_type' => $employmentType,
                    'working_days' => $attendances->count(),
                    'total_working_hours' => $attendances->sum('working_hours'),
                    'total_overtime_hours' => $attendances->sum('overtime_hours'),
                    'base_salary' => $user->base_salary ?? 0,
                    'hourly_rate' => $user->hourly_rate ?? 0,
                    'cost' => $userSalary,
                ];
            }
        }
        
        // Chi phí khác: Lấy từ request hoặc mặc định 0
        $otherCosts = (float) ($request->get('other_costs', 0));
        
        // Lợi nhuận = Doanh thu - Chi phí nguyên vật liệu - Chi phí nhân viên - Chi phí khác
        $profit = $revenue - $ingredientCost - $salaryCost - $otherCosts;
        
        // Tính tỷ suất lợi nhuận (%)
        $profitMargin = $revenue > 0 ? ($profit / $revenue * 100) : 0;
        
        // Tính các tỷ lệ phần trăm
        $ingredientCostPercent = $revenue > 0 ? ($ingredientCost / $revenue * 100) : 0;
        $salaryCostPercent = $revenue > 0 ? ($salaryCost / $revenue * 100) : 0;
        $otherCostsPercent = $revenue > 0 ? ($otherCosts / $revenue * 100) : 0;
        
        // Chi tiết chi phí nguyên vật liệu (để hiển thị)
        $ingredientDetails = IngredientStock::where('type', 'import')
            ->whereBetween('created_at', [$monthStart, $endDate])
            ->with('ingredient')
            ->select('ingredient_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_amount) as total_amount'))
            ->groupBy('ingredient_id')
            ->get();
        
        
        $data = [
            'month' => $selectedMonth,
            'period_label' => $periodLabel,
            'revenue' => (float) $revenue,
            'ingredient_cost' => (float) $ingredientCost,
            'salary_cost' => (float) $salaryCost,
            'other_costs' => (float) $otherCosts,
            'profit' => (float) $profit,
            'profit_margin' => round($profitMargin, 2),
            'ingredient_cost_percent' => round($ingredientCostPercent, 2),
            'salary_cost_percent' => round($salaryCostPercent, 2),
            'other_costs_percent' => round($otherCostsPercent, 2),
            'ingredient_details' => $ingredientDetails,
            'salary_details' => $salaryDetails,
        ];

        return view('admin.profit.index', compact('data'));
    }
}

