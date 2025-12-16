<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Attendance::with('user');

        // Filter theo nhân viên
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter theo ngày
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter theo trạng thái
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $staffList = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])
            ->orderBy('name')
            ->get();

        // Thống kê tổng quan
        $stats = $this->getStats($request);

        return view('admin.attendances.index', compact(
            'attendances',
            'staffList',
            'stats'
        ));
    }

    public function show($userId, Request $request)
    {
        $user = User::findOrFail($userId);

        $query = Attendance::where('user_id', $userId);

        // Filter theo ngày
        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->paginate(30);

        // Thống kê
        $stats = $this->getUserStats($userId, $request);

        return view('admin.attendances.show', compact(
            'user',
            'attendances',
            'stats'
        ));
    }

    private function getStats(Request $request)
    {
        $query = Attendance::query();

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $totalRecords = $query->count();
        $presentCount = (clone $query)->where('status', 'present')->count();
        $lateCount = (clone $query)->where('status', 'late')->count();
        $absentCount = (clone $query)->where('status', 'absent')->count();
        $totalWorkingHours = (clone $query)->sum('working_hours');
        $totalOvertimeHours = (clone $query)->sum('overtime_hours');

        return [
            'total_records' => $totalRecords,
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'absent_count' => $absentCount,
            'total_working_hours' => $totalWorkingHours,
            'total_overtime_hours' => $totalOvertimeHours,
        ];
    }

    private function getUserStats($userId, Request $request)
    {
        $query = Attendance::where('user_id', $userId);

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $attendances = $query->get();

        return [
            'total_days' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'late_days' => $attendances->where('status', 'late')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'total_working_hours' => $attendances->sum('working_hours'),
            'total_overtime_hours' => $attendances->sum('overtime_hours'),
        ];
    }
}
