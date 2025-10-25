<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    // ==================== PUBLIC METHODS ====================

    public function index()
    {
        $employees = Employee::whereNull('end_date')->orderBy('employee_code', 'desc')->get();
        return view('employees.view', compact('employees'));
    }

    public function show_employee_code_list()
    {
        $employees = Employee::whereNull('end_date')->orderBy('employee_code', 'desc')->get();
        return view('employees.employee_code_list', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function showQr($id)
    {
        $employee = Employee::findOrFail($id);
        return view('design.qr', compact('employee'));
    }

    // ==================== EMPLOYEE DATA METHODS ====================

    public function employeesData(Request $request)
    {
        $employees = Employee::whereNull('end_date')->get();

        $selectedEmployee = null;
        $data             = null;

        if ($request->has('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);

            if ($selectedEmployee) {
                $month = $request->input('month') ?? null;
                $year  = $request->input('year') ?? null;

                $data = $this->getEmployeeMonthlyData($selectedEmployee->employee_code, $month, $year);
            }
        }

        return view('employees.summary', compact('employees', 'selectedEmployee', 'data'));
    }

    public function employeeData(Request $request, $employee_code)
    {
        if (! session('employee_id')) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $employee = Employee::where('employee_code', $employee_code)->first();

        if (! $employee) {
            return redirect()->back()->with('error', 'الموظف غير موجود');
        }

        $data = $this->getEmployeeMonthlyData($employee_code);

        return view('employees.dashboard', compact('data'));
    }

    // ==================== CRUD OPERATIONS ====================

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'employee_code' => ['nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')],
            'salary'        => 'required|numeric',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date',
            'email'         => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')],
            'phone'         => ['nullable', 'string', 'max:30', Rule::unique('employees', 'phone')],
            'password'      => 'required|string|min:6',
        ]);

        $employeeCode = $data['employee_code'] ?? $this->generateUniqueEmployeeCode();

        $employee = Employee::create([
            'name'          => $data['name'],
            'employee_code' => $employeeCode,
            'salary'        => $data['salary'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'] ?? null,
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'password'      => Hash::make($data['password']),
        ]);

        return response()->json([
            'message'  => 'تم إنشاء الموظف',
            'employee' => $employee,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $rules = [
            'name'          => 'sometimes|required|string|max:255',
            'employee_code' => ['sometimes', 'nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($employee->id)],
            'salary'        => 'sometimes|required|numeric',
            'start_date'    => 'sometimes|required|date',
            'end_date'      => 'nullable|date',
            'email'         => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee->id)],
            'phone'         => ['sometimes', 'nullable', 'string', 'max:30', Rule::unique('employees', 'phone')->ignore($employee->id)],
            'password'      => 'sometimes|nullable|string|min:6',
        ];

        $data = $request->validate($rules);

        $updateData = $this->prepareUpdateData($data);

        $employee->update($updateData);

        return response()->json([
            'message'  => 'تم تحديث بيانات الموظف',
            'employee' => $employee,
        ], 200);
    }

    public function resetPassword(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'new_password' => 'required|string|min:4',
        ]);

        $employee->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'تم إعادة تعيين كلمة السر بنجاح',
        ], 200);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'تم حذف الموظف بنجاح',
        ], 200);
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function generateUniqueEmployeeCode()
    {
        do {
            $code = 'EMP' . mt_rand(10000, 99999);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }

    private function prepareUpdateData(array $data): array
    {
        $updateData = [];

        $fields = [
            'name', 'employee_code', 'salary', 'start_date', 'end_date', 'email', 'phone',
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $updateData;
    }

    private function getEmployeeMonthlyData($employee_code, $month = null, $year = null)
    {
        $employee = Employee::where('employee_code', $employee_code)->first();

        if (! $employee) {
            return null;
        }

        $now          = Carbon::now('Asia/Beirut');
        $currentMonth = $month ?? $now->month;
        $currentYear  = $year ?? $now->year;

        $monthNames = [
            1 => 'يناير', 2   => 'فبراير', 3  => 'مارس', 4    => 'أبريل',
            5 => 'مايو', 6    => 'يونيو', 7   => 'يوليو', 8   => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        $currentMonthName = $monthNames[$currentMonth] . ' ' . $currentYear;
        $startDate        = Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
        $endDate          = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();

        // Calculate attendance and absence
        $attendanceDays = DailyWorkHour::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('actual_hours', '>', 0)
            ->count();

        $absentDays = DailyWorkHour::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('required_hours', '>', 0)
            ->where('actual_hours', 0)
            ->count();

        // Calculate hours
        $totalActualHours = DailyWorkHour::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('actual_hours');

        $totalRequiredHours = DailyWorkHour::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('required_hours');

        // Get absent dates
        $absentDates = DailyWorkHour::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('required_hours', '>', 0)
            ->where('actual_hours', 0)
            ->pluck('date')
            ->map(function ($date) {
                return [
                    'date'           => $date,
                    'day_name'       => Carbon::parse($date, 'Asia/Beirut')->translatedFormat('l'),
                    'formatted_date' => Carbon::parse($date, 'Asia/Beirut')->format('d/m/Y'),
                ];
            });

        // Get daily records
        $dailyRecords = DailyWorkHour::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($record) use ($employee) {
                $requiredHours  = $this->getRequiredHours($employee->id, $record->date);
                $attendanceLogs = AttendanceLog::where('employee_id', $employee->id)
                    ->whereDate('date', $record->date)
                    ->orderBy('check_in', 'asc')
                    ->get();

                return [
                    'date'            => $record->date,
                    'day_name'        => Carbon::parse($record->date, 'Asia/Beirut')->translatedFormat('l'),
                    'formatted_date'  => Carbon::parse($record->date, 'Asia/Beirut')->format('d/m/Y'),
                    'required_hours'  => $requiredHours,
                    'actual_hours'    => $record->actual_hours,
                    'difference'      => round($record->actual_hours - $requiredHours, 2),
                    'status'          => $record->actual_hours > 0 ? 'حاضر' : ($requiredHours > 0 ? 'غائب' : 'إجازة'),
                    'attendance_logs' => $attendanceLogs->map(function ($log) {
                        return [
                            'check_in'  => $log->check_in ? Carbon::parse($log->check_in, 'Asia/Beirut')->format('H:i') : '-',
                            'check_out' => $log->check_out ? Carbon::parse($log->check_out, 'Asia/Beirut')->format('H:i') : '-',
                            'note'      => $log->note,
                        ];
                    }),
                ];
            });

        return [
            'employee'      => [
                'id'            => $employee->id,
                'name'          => $employee->name,
                'employee_code' => $employee->employee_code,
            ],
            'current_month' => $currentMonthName,
            'summary'       => [
                'attendance_days'      => $attendanceDays,
                'absent_days'          => $absentDays,
                'total_actual_hours'   => round($totalActualHours, 2),
                'total_required_hours' => round($totalRequiredHours, 2),
                'achievement_rate'     => $totalRequiredHours > 0 ? round(($totalActualHours / $totalRequiredHours) * 100, 2) : 0,
            ],
            'absent_dates'  => $absentDates,
            'daily_records' => $dailyRecords,
            'period'        => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date'   => $endDate->format('Y-m-d'),
            ],
        ];
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

    public function isWorkingDay($employee, Carbon $date)
    {
        $schedule = $employee->workSchedules()
            ->where('day_of_week', $date->dayOfWeek)
            ->get();

        foreach ($schedule as $rule) {
            if (! $rule->is_alternate) {
                return true;
            }

            if ($date->weekOfYear % 2 == 0) {
                return true;
            }
        }

        return false;
    }
}
