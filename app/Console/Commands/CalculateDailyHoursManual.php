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
    protected $description = 'Calculate Working Hours For Specific Date With Optional Truncate';

    public function handle()
    {
        // الحصول على التاريخ من المدخلات
        $dateInput = $this->argument('date');

        try {
            $date = Carbon::parse($dateInput, 'Asia/Beirut')->toDateString();
            $dayOfWeek = Carbon::parse($dateInput, 'Asia/Beirut')->dayOfWeek;
        } catch (\Exception $e) {
            $this->error("Invalid date format. Please use YYYY-MM-DD format.");
            return 1;
        }

        $this->info("Calculating working hours for date: {$date} (Day: {$dayOfWeek})");

        // تفعيل الـ truncate إذا كانت الخيار --truncate موجود
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

        $employees = Employee::all();
        $totalEmployees = count($employees);
        $processed = 0;
        $employeesWithLogs = 0;
        $totalActualHours = 0;

        $this->info("Processing {$totalEmployees} employees...");

        $progressBar = $this->output->createProgressBar($totalEmployees);
        $progressBar->start();

        foreach ($employees as $employee) {
            // حساب الساعات الفعلية
            $logs = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('date', $date)
                ->get();

            $totalMinutes = 0;
            $logCount = $logs->count();

            if ($this->option('debug') && $logCount > 0) {
                $this->info("\nDebug - Employee {$employee->employee_code}: {$logCount} logs found");
            }

            foreach ($logs as $index => $log) {
                if ($log->check_in && $log->check_out) {
                    $minutes = Carbon::parse($log->check_in)
                        ->diffInMinutes(Carbon::parse($log->check_out));
                    $totalMinutes += $minutes;

                    if ($this->option('debug')) {
                        $this->info("Log {$index}: {$log->check_in} to {$log->check_out} = {$minutes} minutes");
                    }
                } else {
                    if ($this->option('debug')) {
                        $this->warn("Log {$index}: Incomplete - CheckIn: {$log->check_in}, CheckOut: {$log->check_out}");
                    }
                }
            }

            $actualHours = $totalMinutes / 60;

            if ($logCount > 0 && $actualHours > 0) {
                $employeesWithLogs++;
                $totalActualHours += $actualHours;
            }

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
                    'actual_hours'   => round($actualHours, 2),
                    'required_hours' => $requiredHours,
                ]
            );

            if ($this->option('debug') && $logCount > 0) {
                $this->info("Total for {$employee->employee_code}: {$actualHours}h");
            }

            $progressBar->advance();
            $processed++;
        }

        $progressBar->finish();
        $this->newLine();

        // عرض إحصائيات
        $this->info("=== SUMMARY ===");
        $this->info("📅 Date: {$date}");
        $this->info("👥 Total employees: {$totalEmployees}");
        $this->info("📊 Employees with logs: {$employeesWithLogs}");
        $this->info("⏱️  Total actual hours: " . round($totalActualHours, 2) . "h");
        $this->info("✅ Successfully processed {$processed} employees");

        if ($this->option('truncate')) {
            $this->info("📈 Table was truncated and recalculated");
        }

        return 0;
    }
}
