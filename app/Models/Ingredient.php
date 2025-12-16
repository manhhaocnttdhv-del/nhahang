<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'unit',
        'unit_price',
        'min_stock',
        'max_stock',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    // Relationships
    public function stocks()
    {
        return $this->hasMany(IngredientStock::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'ingredient_menu_item')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Helper methods
    public function getCurrentStock()
    {
        // Tính tồn kho hiện tại: tổng nhập - tổng xuất
        $imports = $this->stocks()
            ->where('type', 'import')
            ->sum('quantity');
        
        $exports = $this->stocks()
            ->where('type', 'export')
            ->sum('quantity');
        
        $adjustments = $this->stocks()
            ->where('type', 'adjustment')
            ->sum('quantity');
        
        return $imports - $exports + $adjustments;
    }

    public function isLowStock()
    {
        return $this->getCurrentStock() <= $this->min_stock;
    }

    public function isOverStock()
    {
        return $this->max_stock > 0 && $this->getCurrentStock() >= $this->max_stock;
    }
}
