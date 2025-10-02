<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductBarcodeLog extends Model
{
    use HasFactory;

    // الأعمدة اللي مسموح تعبئتها مباشرة
    protected $fillable = [
        'barcode',
        'exists',
        'source',
        'user_id',
    ];

    /**
     * العلاقة مع المستخدم (إذا مسجل)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * مثال دالة لتسجيل لوج مؤقت
     */
    public static function createLog(string $barcode, bool $exists = false, string $source = 'manual', $userId = null)
    {
        return self::create([
            'barcode' => $barcode,
            'exists'  => $exists,
            'source'  => $source,
            'user_id' => $userId,
        ]);
    }
}
