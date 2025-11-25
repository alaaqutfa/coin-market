<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeatPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id',
        'meat_product_id',
        'quantity',
        'unit_cost',
        'total_cost'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    // العلاقة مع الفاتورة
    public function invoice()
    {
        return $this->belongsTo(MeatPurchaseInvoice::class, 'purchase_invoice_id');
    }

    // العلاقة مع المنتج
    public function product()
    {
        return $this->belongsTo(MeatProduct::class, 'meat_product_id');
    }

    // حدث قبل الحفظ لحساب الإجمالي
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_cost = $item->quantity * $item->unit_cost;
        });

        // عند إنشاء عنصر شراء، تحديث مخزون المنتج
        static::created(function ($item) {
            $item->product->updateStock($item->quantity, 'in');

            // تسجيل حركة دخول للمخزون
            MeatInventoryMovement::create([
                'meat_product_id' => $item->meat_product_id,
                'movement_type' => 'in',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_cost,
                'total_price' => $item->total_cost,
                'movement_date' => $item->invoice->purchase_date,
                'notes' => 'شراء - فاتورة ' . $item->invoice->invoice_number
            ]);
        });
    }
}
