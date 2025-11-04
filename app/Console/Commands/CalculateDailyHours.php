<?php
namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateDailyHours extends Command
{
    protected $signature   = 'attendance:calculate-daily-hours {--debug}';
    protected $description = 'Calculate working hours for the previous day and store them in DailyWorkHour';

    public function handle()
    {
        // نحدد اليوم السابق (أمس)
        $date      = Carbon::now('Asia/Beirut')->subDay();
        $dayOfWeek = $date->dayOfWeek;

        $this->info("🕒 Calculating working hours for {$date->toDateString()} (Day: {$dayOfWeek})");

        $employees = Employee::whereNull('end_date')->get();
        $this->info("👥 Found {$employees->count()} active employees.");

        foreach ($employees as $employee) {
            // ✅ تخطي الموظف إذا لم يبدأ العمل بعد
            if ($employee->start_date && $date->lt(Carbon::parse($employee->start_date))) {
                if ($this->option('debug')) {
                    $this->warn("Skipping {$employee->employee_code} - {$employee->name}: not started yet (starts {$employee->start_date})");
                }
                continue;
            }

            // 🔽 السجلات اليومية
            $logs = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('date', $date->toDateString())
                ->get();

            $totalMinutes = 0;
            foreach ($logs as $log) {
                if ($log->check_in && $log->check_out) {
                    $minutes = Carbon::parse($log->check_in)->diffInMinutes(Carbon::parse($log->check_out));
                    $totalMinutes += $minutes;

                    if ($this->option('debug')) {
                        $this->line("{$employee->employee_code}: Log {$log->check_in} → {$log->check_out} = {$minutes} min");
                    }
                }
            }

            $actualHours = round($totalMinutes / 60, 2);

            // ⏰ الساعات المطلوبة من جدول WorkSchedule
            $workSchedule = WorkSchedule::where('employee_id', $employee->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            $requiredHours = $workSchedule ? $workSchedule->work_hours : 0;

            if ($requiredHours > 0 && $requiredHours == $actualHours) {
                $actualHours += (1 / 60);
            }

            // ✅ التعامل مع نظام العمل المتناوب بناءً على الأسبوع السابق
            if ($requiredHours > 0 && $actualHours == 0 && $workSchedule && $workSchedule->is_alternate == 1) {
                $previousWeekDate = $date->copy()->subWeek();

                $previousRecord = DailyWorkHour::where('employee_id', $employee->id)
                    ->where(DB::raw('DATE(`date`)'), '=', $previousWeekDate->toDateString())
                    ->first();

                $previousActual   = $previousRecord ? $previousRecord->actual_hours : 0;
                $previousRequired = $previousRecord ? $previousRecord->required_hours : 0;

                // ✅ منطق التناوب: إذا الأسبوع الماضي كانت الساعات المنجزة = الساعات المطلوبة → هذا الأسبوع راحة
                // أما إذا كانت مختلفة → هذا الأسبوع عمل
                if ($previousActual == $previousRequired && $previousRequired > 0) {
                    $actualHours = 0;
                    if ($this->option('debug')) {
                        $this->warn("{$employee->employee_code} - {$employee->name}: alternate rest week (previous week was full work {$previousActual}h)");
                    }
                } else {
                    $actualHours = $requiredHours;
                    if ($this->option('debug')) {
                        $this->info("{$employee->employee_code} - {$employee->name}: alternate work week (no logs but counted {$requiredHours}h)");
                    }
                }
            }

            // 🧾 الحفظ أو التحديث في DailyWorkHour
            DailyWorkHour::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date'        => $date->toDateString(),
                ],
                [
                    'actual_hours'   => $actualHours,
                    'required_hours' => $requiredHours,
                ]
            );

            if ($this->option('debug')) {
                $this->info("✅ {$employee->employee_code}: Actual {$actualHours}h / Required {$requiredHours}h");
            }
        }

        $this->info("✅ Daily calculation completed for {$date->toDateString()}!");
    }
}
