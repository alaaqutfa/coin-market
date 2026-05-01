<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'cart_id', 'status',
        'rejection_reason', 'total', 'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // توليد رقم طلب فريد
    public static function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . rand(100, 999);
    }
}
