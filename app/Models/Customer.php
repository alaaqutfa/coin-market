<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'address', 'map_link', 'session_id', 'password'];

    protected $hidden = ['password'];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getActiveCartAttribute()
    {
        return $this->carts()->where('status', 'pending')->latest()->first();
    }
}
