<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'employee_code',
        'salary',
        'start_date',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'start_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
