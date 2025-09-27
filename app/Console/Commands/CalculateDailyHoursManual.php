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
    protected $signature   = 'attendance:calculate-daily-hours-manual {date} {--truncate}';
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

        $this->info("Processing {$totalEmployees} employees...");

        $progressBar = $this->output->createProgressBar($totalEmployees);
        $progressBar->start();

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
                    'actual_hours'   => round($actualHours, 2),
                    'required_hours' => $requiredHours,
                ]
            );

            $progressBar->advance();
            $processed++;
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Successfully processed {$processed} employees for date: {$date}");

        if ($this->option('truncate')) {
            $this->info("📊 Table was truncated and recalculated for date: {$date}");
        } else {
            $this->info("📊 Data updated/inserted for date: {$date}");
        }

        return 0;
    }
}
