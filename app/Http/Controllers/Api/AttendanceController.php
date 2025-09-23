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

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

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

        // تحقق من الباركود (مثلاً رمز ثابت أو موجود بجدول)
        if ($request->barcode !== 'A123456a@') {
            return response()->json(['message' => 'الباركود غير صحيح'], 422);
        }

        // تحقق من الموقع (مثلاً مركز الشركة)
        $companyLat = 33.9684253;
        $companyLng = 35.6160169;
        $distance   = $this->haversine($companyLat, $companyLng, $request->lat, $request->lng);

        if ($distance > 0.1) { // المسافة بالكيلومتر (100 متر)
            return response()->json(['message' => 'أنت لست في موقع الشركة'], 422);
        }

        $today    = now()->toDateString();
        $existing = AttendanceLog::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            return response()->json(['message' => 'تم تسجيل الدخول اليوم سابقاً'], 422);
        }

        $log = AttendanceLog::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            [
                'check_in' => now(),
                'note'     => $request->note,
            ]
        );

        return response()->json(['message' => 'تم تسجيل الدخول', 'log' => $log]);
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user();
        $request->validate([
            'note'    => 'nullable|string',
            'barcode' => 'required|string',
            'lat'     => 'required|numeric',
            'lng'     => 'required|numeric',
        ]);

        if ($request->barcode !== 'YOUR_BARCODE_VALUE') {
            return response()->json(['message' => 'الباركود غير صحيح'], 422);
        }

        $companyLat = 33.5000;
        $companyLng = 36.3000;
        $distance   = $this->haversine($companyLat, $companyLng, $request->lat, $request->lng);

        if ($distance > 0.1) {
            return response()->json(['message' => 'أنت لست في موقع الشركة'], 422);
        }

        $today = now()->toDateString();
        $log   = AttendanceLog::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (! $log || ! $log->check_in) {
            return response()->json(['message' => 'لم يتم تسجيل الدخول اليوم بعد'], 422);
        }

        if ($log->check_out) {
            return response()->json(['message' => 'تم تسجيل الخروج سابقاً'], 422);
        }

        $log->update([
            'check_out' => now(),
            'note'      => $request->note ?? $log->note,
        ]);

        return response()->json(['message' => 'تم تسجيل الخروج', 'log' => $log]);
    }

    public function index(Request $request)
    {
        $employee = $request->user();
        $logs     = AttendanceLog::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->paginate(30);

        return response()->json($logs);
    }
}
