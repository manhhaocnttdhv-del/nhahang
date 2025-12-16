<?php

namespace App\Http\Controllers\Web\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

    public function index()
    {
        $user = auth()->user();
        $today = today();
        
        // Lấy điểm danh hôm nay
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        // Lấy lịch sử điểm danh (30 ngày gần nhất)
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Thống kê tháng này
        $monthStats = $this->getMonthStats($user->id);

        return view('staff.attendance.index', compact(
            'todayAttendance',
            'attendances',
            'monthStats'
        ));
    }

    public function checkIn(Request $request)
    {
        $user = auth()->user();
        $today = today();

        // Kiểm tra xem đã điểm danh hôm nay chưa
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        if ($attendance && $attendance->check_in) {
            return back()->withErrors(['error' => 'Bạn đã điểm danh vào hôm nay rồi!']);
        }

        $checkInTime = now()->format('H:i:s');
        
        // Kiểm tra muộn (sau 8:00)
        $expectedCheckIn = Carbon::parse($today->format('Y-m-d') . ' 08:00:00');
        $actualCheckIn = Carbon::parse($today->format('Y-m-d') . ' ' . $checkInTime);
        $isLate = $actualCheckIn->gt($expectedCheckIn);
        $status = $isLate ? 'late' : 'present';

        if ($attendance) {
            // Đã có bản ghi nhưng chưa check in
            $attendance->update([
                'check_in' => $checkInTime,
                'status' => $status,
            ]);
        } else {
            // Tạo mới
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $checkInTime,
                'status' => $status,
            ]);
        }

        $message = $isLate ? 'Điểm danh vào thành công (muộn)' : 'Điểm danh vào thành công';
        return back()->with('success', $message);
    }

    public function checkOut(Request $request)
    {
        $user = auth()->user();
        $today = today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today->format('Y-m-d'))
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return back()->withErrors(['error' => 'Bạn chưa điểm danh vào!']);
        }

        if ($attendance->check_out) {
            return back()->withErrors(['error' => 'Bạn đã điểm danh ra rồi!']);
        }

        $checkOutTime = now()->format('H:i:s');
        
        // Tính số giờ làm việc
        $dateStr = $attendance->date instanceof \Carbon\Carbon ? $attendance->date->format('Y-m-d') : $attendance->date;
        $checkIn = Carbon::parse($dateStr . ' ' . $attendance->check_in);
        $checkOut = Carbon::parse($dateStr . ' ' . $checkOutTime);
        
        // Nếu check_out < check_in, có thể là làm qua ngày
        if ($checkOut->lt($checkIn)) {
            $checkOut->addDay();
        }

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        
        // Trừ 1 giờ nghỉ trưa (nếu làm > 4 giờ)
        if ($totalMinutes > 240) {
            $totalMinutes -= 60;
        }

        $workingHours = round($totalMinutes / 60, 2);
        
        // Tính giờ làm thêm (nếu > 8 giờ)
        $standardHours = 8;
        $overtimeHours = $workingHours > $standardHours ? round($workingHours - $standardHours, 2) : 0;

        $attendance->update([
            'check_out' => $checkOutTime,
            'working_hours' => $workingHours,
            'overtime_hours' => $overtimeHours,
        ]);

        return back()->with('success', 'Điểm danh ra thành công! Tổng giờ làm: ' . $workingHours . ' giờ');
    }

    private function getMonthStats($userId)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalWorkingHours = $attendances->sum('working_hours');
        $totalOvertimeHours = $attendances->sum('overtime_hours');
        $presentDays = $attendances->where('status', 'present')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $absentDays = $attendances->where('status', 'absent')->count();

        return [
            'total_working_hours' => $totalWorkingHours,
            'total_overtime_hours' => $totalOvertimeHours,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'total_days' => $attendances->count(),
        ];
    }
}
