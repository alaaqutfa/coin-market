<?php
namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateDailyHours extends Command
{
    protected $signature   = 'attendance:calculate-daily-hours';
    protected $description = 'Calculate Working Hours And Store In DailyWorkHour';

    public function handle()
    {
        $date = Carbon::now('Asia/Beirut')->toDateString();
        $dayOfWeek = Carbon::now('Asia/Beirut')->dayOfWeek; // 0 (الأحد) إلى 6 (السبت)

        // $date      = Carbon::now('Asia/Beirut')->subDay()->toDateString();
        // $dayOfWeek = Carbon::now('Asia/Beirut')->subDay()->dayOfWeek;

        $this->info("Start Calculate Working Hours - {$date} (Day: {$dayOfWeek})");

        $employees = Employee::all();

        foreach ($employees as $employee) {
            // حساب الساعات الفعلية
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

            // الحصول على الساعات المطلوبة من WorkSchedule
            $workSchedule = WorkSchedule::where('employee_id', $employee->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            $requiredHours = $workSchedule ? $workSchedule->work_hours : 0;

            // حفظ البيانات في DailyWorkHour
            DailyWorkHour::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date'        => $date,
                ],
                [
                    'actual_hours'   => $actualHours,
                    'required_hours' => $requiredHours,
                ]
            );

            $this->info("{$employee->employee_code}: Actual: {$actualHours}h, Required: {$requiredHours}h");
        }

        $this->info("Done ✅");
    }
}
