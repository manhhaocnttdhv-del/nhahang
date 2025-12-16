<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'type',
        'quantity',
        'unit_price',
        'total_amount',
        'stock_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'stock_date' => 'date',
    ];

    // Relationships
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stock) {
            // Tự động tính total_amount
            $stock->total_amount = $stock->quantity * $stock->unit_price;
        });

        static::updating(function ($stock) {
            // Tự động tính total_amount khi update
            $stock->total_amount = $stock->quantity * $stock->unit_price;
        });
    }
}
