<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat        = deg2rad($lat2 - $lat1);
        $dLon        = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'note'        => 'nullable|string',
            'barcode'     => 'required|string',
            'lat'         => 'required|numeric',
            'lng'         => 'required|numeric',
        ]);

        $employee = Employee::where('employee_code', $request->employee_id)->first();
        if (! $employee) {
            return response()->json(['message' => 'الموظف غير موجود'], 422);
        }

        if ($request->barcode !== 'A123456a@') {
            return response()->json(['message' => 'الباركود غير صحيح'], 422);
        }

        $companyLat = 33.9684253;
        $companyLng = 35.6160169;
        $distance   = $this->haversine($companyLat, $companyLng, $request->lat, $request->lng);

        if ($distance > 0.1) {
            return response()->json(['message' => 'أنت لست في موقع الشركة'], 422);
        }

        $today = now('Asia/Beirut')->toDateString();

        $lastLog = AttendanceLog::where('employee_id', $employee->id)
            ->latest()
            ->first();

        if ($lastLog && ! $lastLog->check_out) {
            return response()->json([
                'message' => 'لا يمكنك تسجيل دخول جديد قبل تسجيل خروج الجلسة السابقة.',
            ], 422);
        }

        $time    = now('Asia/Beirut')->format('H:i');
        $newNote = "{$time} - دخول : " . ($request->note ?? '');

        // إذا كان هناك سجل سابق، نضيف الملاحظة الجديدة مع الاحتفاظ بالقديمة
        if ($lastLog && $lastLog->note) {
            $newNote = $lastLog->note . "\n" . $newNote;
        }

        $log = AttendanceLog::create([
            'employee_id' => $employee->id,
            'date'        => $today,
            'check_in'    => now('Asia/Beirut'),
            'note'        => $newNote,
        ]);

        return response()->json(['message' => 'تم تسجيل الدخول', 'log' => $log]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'note'        => 'nullable|string',
            'barcode'     => 'required|string',
            'lat'         => 'required|numeric',
            'lng'         => 'required|numeric',
        ]);

        $employee = Employee::where('employee_code', $request->employee_id)->first();
        if (! $employee) {
            return response()->json(['message' => 'الموظف غير موجود'], 422);
        }

        if ($request->barcode !== 'A123456a@') {
            return response()->json(['message' => 'الباركود غير صحيح'], 422);
        }

        $companyLat = 33.9684253;
        $companyLng = 35.6160169;
        $distance   = $this->haversine($companyLat, $companyLng, $request->lat, $request->lng);

        if ($distance > 0.1) {
            return response()->json(['message' => 'أنت لست في موقع الشركة'], 422);
        }

        $log = AttendanceLog::where('employee_id', $employee->id)
            ->where('date', now('Asia/Beirut')->toDateString())
            ->whereNull('check_out')
            ->latest()
            ->first();

        if (! $log || ! $log->check_in) {
            return response()->json(['message' => 'لم يتم تسجيل الدخول اليوم بعد'], 422);
        }

        $time    = now('Asia/Beirut')->format('H:i');
        $newNote = "{$time} - خروج : " . ($request->note ?? '');

        if ($log->note) {
            $updatedNote = $log->note . "\n" . $newNote;
        } else {
            $updatedNote = $newNote;
        }

        $log->update([
            'check_out' => now('Asia/Beirut'),
            'note'      => $updatedNote,
        ]);

        $hoursData = $this->recordDailyHours($employee->id, now('Asia/Beirut')->toDateString(), $log->check_in, now('Asia/Beirut'));

        $yesterdayLog = AttendanceLog::where('employee_id', $employee->id)
            ->where('date', now('Asia/Beirut')->subDay()->toDateString())
            ->whereNull('check_out')
            ->first();

        if ($yesterdayLog) {
            $yesterdayNote = $yesterdayLog->note ? $yesterdayLog->note . "\nغياب" : "غياب";
            $yesterdayLog->update([
                'note' => $yesterdayNote,
            ]);
        }

        return response()->json([
            'message'    => 'تم تسجيل الخروج بنجاح',
            'log'        => $log,
            'hours_data' => $hoursData,
        ]);
    }

    public function index(Request $request)
    {
        $employee = $request->user();
        $logs     = AttendanceLog::where('employee_id', $employee->id)
            ->orderByDesc('id')
            ->paginate(30);

        return response()->json($logs);
    }

    public function attendanceToday(Request $request)
    {
        $today = now('Asia/Beirut')->toDateString();

        $logs = AttendanceLog::with('employee')
            ->where('date', $today)
            ->orderByDesc('check_in')
            ->get();

        $formattedLogs = $logs->map(function ($log) {
            return [
                'id'            => $log->id,
                'employee_id'   => $log->employee_id,
                'employee_name' => $log->employee->name ?? 'غير معروف',
                'employee_code' => $log->employee->employee_code ?? 'غير معروف',
                'date'          => $log->date,
                'check_in'      => $log->check_in,
                'check_out'     => $log->check_out,
                'note'          => $log->note,
                'status'        => $log->check_out ? 'مغادر' : 'حاضر',
                'duration'      => $log->check_out ?
                $this->calculateDuration($log->check_in, $log->check_out) :
                'لا يزال حاضراً',
            ];
        });

        return response()->json([
            'date'            => $today,
            'total_records'   => $logs->count(),
            'present_count'   => $logs->whereNull('check_out')->count(),
            'left_count'      => $logs->whereNotNull('check_out')->count(),
            'attendance_logs' => $formattedLogs,
        ]);
    }

    private function calculateDuration($checkIn, $checkOut)
    {
        $start    = Carbon::parse($checkIn, 'Asia/Beirut');
        $end      = Carbon::parse($checkOut, 'Asia/Beirut');
        $duration = $start->diff($end);

        return sprintf('%02d:%02d', $duration->h, $duration->i);
    }

    public function attendanceTodayPaginated(Request $request)
    {
        $today = now('Asia/Beirut')->toDateString();

        $logs = AttendanceLog::with('employee')
            ->where('date', $today)
            ->orderByDesc('check_in')
            ->paginate(50);

        $formattedData = [
            'date'            => $today,
            'total_records'   => $logs->total(),
            'present_count'   => $logs->whereNull('check_out')->count(),
            'left_count'      => $logs->whereNotNull('check_out')->count(),
            'current_page'    => $logs->currentPage(),
            'last_page'       => $logs->lastPage(),
            'per_page'        => $logs->perPage(),
            'attendance_logs' => $logs->map(function ($log) {
                return [
                    'id'            => $log->id,
                    'employee_id'   => $log->employee_id,
                    'employee_name' => $log->employee->name ?? 'غير معروف',
                    'employee_code' => $log->employee->employee_code ?? 'غير معروف',
                    'date'          => $log->date,
                    'check_in'      => $log->check_in,
                    'check_out'     => $log->check_out,
                    'note'          => $log->note,
                    'status'        => $log->check_out ? 'مغادر' : 'حاضر',
                    'duration'      => $log->check_out ?
                    $this->calculateDuration($log->check_in, $log->check_out) :
                    'لا يزال حاضراً',
                ];
            }),
        ];

        return response()->json($formattedData);
    }

    private function isEvenWeek($date)
    {
        $weekNumber = Carbon::parse($date)->weekOfYear;
        return $weekNumber % 2 === 0;
    }

    private function getRequiredHours($employeeId, $date)
    {
        $dayOfWeek  = Carbon::parse($date)->dayOfWeek;
        $isEvenWeek = $this->isEvenWeek($date);

        $schedule = WorkSchedule::where('employee_id', $employeeId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($query) use ($isEvenWeek) {
                $query->where('is_alternate', false)
                    ->orWhere('is_alternate', $isEvenWeek);
            })
            ->orderBy('is_alternate', 'desc')
            ->first();

        return $schedule ? $schedule->work_hours : 0;
    }

    private function recordDailyHours($employeeId, $date, $checkIn, $checkOut)
    {
        $checkInTime   = Carbon::parse($checkIn, 'Asia/Beirut');
        $checkOutTime  = Carbon::parse($checkOut, 'Asia/Beirut');
        $actualHours   = $checkOutTime->diffInMinutes($checkInTime) / 60;
        $requiredHours = $this->getRequiredHours($employeeId, $date);

        DailyWorkHour::updateOrCreate(
            ['employee_id' => $employeeId, 'date' => $date],
            ['required_hours' => $requiredHours, 'actual_hours' => $actualHours]
        );

        return [
            'actual_hours'   => $actualHours,
            'required_hours' => $requiredHours,
        ];
    }

    public function dashboardToday(Request $request)
    {
        $today     = now('Asia/Beirut')->toDateString();
        $dayOfWeek = now('Asia/Beirut')->dayOfWeek;

        $logs = AttendanceLog::with(['employee'])
            ->where('date', $today)
            ->orderByDesc('check_in')
            ->get();

        $totalEmployees    = Employee::count();
        $expectedEmployees = Employee::whereHas('workSchedules', function ($query) use ($dayOfWeek) {
            $query->where('day_of_week', $dayOfWeek);
        })->count();

        $presentEmployees = $logs->count();
        $absentEmployees  = max(0, $expectedEmployees - $presentEmployees);

        $totalRequiredHours = 0;
        $totalActualHours   = 0;

        $formattedLogs = $logs->map(function ($log) use (&$totalRequiredHours, &$totalActualHours) {
            $requiredHours = $this->getRequiredHours($log->employee_id, $log->date);
            $actualHours   = 0;

            if ($log->check_out) {
                $actualHours = Carbon::parse($log->check_in, 'Asia/Beirut')->diffInMinutes(Carbon::parse($log->check_out, 'Asia/Beirut')) / 60;
            }

            $totalRequiredHours += $requiredHours;
            $totalActualHours += $actualHours;

            return [
                'id'                => $log->id,
                'employee_id'       => $log->employee_id,
                'employee_name'     => $log->employee->name,
                'employee_code'     => $log->employee->employee_code,
                'date'              => $log->date,
                'check_in'          => $log->check_in,
                'check_out'         => $log->check_out,
                'required_hours'    => number_format($requiredHours, 2),
                'actual_hours'      => number_format($actualHours, 2),
                'hours_difference'  => number_format($actualHours - $requiredHours, 2),
                'status'            => $log->check_out ? 'مغادر' : 'حاضر',
                'attendance_status' => $requiredHours > 0 ? 'مطلوب' : 'إجازة',
                'note'              => $log->note,
            ];
        });

        return response()->json([
            'date'            => $today,
            'day_name'        => now('Asia/Beirut')->translatedFormat('l'),
            'statistics'      => [
                'total_employees'        => $totalEmployees,
                'expected_employees'     => $expectedEmployees,
                'present_employees'      => $presentEmployees,
                'absent_employees'       => $absentEmployees,
                'attendance_rate'        => $expectedEmployees > 0 ? round(($presentEmployees / $expectedEmployees) * 100, 2) : 0,
                'total_required_hours'   => number_format($totalRequiredHours, 2),
                'total_actual_hours'     => number_format($totalActualHours, 2),
                'total_hours_difference' => number_format($totalActualHours - $totalRequiredHours, 2),
            ],
            'attendance_logs' => $formattedLogs,
        ]);
    }

    public function monthlyReport(Request $request, $employeeId = null, $year = null, $month = null)
    {
        $employeeId = $employeeId ?? $request->user()->id;
        $year       = $year ?? now('Asia/Beirut')->year;
        $month      = $month ?? now('Asia/Beirut')->month;

        $employee  = Employee::findOrFail($employeeId);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        $totalRequiredMonthly = 0;
        $currentDate          = $startDate->copy();
        while ($currentDate <= $endDate) {
            $totalRequiredMonthly += $this->getRequiredHours($employeeId, $currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        $dailyHours = DailyWorkHour::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->get();

        $totalActualHours = $dailyHours->sum('actual_hours');

        return response()->json([
            'employee'      => [
                'id'            => $employee->id,
                'name'          => $employee->name,
                'employee_code' => $employee->employee_code,
            ],
            'period'        => [
                'year'       => $year,
                'month'      => $month,
                'month_name' => $startDate->translatedFormat('F Y'),
            ],
            'summary'       => [
                'monthly_required_hours' => $totalRequiredMonthly,
                'total_actual_hours'     => $totalActualHours,
                'achievement_rate'       => $totalRequiredMonthly > 0 ? round(($totalActualHours / $totalRequiredMonthly) * 100, 2) : 0,
                'working_days'           => $dailyHours->where('required_hours', '>', 0)->count(),
                'off_days'               => $dailyHours->where('required_hours', 0)->count(),
                'average_daily_hours'    => $dailyHours->where('required_hours', '>', 0)->count() > 0 ?
                round($totalActualHours / $dailyHours->where('required_hours', '>', 0)->count(), 2) : 0,
            ],
            'daily_details' => $dailyHours->map(function ($daily) use ($employeeId) {
                $requiredHours = $this->getRequiredHours($employeeId, $daily->date);
                return [
                    'date'           => $daily->date,
                    'day_name'       => Carbon::parse($daily->date, 'Asia/Beirut')->translatedFormat('l'),
                    'required_hours' => $requiredHours,
                    'actual_hours'   => $daily->actual_hours,
                    'difference'     => round($daily->actual_hours - $requiredHours, 2),
                    'day_type'       => $requiredHours > 0 ? 'عمل' : 'إجازة',
                ];
            }),
        ]);
    }

    public function employeeSchedule(Request $request, $employeeId = null)
    {
        $employeeId = $employeeId ?? $request->user()->id;

        $schedules = WorkSchedule::where('employee_id', $employeeId)
            ->orderBy('day_of_week')
            ->orderBy('is_alternate')
            ->get();

        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        $formattedSchedules = $schedules->map(function ($schedule) use ($daysOfWeek) {
            return [
                'day_of_week'   => $schedule->day_of_week,
                'day_name'      => $daysOfWeek[$schedule->day_of_week] ?? 'Unknown',
                'is_alternate'  => (bool) $schedule->is_alternate,
                'schedule_type' => $schedule->is_alternate ? 'متناوب' : 'ثابت',
                'start_time'    => $schedule->start_time,
                'end_time'      => $schedule->end_time,
                'work_hours'    => $schedule->work_hours,
            ];
        });

        return response()->json([
            'employee_id' => $employeeId,
            'schedules'   => $formattedSchedules,
        ]);
    }

    public function updateEmployeeSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'              => 'required|exists:employees,id',
            'schedules'                => 'required|array',
            'schedules.*.day_of_week'  => 'required|integer|between:0,6',
            'schedules.*.is_alternate' => 'sometimes|boolean',
            'schedules.*.start_time'   => 'required|date_format:H:i',
            'schedules.*.end_time'     => 'required|date_format:H:i',
            'schedules.*.work_hours'   => 'required|numeric|min:0|max:24',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $employeeId = $request->employee_id;
        WorkSchedule::where('employee_id', $employeeId)->delete();

        foreach ($request->schedules as $scheduleData) {
            // تحويل القيم إلى النوع الصحيح
            WorkSchedule::create([
                'employee_id'  => $employeeId,
                'day_of_week'  => (int) $scheduleData['day_of_week'],
                'is_alternate' => (bool) $scheduleData['is_alternate'],
                'start_time'   => $scheduleData['start_time'],
                'end_time'     => $scheduleData['end_time'],
                'work_hours'   => (float) $scheduleData['work_hours'],
            ]);
        }

        return response()->json([
            'message'   => 'تم تحديث جدول الدوام بنجاح',
            'schedules' => WorkSchedule::where('employee_id', $employeeId)->get(),
        ]);
    }

    public function setDailyRequiredHours(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|integer|exists:employees,id',
            'date'           => 'required|date',
            'required_hours' => 'required|numeric|min:0|max:24',
        ]);

        $dailyHours = DailyWorkHour::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            ['required_hours' => $request->required_hours]
        );

        return response()->json([
            'message' => 'تم تحديث الساعات المطلوبة بنجاح',
            'data'    => $dailyHours,
        ]);
    }

    public function monthlySummary(Request $request, $year = null, $month = null)
    {
        $year  = $year ?? now('Asia/Beirut')->year;
        $month = $month ?? now('Asia/Beirut')->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = Carbon::create($year, $month, 1)->endOfMonth();

        $employees = Employee::with(['dailyHours' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        }])->get();

        $summary = $employees->map(function ($employee) {
            $totalRequired = $employee->dailyHours->sum('required_hours');
            $totalActual   = $employee->dailyHours->sum('actual_hours');

            return [
                'employee_id'          => $employee->id,
                'employee_name'        => $employee->name,
                'employee_code'        => $employee->employee_code,
                'total_required_hours' => $totalRequired,
                'total_actual_hours'   => $totalActual,
                'achievement_rate'     => $totalRequired > 0 ? round(($totalActual / $totalRequired) * 100, 2) : 0,
                'attendance_days'      => $employee->dailyHours->count(),
                'status'               => $totalActual >= $totalRequired ? 'مكتمل' : 'غير مكتمل',
            ];
        });

        return response()->json([
            'period'            => $startDate->translatedFormat('F Y'),
            'total_employees'   => $employees->count(),
            'employees_summary' => $summary,
        ]);
    }
}
