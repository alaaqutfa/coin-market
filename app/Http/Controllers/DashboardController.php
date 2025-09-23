<?php
namespace App\Http\Controllers;

use App\Models\DailyWorkHour;
use App\Models\Employee;
use App\Models\AttendanceLog;

class DashboardController
{
    public function dashboard()
    {
        $today     = now()->toDateString();
        $dayOfWeek = now()->dayOfWeek;

        $todayStats   = $this->getTodayStats();
        $monthlyStats = $this->getMonthlyStats();

        return view('dashboard', compact('todayStats', 'monthlyStats'));
    }

    /**
 * الحصول على إحصائيات اليوم
 */
private function getTodayStats()
{
    $today = now()->toDateString();
    $dayOfWeek = now()->dayOfWeek;

    $totalEmployees = Employee::count();
    $expectedEmployees = Employee::whereHas('workSchedules', function($query) use ($dayOfWeek) {
        $query->where('day_of_week', $dayOfWeek);
    })->count();

    $presentEmployees = AttendanceLog::where('date', $today)->count();
    $absentEmployees = max(0, $expectedEmployees - $presentEmployees);

    return [
        'total_employees' => $totalEmployees,
        'expected_today' => $expectedEmployees,
        'present_today' => $presentEmployees,
        'absent_today' => $absentEmployees,
        'attendance_rate' => $expectedEmployees > 0 ? round(($presentEmployees / $expectedEmployees) * 100, 2) : 0
    ];
}

/**
 * الحصول على إحصائيات الشهر
 */
private function getMonthlyStats()
{
    $startDate = now()->startOfMonth();
    $endDate = now()->endOfMonth();

    $totalRequired = DailyWorkHour::whereBetween('date', [$startDate, $endDate])->sum('required_hours');
    $totalActual = DailyWorkHour::whereBetween('date', [$startDate, $endDate])->sum('actual_hours');

    return [
        'total_required_hours' => $totalRequired,
        'total_actual_hours' => $totalActual,
        'completion_rate' => $totalRequired > 0 ? round(($totalActual / $totalRequired) * 100, 2) : 0
    ];
}

}
