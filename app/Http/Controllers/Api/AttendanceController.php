<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // نصف قطر الأرض بالكيلومتر
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

        $employee = Employee::where('employee_code',$request->employee_id)->first();
        if (!$employee) {
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

        $today = now()->toDateString();

        // التحقق من آخر سجل للموظف
        $lastLog = AttendanceLog::where('employee_id', $request->employee_id)
            ->latest()
            ->first();

        if ($lastLog && ! $lastLog->check_out) {
            return response()->json([
                'message' => 'لا يمكنك تسجيل دخول جديد قبل تسجيل خروج الجلسة السابقة.',
            ], 422);
        }

        $time    = now()->format('H:i');
        $newNote = "{$time} - دخول : " . ($request->note ?? '');

        // إنشاء سجل جديد لكل تسجيل دخول
        $log = AttendanceLog::create([
            'employee_id' => $request->employee_id,
            'date'        => $today,
            'check_in'    => now(),
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

        $employee = Employee::where('employee_code',$request->employee_id)->first();
        if (!$employee) {
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

        // البحث عن آخر سجل للموظف بدون خروج
        $log = AttendanceLog::where('employee_id', $request->employee_id)
            ->where('date', now()->toDateString())
            ->whereNull('check_out')
            ->latest()
            ->first();

        // التحقق من وجود تسجيل دخول
        if (! $log || ! $log->check_in) {
            return response()->json(['message' => 'لم يتم تسجيل الدخول اليوم بعد'], 422);
        }

        $time    = now()->format('H:i');
        $newNote = "{$time} - خروج : " . ($request->note ?? '');

        // تحديث الخروج ودمج الملاحظات
        $log->update([
            'check_out' => now(),
            'note'      => $newNote,
        ]);

        $yesterdayLog = AttendanceLog::where('employee_id', $request->employee_id)
            ->where('date', now()->subDay()->toDateString())
            ->whereNull('check_out')
            ->first();

        if ($yesterdayLog) {
            $yesterdayLog->update([
                'note' => trim(($yesterdayLog->note ?? '') . "\n" . 'غياب'),
            ]);
        }

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح',
            'log'     => $log,
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

    // دالة جديدة لعرض جميع حركات اليوم مع أسماء الموظفين
    public function attendanceToday(Request $request)
    {
        $today = now()->toDateString();

        // جلب جميع سجلات الحضور لليوم مع معلومات الموظفين
        $logs = AttendanceLog::with('employee')
            ->where('date', $today)
            ->orderByDesc('check_in')
            ->get();

        // تنسيق البيانات للإرجاع
        $formattedLogs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'employee_id' => $log->employee_id,
                'employee_name' => $log->employee->name ?? 'غير معروف',
                'employee_code' => $log->employee->employee_code ?? 'غير معروف',
                'date' => $log->date,
                'check_in' => $log->check_in,
                'check_out' => $log->check_out,
                'note' => $log->note,
                'status' => $log->check_out ? 'مغادر' : 'حاضر',
                'duration' => $log->check_out ?
                    $this->calculateDuration($log->check_in, $log->check_out) :
                    'لا يزال حاضراً'
            ];
        });

        return response()->json([
            'date' => $today,
            'total_records' => $logs->count(),
            'present_count' => $logs->whereNull('check_out')->count(),
            'left_count' => $logs->whereNotNull('check_out')->count(),
            'attendance_logs' => $formattedLogs
        ]);
    }

    // دالة مساعدة لحساب المدة بين الدخول والخروج
    private function calculateDuration($checkIn, $checkOut)
    {
        $start = \Carbon\Carbon::parse($checkIn);
        $end = \Carbon\Carbon::parse($checkOut);
        $duration = $start->diff($end);

        return sprintf('%02d:%02d', $duration->h, $duration->i);
    }

    // إذا أردت إصدار مع pagination
    public function attendanceTodayPaginated(Request $request)
    {
        $today = now()->toDateString();

        $logs = AttendanceLog::with('employee')
            ->where('date', $today)
            ->orderByDesc('check_in')
            ->paginate(50);

        // تنسيق البيانات مع pagination
        $formattedData = [
            'date' => $today,
            'total_records' => $logs->total(),
            'present_count' => $logs->whereNull('check_out')->count(),
            'left_count' => $logs->whereNotNull('check_out')->count(),
            'current_page' => $logs->currentPage(),
            'last_page' => $logs->lastPage(),
            'per_page' => $logs->perPage(),
            'attendance_logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'employee_id' => $log->employee_id,
                    'employee_name' => $log->employee->name ?? 'غير معروف',
                    'employee_code' => $log->employee->employee_code ?? 'غير معروف',
                    'date' => $log->date,
                    'check_in' => $log->check_in,
                    'check_out' => $log->check_out,
                    'note' => $log->note,
                    'status' => $log->check_out ? 'مغادر' : 'حاضر',
                    'duration' => $log->check_out ?
                        $this->calculateDuration($log->check_in, $log->check_out) :
                        'لا يزال حاضراً'
                ];
            })
        ];

        return response()->json($formattedData);
    }
}
