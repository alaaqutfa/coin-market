<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
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
        $employee = $request->user();

        $request->validate([
            'note'    => 'nullable|string',
            'barcode' => 'required|string',
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
        ]);

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
        $lastLog = AttendanceLog::where('employee_id', $employee->id)
            ->latest()
            ->first();

        if ($lastLog && ! $lastLog->check_out) {
            return response()->json([
                'message' => 'لا يمكنك تسجيل دخول جديد قبل تسجيل خروج الجلسة السابقة.',
            ], 422);
        }

        // إنشاء سجل جديد لكل تسجيل دخول
        $log = AttendanceLog::create([
            'employee_id' => $employee->id,
            'date'        => $today,
            'check_in'    => now(),
            'note'        => $request->note,
        ]);

        return response()->json(['message' => 'تم تسجيل الدخول', 'log' => $log]);
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user();

        // التحقق من صحة البيانات
        $request->validate([
            'note'    => 'nullable|string',
            'barcode' => 'required|string',
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
        ]);

        // التحقق من الباركود
        if ($request->barcode !== 'A123456a@') {
            return response()->json(['message' => 'الباركود غير صحيح'], 422);
        }

        // التحقق من الموقع
        $companyLat = 33.9684253;
        $companyLng = 35.6160169;
        $distance   = $this->haversine($companyLat, $companyLng, $request->lat, $request->lng);

        if ($distance > 0.1) { // خارج نطاق 100 متر
            return response()->json(['message' => 'أنت لست في موقع الشركة'], 422);
        }

        // البحث عن آخر سجل للموظف بدون خروج
        $log = AttendanceLog::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->whereNull('check_out')
            ->latest()
            ->first();

        // التحقق من وجود تسجيل دخول
        if (! $log || ! $log->check_in) {
            return response()->json(['message' => 'لم يتم تسجيل الدخول اليوم بعد'], 422);
        }

        // تحديث الخروج ودمج الملاحظات
        $log->update([
            'check_out' => now(),
            'note'      => trim(($log->note ?? '') . "\n" . ($request->note ?? '')),
        ]);

        $yesterdayLog = AttendanceLog::where('employee_id', $employee->id)
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
            ->orderByDesc('id') // كل جلسة بشكل مستقل
            ->paginate(30);

        return response()->json($logs);
    }
}
