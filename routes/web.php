<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin User Management (Super Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'superadmin'])
    ->prefix('admin/users')
    ->group(function () {
        Route::get('/', [AuthController::class, 'index'])->name('admin.users.index');
        Route::get('/create', [AuthController::class, 'create'])->name('admin.users.create');
        Route::post('/', [AuthController::class, 'store'])->name('admin.users.store');
        Route::delete('/{id}', [AuthController::class, 'destroy'])->name('admin.users.destroy');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showCustomerLogin'])->name('login');

Route::prefix('employee')->group(function () {
    Route::get('/login', [AuthController::class, 'showEmployeeLogin'])->name('employee.login');
    Route::get('/{employee_code}', [EmployeeController::class, 'employeeData'])->name('employee.show');
    Route::get('/manager/summary', [EmployeeController::class, 'employeesData'])->name('employee.all')->withoutMiddleware(['auth']);
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Admin Panel (Protected by 'auth' & 'admin' Middleware)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Product Management
        |--------------------------------------------------------------------------
        */
        Route::get('/products', [ProductController::class, 'list'])->name('products.list');
        Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
        Route::post('/products/bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulkStore');
        Route::get('/products/missing', [ProductController::class, 'getMissingProducts'])->name('products.getMissingProducts');
        Route::delete('/product-barcode-log/{id}', [ProductController::class, 'destroyMissing'])->name('product.destroyMissing');
        Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
        Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');
        Route::delete('/products/clean-unused', [ProductController::class, 'cleanUnusedImages'])->name('products.cleanUnused');
        Route::get('/show-catalog', [ProductController::class, 'showCatalog'])->name('showCatalog');

        /*
        |--------------------------------------------------------------------------
        | Employee Management
        |--------------------------------------------------------------------------
        */
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/employee/qr/{id}', [EmployeeController::class, 'showQr'])->name('employee.qr');
        Route::post('/employees/{id}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.reset-password');
        /*
        |--------------------------------------------------------------------------
        | Attendance Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('attendance')->name('attendance.')->group(function () {
            // CRUD operations
            Route::put('/{id}', [AttendanceController::class, 'update'])->name('update');
            Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('destroy');

            // Daily dashboard
            Route::get('/dashboard-today', [AttendanceController::class, 'dashboardToday'])->name('dashboard.today');

            // Monthly reports
            Route::get('/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('monthly.report');
            Route::get('/monthly-report/{employeeId}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.employee');
            Route::get('/monthly-report/{year}/{month}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.date');
            Route::get('/monthly-report/{employeeId}/{year}/{month}', [AttendanceController::class, 'monthlyReport'])->name('monthly.report.full');

            // Monthly summary for all employees
            Route::get('/monthly-summary', [AttendanceController::class, 'monthlySummary'])->name('monthly.summary');
            Route::get('/attendance/monthly/summary/{year?}/{month?}', [AttendanceController::class, 'monthlySummary'])->name('attendance.monthly.summary');
            Route::get('/monthly-summary/{year}/{month}', [AttendanceController::class, 'monthlySummary'])->name('monthly.summary.date');

            // Work schedule
            Route::get('/employee-schedule/{employeeId}', [AttendanceController::class, 'employeeSchedule'])->name('employee.schedule');
            Route::post('/update-schedule', [AttendanceController::class, 'updateEmployeeSchedule'])->name('update.schedule');

            // Daily required hours setup
            Route::post('/set-daily-hours', [AttendanceController::class, 'setDailyRequiredHours'])->name('set.daily.hours');

            // Daily attendance
            Route::get('/today', [AttendanceController::class, 'attendanceToday'])->name('today');
            Route::get('/today-paginated', [AttendanceController::class, 'attendanceTodayPaginated'])->name('today.paginated');
            Route::get('/day', [AttendanceController::class, 'getEmployeeDayAttendance'])->name('viewDayAttendance');
        });
    });

/*
|--------------------------------------------------------------------------
| Customer-Facing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerController::class, 'home'])->name('customer.home');
Route::get('/filter', [CustomerController::class, 'filter'])->name('customer.filter');
Route::get('/products/{id}', [CustomerController::class, 'show'])->name('customer.product.show');

/*
|--------------------------------------------------------------------------
| Employee-Facing Routes
|--------------------------------------------------------------------------
*/
Route::get('/list', [EmployeeController::class, 'show_employee_code_list'])->name('employee_code_list');
