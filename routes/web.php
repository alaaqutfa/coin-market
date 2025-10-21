<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'superadmin'])->prefix('admin/users')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('admin.users.index');
    Route::get('/create', [AuthController::class, 'create'])->name('admin.users.create');
    Route::post('/', [AuthController::class, 'store'])->name('admin.users.store');
    Route::delete('/{id}', [AuthController::class, 'destroy'])->name('admin.users.destroy');
});
Route::prefix('admin')->group(function () {
    // رووتس المصادقة
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});
Route::prefix('admin')->middleware(['admin'])->group(function () {
    // Routes الأساسية
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [ProductController::class, 'list'])->name('products.list');
    Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
    Route::post('/products/bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulkStore');
    Route::get('/products/missing', [ProductController::class, 'getMissingProducts'])->name('products.getMissingProducts');
    Route::delete('/product-barcode-log/{id}', [ProductController::class, 'destroyMissing'])->name('product.destroyMissing');
    Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
    Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');
    Route::delete('/products/clean-unused', [ProductController::class, 'cleanUnusedImages'])->name('products.cleanUnused');
    Route::get('/show-catalog', [ProductController::class, 'showCatalog'])->name('showCatalog');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employee_code_list', [EmployeeController::class, 'show_employee_code_list'])->name('employee_code_list');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employee/qr/{id}', [EmployeeController::class, 'showQr'])->name('employee.qr');
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        // لوحة التحكم اليومية
        Route::get('/dashboard-today', [AttendanceController::class, 'dashboardToday'])->name('dashboard.today');

        // التقارير الشهرية
        Route::get('/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('monthly.report');
        Route::get('/monthly-report/{employeeId}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.employee');
        Route::get('/monthly-report/{year}/{month}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.date');
        Route::get('/monthly-report/{employeeId}/{year}/{month}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.full');

        // التقرير الشهري لجميع الموظفين
        Route::get('/monthly-summary', [AttendanceController::class, 'monthlySummary'])->name('monthly.summary');
        Route::get('/attendance/monthly/summary/{year?}/{month?}', [AttendanceController::class, 'monthlySummary'])
            ->name('attendance.monthly.summary');
        Route::get('/monthly-summary/{year}/{month}', [AttendanceController::class, 'monthlySummary'])->name('monthly.summary.date');

        // جداول الدوام
        Route::get('/employee-schedule/{employeeId}', [AttendanceController::class, 'employeeSchedule'])->name('employee.schedule');
        Route::post('/update-schedule', [AttendanceController::class, 'updateEmployeeSchedule'])->name('update.schedule');

        // إعداد الساعات اليومية
        Route::post('/set-daily-hours', [AttendanceController::class, 'setDailyRequiredHours'])->name('set.daily.hours');

        // سجل الحضور (للعرض اليومي)
        Route::get('/today', [AttendanceController::class, 'attendanceToday'])->name('today');
        Route::get('/today-paginated', [AttendanceController::class, 'attendanceTodayPaginated'])->name('today.paginated');
        Route::get('/day', [AttendanceController::class, 'getEmployeeDayAttendance'])->name('viewDayAttendance');

    });
});

Route::get('/', [CustomerController::class, 'home'])->name('customer.home');
Route::get('/products/{id}', [CustomerController::class, 'show'])->name('customer.product.show');
