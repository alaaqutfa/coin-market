<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeatInventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'meat_product_id',
        'movement_type',
        'quantity',
        'unit_price',
        'total_price',
        'movement_date',
        'notes',
    ];

    protected $casts = [
        'quantity'      => 'decimal:3',
        'unit_price'    => 'decimal:2',
        'total_price'   => 'decimal:2',
        'movement_date' => 'date',
    ];

    // العلاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(MeatProduct::class, 'meat_product_id');
    }

    // حدث قبل الحفظ لحساب الإجمالي
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($movement) {
            $movement->total_price = $movement->quantity * $movement->unit_price;
        });

        // تحديث المخزون عند إنشاء الحركة
        static::created(function ($movement) {
            if (in_array($movement->movement_type, ['out', 'waste'])) {
                $movement->product->updateStock($movement->quantity, 'out');
            } elseif ($movement->movement_type === 'return') {
                $movement->product->updateStock($movement->quantity, 'in');
            }
        });
    }

    // سكوب للحركات اليومية
    public function scopeToday($query)
    {
        return $query->whereDate('movement_date', today());
    }

    // سكوب للمبيعات
    public function scopeSales($query)
    {
        return $query->where('movement_type', 'out');
    }

    // سكوب للإرجاعات
    public function scopeReturns($query)
    {
        return $query->where('movement_type', 'return');
    }

    // سكوب للهدر
    public function scopeWaste($query)
    {
        return $query->where('movement_type', 'waste');
    }
}
