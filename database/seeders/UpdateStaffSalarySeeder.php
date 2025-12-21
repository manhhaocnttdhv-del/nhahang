<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateStaffSalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cập nhật lương cho tất cả nhân viên
        $staff = User::whereIn('role', ['admin', 'staff', 'cashier', 'kitchen_manager'])->get();
        
        if ($staff->isEmpty()) {
            $this->command->warn('Không có nhân viên nào!');
            return;
        }
        
        $updatedCount = 0;
        
        foreach ($staff as $user) {
            $updated = false;
            
            // Nếu chưa có employment_type, set mặc định là full_time
            if (empty($user->employment_type)) {
                $user->employment_type = 'full_time';
                $updated = true;
            }
            
            // Nếu là full_time và chưa có base_salary, set mặc định 8,000,000đ
            if ($user->employment_type === 'full_time' && (empty($user->base_salary) || $user->base_salary == 0)) {
                $user->base_salary = 8000000;
                // Set hourly_rate để tính overtime (50,000đ/giờ)
                if (empty($user->hourly_rate) || $user->hourly_rate == 0) {
                    $user->hourly_rate = 50000;
                }
                $updated = true;
            }
            
            // Nếu là part_time và chưa có hourly_rate, set mặc định 50,000đ/giờ
            if ($user->employment_type === 'part_time' && (empty($user->hourly_rate) || $user->hourly_rate == 0)) {
                $user->hourly_rate = 50000;
                $updated = true;
            }
            
            if ($updated) {
                $user->save();
                $updatedCount++;
                $this->command->info("Đã cập nhật lương cho: {$user->name} ({$user->employment_type})");
            }
        }
        
        $this->command->info("Đã cập nhật {$updatedCount} nhân viên!");
    }
}

