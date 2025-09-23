<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyWorkHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'required_hours',
        'actual_hours',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // العلاقة مع الموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
