<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class ProductBarcodeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'product_id',
        'seen',
        'expires_at'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function deleteSeenLogs(): int
    {
        try {
            $deletedCount = self::where('seen', true)->delete();
            
            Log::info("تم حذف {$deletedCount} سجل مقروء من سجلات الباركود");
            
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error("فشل في حذف السجلات المقروءة - " . $e->getMessage());
            return 0;
        }
    }

    public static function deleteSeenLogsOlderThan(string $date): int
    {
        try {
            $deletedCount = self::where('seen', true)
                ->where('created_at', '<', $date)
                ->delete();
            
            Log::info("تم حذف {$deletedCount} سجل مقروء أقدم من {$date}");
            
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error("فشل في حذف السجلات القديمة - " . $e->getMessage());
            return 0;
        }
    }

    public static function deleteSeenLogsForProduct(int $productId): int
    {
        try {
            $deletedCount = self::where('seen', true)
                ->where('product_id', $productId)
                ->delete();
            
            Log::info("تم حذف {$deletedCount} سجل مقروء للمنتج ID: {$productId}");
            
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error("فشل في حذف السجلات للمنتج ID: {$productId} - " . $e->getMessage());
            return 0;
        }
    }

    public static function deleteSeenLogsForUser(int $userId): int
    {
        try {
            $deletedCount = self::where('seen', true)
                ->where('user_id', $userId)
                ->delete();
            
            Log::info("تم حذف {$deletedCount} سجل مقروء للمستخدم ID: {$userId}");
            
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error("فشل في حذف السجلات للمستخدم ID: {$userId} - " . $e->getMessage());
            return 0;
        }
    }

    public function scopeUnseen($query)
    {
        return $query->where('seen', false);
    }

    public function scopeSeen($query)
    {
        return $query->where('seen', true);
    }

    public function markAsSeen(): void
    {
        $this->update(['seen' => true]);
    }

    public function markAsUnseen(): void
    {
        $this->update(['seen' => false]);
    }
}