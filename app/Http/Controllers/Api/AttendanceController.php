<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function checkIn(Request $request)
    {
        $employee = $request->user();
        $request->validate([
            'note' => 'nullable|string',
        ]);

        $today = Carbon::now()->toDateString();

        $existing = AttendanceLog::where('employee_id', $employee->id)
                    ->where('date', $today)
                    ->first();

        if ($existing && $existing->check_in) {
            return response()->json(['message' => 'تم تسجيل الدخول اليوم سابقاً'], 422);
        }

        $log = AttendanceLog::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            [
                'check_in' => Carbon::now(),
                'note' => $request->input('note'),
            ]
        );

        return response()->json(['message' => 'تم تسجيل الدخول', 'log' => $log]);
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user();
        $request->validate([
            'note' => 'nullable|string',
        ]);

        $today = Carbon::now()->toDateString();

        $log = AttendanceLog::where('employee_id', $employee->id)
                ->where('date', $today)
                ->first();

        if (!$log || !$log->check_in) {
            return response()->json(['message' => 'لم يتم تسجيل الدخول اليوم بعد'], 422);
        }

        if ($log->check_out) {
            return response()->json(['message' => 'تم تسجيل الخروج سابقاً'], 422);
        }

        $log->update([
            'check_out' => Carbon::now(),
            'note' => $request->input('note') ?? $log->note,
        ]);

        return response()->json(['message' => 'تم تسجيل الخروج', 'log' => $log]);
    }

    public function index(Request $request)
    {
        $employee = $request->user();
        $logs = AttendanceLog::where('employee_id', $employee->id)
                ->orderByDesc('date')
                ->paginate(30);

        return response()->json($logs);
    }
}
