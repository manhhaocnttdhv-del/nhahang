<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'overtime_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'working_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function calculateWorkingHours()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $checkIn = Carbon::parse($this->date . ' ' . $this->check_in);
        $checkOut = Carbon::parse($this->date . ' ' . $this->check_out);

        // Nếu check_out < check_in, có thể là làm qua ngày
        if ($checkOut->lt($checkIn)) {
            $checkOut->addDay();
        }

        $totalMinutes = $checkIn->diffInMinutes($checkOut);
        
        // Trừ 1 giờ nghỉ trưa (nếu làm > 4 giờ)
        if ($totalMinutes > 240) {
            $totalMinutes -= 60;
        }

        return round($totalMinutes / 60, 2);
    }

    public function calculateOvertimeHours($standardHours = 8)
    {
        $workingHours = $this->working_hours;
        if ($workingHours > $standardHours) {
            return round($workingHours - $standardHours, 2);
        }
        return 0;
    }

    public function isLate($expectedCheckIn = '08:00:00')
    {
        if (!$this->check_in) {
            return false;
        }

        $checkInTime = Carbon::parse($this->date . ' ' . $this->check_in);
        $expectedTime = Carbon::parse($this->date . ' ' . $expectedCheckIn);

        return $checkInTime->gt($expectedTime);
    }

    public function isPresent()
    {
        return $this->status === 'present' && $this->check_in && $this->check_out;
    }
}
