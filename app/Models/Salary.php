<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employment_type',
        'period_start',
        'period_end',
        'base_salary',
        'working_days',
        'working_hours',
        'hourly_rate',
        'overtime_hours',
        'overtime_rate',
        'bonus',
        'deduction',
        'total_salary',
        'notes',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'base_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper methods
    public function calculateTotal()
    {
        $total = 0;
        
        if ($this->employment_type === 'full_time') {
            // Full-time: base_salary + overtime + bonus - deduction
            $total = $this->base_salary;
        } else {
            // Part-time: working_hours * hourly_rate + overtime + bonus - deduction
            $total = $this->working_hours * $this->hourly_rate;
        }
        
        // Thêm overtime
        $total += $this->overtime_hours * $this->overtime_rate;
        
        // Thêm bonus và trừ deduction
        $total += $this->bonus - $this->deduction;
        
        return $total;
    }
}
