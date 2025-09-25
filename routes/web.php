<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Routes الأساسية
Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/products', [ProductController::class, 'list'])->name('products.list');
Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');
Route::get('/show-catalog', [ProductController::class, 'showCatalog'])->name('showCatalog');

// Routes الموظفين
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employee_code_list', [EmployeeController::class, 'show_employee_code_list'])->name('employee_code_list');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');


// لوحة تحكم رئيسية للحضور
// صفحة العرض اليومي
// صفحات التقارير الشهرية
// واجهة إدارة جداول الدوام
// واجهة إعداد الساعات اليومية

// Routes الحضور والانصراف (للوحة التحكم)
Route::prefix('attendance')->name('attendance.')->group(function () {
    // لوحة التحكم اليومية
    Route::get('/dashboard-today', [AttendanceController::class, 'dashboardToday'])->name('dashboard.today');

    // التقارير الشهرية
    Route::get('/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('monthly.report');
    Route::get('/monthly-report/{employeeId}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.employee');
    Route::get('/monthly-report/{year}/{month}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.date');
    Route::get('/monthly-report/{employeeId}/{year}/{month}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.full');

    // التقرير الشهري لجميع الموظفين
    Route::get('/monthly-summary', [AttendanceController::class, 'monthlySummary'])->name('monthly.summary');
    Route::get('/monthly-summary/{year}/{month}', [AttendanceController::class, 'monthlySummary'])->name('monthly.summary.date');

    // جداول الدوام
    Route::get('/employee-schedule/{employeeId}', [AttendanceController::class, 'employeeSchedule'])->name('employee.schedule');
    Route::post('/update-schedule', [AttendanceController::class, 'updateEmployeeSchedule'])->name('update.schedule');

    // إعداد الساعات اليومية
    Route::post('/set-daily-hours', [AttendanceController::class, 'setDailyRequiredHours'])->name('set.daily.hours');

    // سجل الحضور (للعرض اليومي)
    Route::get('/today', [AttendanceController::class, 'attendanceToday'])->name('today');
    Route::get('/today-paginated', [AttendanceController::class, 'attendanceTodayPaginated'])->name('today.paginated');
});

