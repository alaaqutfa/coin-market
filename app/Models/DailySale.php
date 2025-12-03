<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'meat_product_id',
        'sale_date',
        'sale_price',
        'return_price',
        'transaction_type',
        'quantity',
        'total_amount',
        'notes',
        'transaction_time',
    ];

    protected $casts = [
        'sale_date'        => 'date',
        'transaction_time' => 'datetime',
        'sale_price'       => 'decimal:2',
        'return_price'     => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'quantity'         => 'decimal:3',
    ];

    /**
     * العلاقة مع المنتج
     */
    public function meatProduct()
    {
        return $this->belongsTo(MeatProduct::class);
    }

    /**
     * نطاق البحث حسب التاريخ
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    /**
     * نطاق نوع العملية
     */
    public function scopeTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * حساب صافي المبيعات لفترة معينة
     */
    public static function netSales($startDate, $endDate)
    {
        $sales = self::dateRange($startDate, $endDate)
            ->selectRaw('
                SUM(CASE WHEN transaction_type = "sale" THEN total_amount ELSE 0 END) as total_sales,
                SUM(CASE WHEN transaction_type = "return" THEN total_amount ELSE 0 END) as total_returns,
                (SUM(CASE WHEN transaction_type = "sale" THEN total_amount ELSE 0 END) -
                 SUM(CASE WHEN transaction_type = "return" THEN total_amount ELSE 0 END)) as net_amount
            ')
            ->first();

        return [
            'total_sales'   => $sales->total_sales ?? 0,
            'total_returns' => $sales->total_returns ?? 0,
            'net_amount'    => $sales->net_amount ?? 0,
        ];
    }
}
