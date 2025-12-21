<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'receipt_number',
        'receipt_file',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function getCategoryNameAttribute()
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

        return $categories[$this->category] ?? $this->category;
    }
}

