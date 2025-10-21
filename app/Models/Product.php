<?php
namespace App\Models;

use App\Models\ProductBarcodeLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'name',
        'price',
        'symbol',
        'quantity',
        'weight',
        'image_path',
        'social_media_urls',
        'category_id',
        'brand_id',
    ];

    protected $casts = [
        'social_media_urls' => 'array',
        'price'             => 'decimal:2',
    ];

    public function barcodeLogs()
    {
        return $this->hasMany(ProductBarcodeLog::class, 'barcode', 'barcode');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
