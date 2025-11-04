<?php
namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateDailyHoursManual extends Command
{
    protected $signature   = 'attendance:calculate-daily-hours-manual {date} {--truncate} {--debug}';
    protected $description = 'Calculate working hours for a specific date with optional truncate and alternate-day handling';

    public function handle()
    {
        $dateInput = $this->argument('date');

        try {
            $dateObj   = Carbon::parse($dateInput, 'Asia/Beirut')->startOfDay();
            $date      = $dateObj->toDateString(); // نص فقط للطباعة أو where
            $dayOfWeek = $dateObj->dayOfWeek;
        } catch (\Exception $e) {
            $this->error("❌ Invalid date format. Please use YYYY-MM-DD format.");
            return 1;
        }

        $this->info("🕒 Calculating working hours for date: {$date} (Day: {$dayOfWeek})");

        // خيار التفريغ الكامل
        if ($this->option('truncate')) {
            $this->warn('⚠️  This will truncate the entire DailyWorkHour table!');
            if ($this->confirm('Are you sure you want to truncate the table?')) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DailyWorkHour::truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $this->info('✅ DailyWorkHour table truncated successfully.');
            } else {
                $this->info('❌ Truncate operation cancelled.');
                return 0;
            }
        }

        $employees         = Employee::whereNull('end_date')->get();
        $totalEmployees    = $employees->count();
        $employeesWithLogs = 0;
        $totalActualHours  = 0;

        $this->info("👥 Found {$totalEmployees} active employees.");
        $progressBar = $this->output->createProgressBar($totalEmployees);
        $progressBar->start();

        foreach ($employees as $employee) {
            // ✅ تخطي الموظف إذا لم يبدأ العمل بعد
            if ($employee->start_date && Carbon::parse($date)->lt(Carbon::parse($employee->start_date))) {
                if ($this->option('debug')) {
                    $this->warn("Skipping {$employee->employee_code} - {$employee->name}: not started yet (starts {$employee->start_date})");
                }
                $progressBar->advance();
                continue;
            }

            // 🔽 السجلات اليومية
            $logs = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->get();

            $totalMinutes = 0;
            foreach ($logs as $index => $log) {
                if ($log->check_in && $log->check_out) {
                    $minutes = Carbon::parse($log->check_in)->diffInMinutes(Carbon::parse($log->check_out));
                    $totalMinutes += $minutes;

                    if ($this->option('debug')) {
                        $this->info("Log {$index}: {$log->check_in} → {$log->check_out} = {$minutes} min");
                    }
                } elseif ($this->option('debug')) {
                    $this->warn("Log {$index}: Incomplete - In: {$log->check_in}, Out: {$log->check_out}");
                }
            }

            $actualHours = round($totalMinutes / 60, 2);
            if ($actualHours > 0) {
                $employeesWithLogs++;
                $totalActualHours += $actualHours;
            }

            // ⏰ جلب الساعات المطلوبة من جدول WorkSchedule
            $workSchedule = WorkSchedule::where('employee_id', $employee->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();

            $requiredHours = $workSchedule ? $workSchedule->work_hours : 0;

            if ($requiredHours == $actualHours) {
                $actualHours += (1 / 60); // زيادة دقيقة واحدة (1 دقيقة = 1/60 من الساعة)
            }

            // ✅ التعامل مع نظام العمل المتناوب بناءً على الأسبوع السابق
            if ($requiredHours > 0 && $actualHours == 0 && $workSchedule && $workSchedule->is_alternate == 1) {
                $previousWeekDate = $dateObj->copy()->subWeek();

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
                    'date'        => $date,
                ],
                [
                    'actual_hours'   => $actualHours,
                    'required_hours' => $requiredHours,
                ]
            );

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("📊 Summary for {$date}:");
        $this->info("👥 Employees processed: {$totalEmployees}");
        $this->info("🧾 Employees with logs: {$employeesWithLogs}");
        $this->info("⏱️  Total actual hours: " . round($totalActualHours, 2) . "h");
        $this->info("✅ Calculation completed successfully!");

        return 0;
    }
}
