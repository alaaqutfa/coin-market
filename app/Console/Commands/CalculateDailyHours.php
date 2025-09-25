<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use Carbon\Carbon;

class CalculateDailyHours extends Command
{
    protected $signature = 'attendance:calculate-daily-hours';
    protected $description = 'حساب ساعات العمل اليومية لكل موظف وتخزينها في جدول DailyWorkHour';

    public function handle()
    {
        $date = Carbon::now('Asia/Beirut')->toDateString();

        $this->info("Start Calculate Working Hours - {$date}");

        $employees = Employee::all();

        foreach ($employees as $employee) {
            $logs = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->get();

            $totalMinutes = 0;

            foreach ($logs as $log) {
                if ($log->check_in && $log->check_out) {
                    $totalMinutes += Carbon::parse($log->check_in)
                        ->diffInMinutes(Carbon::parse($log->check_out));
                }
            }

            $actualHours = $totalMinutes / 60;

            DailyWorkHour::updateOrCreate(
                ['employee_id' => $employee->id, 'date' => $date],
                ['actual_hours' => $actualHours]
            );

            $this->info("{$employee->name}: {$actualHours}");
        }

        $this->info("Done ✅");
    }
}
