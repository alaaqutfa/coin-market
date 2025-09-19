<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'check_in',
        'check_in_photo',
        'check_out',
        'check_out_photo',
        'note',
        'date',
    ];

    protected $dates = ['check_in', 'check_out', 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
