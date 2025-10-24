<?php
namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateDailyHoursRangeManual extends Command
{
    protected $signature   = 'attendance:calculate-daily-hours-range-manual {date_from} {date_to} {--truncate} {--debug}';
    protected $description = 'Calculate working hours for a range of dates and store them in DailyWorkHour';

    public function handle()
    {
        $dateFromInput = $this->argument('date_from');
        $dateToInput   = $this->argument('date_to');

        // التحقق من صحة التاريخين
        try {
            $dateFrom = Carbon::parse($dateFromInput, 'Asia/Beirut')->startOfDay();
            $dateTo   = Carbon::parse($dateToInput, 'Asia/Beirut')->endOfDay();
        } catch (\Exception $e) {
            $this->error('❌ Invalid date format. Please use YYYY-MM-DD.');
            return 1;
        }

        $this->info("🕒 Calculating working hours for range: {$dateFrom->toDateString()} → {$dateTo->toDateString()}");

        // خيار تفريغ الجدول
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

        $employees      = Employee::whereNull('end_date')->get();
        $totalEmployees = $employees->count();

        $this->info("👥 Found {$totalEmployees} active employees.");

        // نمرّ على كل يوم ضمن النطاق
        $period = new \DatePeriod(
            $dateFrom,
            new \DateInterval('P1D'),
            $dateTo->copy()->addDay()
        );

        foreach ($period as $date) {
            $date = Carbon::instance($date);
            $dayOfWeek = $date->dayOfWeek; // 0=Sunday ... 6=Saturday
            $this->line("\n📅 Processing date: {$date->toDateString()}");

            $progressBar = $this->output->createProgressBar($totalEmployees);
            $progressBar->start();

            $employeesWithLogs = 0;
            $totalActualHours  = 0;

            foreach ($employees as $employee) {
                // ✅ تخطي الموظف إذا لم يبدأ العمل بعد
                if ($employee->start_date && Carbon::parse($date)->lt(Carbon::parse($employee->start_date))) {
                    if ($this->option('debug')) {
                        $this->warn("Skipping {$employee->employee_code} - {$employee->name}: not started yet (starts {$employee->start_date})");
                    }
                    $progressBar->advance();
                    continue;
                }

                // ⏰ جلب الساعات المطلوبة من جدول WorkSchedule
                $workSchedule = WorkSchedule::where('employee_id', $employee->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->first();

                // 🔽 السجلات اليومية
                $logs = AttendanceLog::where('employee_id', $employee->id)
                    ->whereDate('date', $date->toDateString())
                    ->get();

                $totalMinutes = 0;
                $logCount     = $logs->count();

                if ($this->option('debug') && $logCount > 0) {
                    $this->info("\nDebug - Employee {$employee->employee_code} - {$employee->name}: {$logCount} logs found");
                }

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
                if ($logCount > 0 && $actualHours > 0) {
                    $employeesWithLogs++;
                    $totalActualHours += $actualHours;
                }

                $requiredHours = $workSchedule ? $workSchedule->work_hours : 0;

                // ✅ التعامل مع الأيام المتناوبة
                if ($requiredHours > 0 && $actualHours == 0) {
                    if ($workSchedule->is_alternate == 1) {
                        $actualHours = $requiredHours;
                        if ($this->option('debug')) {
                            $this->warn("{$employee->employee_code} - {$employee->name}: paid day (no logs but counted as worked {$requiredHours}h)");
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

                if ($this->option('debug') && $logCount > 0) {
                    $this->info("Total for {$employee->employee_code}: {$actualHours}h");
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info("📊 {$date->toDateString()} → Employees with logs: {$employeesWithLogs}, Total hours: {$totalActualHours}");
        }

        $this->info("\n✅ Range calculation completed successfully!");
        return 0;
    }
}
