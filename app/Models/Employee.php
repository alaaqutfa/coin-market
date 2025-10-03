<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'employee_code',
        'salary',
        'start_date',
        'end_date',
        'email',
        'phone',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'start_date' => 'date',
        'salary'     => 'decimal:2',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    // علاقة مع الدور
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // علاقة مع الساعات اليومية
    public function dailyWorkHours()
    {
        return $this->hasMany(DailyWorkHour::class);
    }
}
