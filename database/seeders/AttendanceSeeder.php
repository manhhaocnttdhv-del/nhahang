<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả nhân viên
        $staff = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
        
        if ($staff->isEmpty()) {
            $this->command->warn('Không có nhân viên nào để tạo điểm danh!');
            return;
        }
        
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $yesterday = $today->copy()->subDay(); // Tính đến hôm qua
        
        // Tạo điểm danh từ đầu tháng đến hôm qua
        $currentDate = $startOfMonth->copy();
        $createdCount = 0;
        
        while ($currentDate->lte($yesterday)) {
            // Bỏ qua Chủ nhật (ngày 0) - giả sử không làm việc Chủ nhật
            if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                foreach ($staff as $user) {
                    // Kiểm tra xem đã có điểm danh chưa
                    $existing = Attendance::where('user_id', $user->id)
                        ->whereDate('date', $currentDate->format('Y-m-d'))
                        ->first();
                    
                    if (!$existing) {
                        // Random có điểm danh hay không (90% có mặt)
                        if (rand(1, 100) <= 90) {
                            // Giờ vào làm (7:00 - 9:00)
                            $checkInHour = rand(7, 9);
                            $checkInMinute = rand(0, 59);
                            $checkIn = Carbon::parse($currentDate->format('Y-m-d') . " {$checkInHour}:{$checkInMinute}:00");
                            
                            // Tính giờ ra làm (sau 8-10 giờ làm việc)
                            $workingHours = rand(8, 10);
                            $checkOut = $checkIn->copy()->addHours($workingHours);
                            
                            // Trừ 1 giờ nghỉ trưa nếu làm > 4 giờ
                            if ($workingHours > 4) {
                                $workingHours -= 1;
                            }
                            
                            // Tính overtime (nếu làm > 8 giờ)
                            $overtimeHours = 0;
                            if ($workingHours > 8) {
                                $overtimeHours = $workingHours - 8;
                            }
                            
                            // Status: 85% present, 10% late, 5% half_day
                            $statusRand = rand(1, 100);
                            $status = 'present';
                            if ($statusRand > 85 && $statusRand <= 95) {
                                $status = 'late';
                            } elseif ($statusRand > 95) {
                                $status = 'half_day';
                                $workingHours = $workingHours / 2; // Nửa ngày
                            }
                            
                            Attendance::create([
                                'user_id' => $user->id,
                                'date' => $currentDate->format('Y-m-d'),
                                'check_in' => $checkIn->format('H:i:s'),
                                'check_out' => $checkOut->format('H:i:s'),
                                'working_hours' => round($workingHours, 2),
                                'overtime_hours' => round($overtimeHours, 2),
                                'status' => $status,
                            ]);
                            
                            $createdCount++;
                        }
                    }
                }
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("Đã tạo {$createdCount} bản ghi điểm danh từ {$startOfMonth->format('d/m/Y')} đến {$yesterday->format('d/m/Y')}");
    }
}

