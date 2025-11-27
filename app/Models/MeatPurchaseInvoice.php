<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeatPurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_name',
        'total_amount',
        'purchase_date',
        'notes',
    ];

    protected $casts = [
        'total_amount'  => 'decimal:2',
        'purchase_date' => 'date',
    ];

    // العلاقة مع عناصر الفاتورة
    public function items()
    {
        return $this->hasMany(MeatPurchaseItem::class, 'purchase_invoice_id');
    }

    // دالة لتوليد رقم فاتورة تلقائي
    public static function generateInvoiceNumber()
    {
        $date        = now()->format('Ymd');
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$date}-%")->latest()->first();

        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -3) + 1 : 1;

        return "INV-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    // دالة لحساب الإجمالي التلقائي
    public function calculateTotal()
    {
        return $this->items->sum('total_cost');
    }
}
