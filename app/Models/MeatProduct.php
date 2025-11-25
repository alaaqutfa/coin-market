<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeatProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'description',
        'current_stock',
        'cost_price',
        'selling_price',
        'waste_percentage',
        'is_active'
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // العلاقة مع فواتير الشراء
    public function purchaseItems()
    {
        return $this->hasMany(MeatPurchaseItem::class);
    }

    // العلاقة مع حركات المخزون
    public function inventoryMovements()
    {
        return $this->hasMany(MeatInventoryMovement::class);
    }

    // دالة لتحديث المخزون
    public function updateStock($quantity, $type = 'in')
    {
        if ($type === 'in') {
            $this->current_stock += $quantity;
        } else {
            $this->current_stock -= $quantity;
        }
        $this->save();
    }

    // دالة لحساب الهدر المتوقع
    public function calculateExpectedWaste($quantity)
    {
        return $quantity * ($this->waste_percentage / 100);
    }
}
