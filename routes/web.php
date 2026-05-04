<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
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
});

Route::get('/manager/summary', [EmployeeController::class, 'employeesData'])
    ->middleware(['auth', 'admin'])
    ->name('employee.all');

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
        | Category Management
        |--------------------------------------------------------------------------
        */
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/api/products/{product}/update-category', [CategoryController::class, 'updateCategory'])
            ->name('products.update-category');
        /*
        |--------------------------------------------------------------------------
        | Product Management
        |--------------------------------------------------------------------------
        */
        Route::get('/products', [ProductController::class, 'list'])->name('products.list');
        Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
        Route::get('/json-products', [ProductController::class, 'jsonProducts'])->name('products.json');
        Route::get('/json-filters', [ProductController::class, 'jsonFilters'])->name('products.jsonFilters');
        Route::post('/products/bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulkStore');
        Route::get('/products/missing', [ProductController::class, 'getMissingProducts'])->name('products.getMissingProducts');
        Route::delete('/product-barcode-log/{id}', [ProductController::class, 'destroyMissing'])->name('product.destroyMissing');
        Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
        Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');
        Route::delete('/products/clean-unused', [ProductController::class, 'cleanUnusedImages'])->name('products.cleanUnused');
        Route::get('/show-catalog', [ProductController::class, 'showCatalog'])->name('showCatalog');
        Route::post('/import-products', [ProductController::class, 'importProducts'])->name('products.import');
        Route::post('/import-today-invoices', [ProductController::class, 'importTodayInvoices'])->name('products.importTodayInvoices');

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

        /*
        |--------------------------------------------------------------------------
        | Orders Management
        |--------------------------------------------------------------------------
        */
        Route::prefix('orders')->name('admin.orders.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
            Route::put('/{id}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/{id}/contact', [App\Http\Controllers\Admin\OrderController::class, 'contactCustomer'])->name('contact');
        });
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');
        Route::post('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export');
    });

/*
|--------------------------------------------------------------------------
| Customer-Facing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerController::class, 'home'])->name('customer.home');
Route::prefix('customer')->name('customer.')->group(function () {
    // Auth routes
    Route::get('/check-auth', function () {
        return response()->json(['logged_in' => session()->has('customer_id')]);
    })->name('checkCustomerAuth');
    Route::get('/login', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'register'])->name('register.submit');
    Route::post('/login', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'login'])->name('login.submit');
    Route::post('/register-ajax', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'registerAjax'])->name('register.ajax');
    Route::post('/login-ajax', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'loginAjax'])->name('login.ajax');
    Route::post('/logout', [App\Http\Controllers\Customer\Auth\CustomerAuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Customer\ProfileController::class,
        'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Customer\ProfileController::class,
        'updatePassword'])->name('profile.password');
    // Category & Cart & orders
    Route::get('/filter', [CustomerController::class, 'filter'])->name('filter');
    Route::get('/products/{id}', [CustomerController::class, 'show'])->name('product.show');
    Route::get('/category/{id}/products', [CustomerController::class, 'categoryProducts'])
        ->name('category.products');
    Route::post('/cart/add', [App\Http\Controllers\Customer\CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/order/whatsapp', [App\Http\Controllers\Customer\CartController::class, 'orderViaWhatsApp'])->name('order.whatsapp');
    Route::get('/cart', [App\Http\Controllers\Customer\CartController::class, 'viewCart'])->name('cart.view');
    Route::post('/cart/update/{itemId}', [App\Http\Controllers\Customer\CartController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [App\Http\Controllers\Customer\CartController::class, 'removeCartItem'])->name('cart.remove');
    Route::post('/checkout', [App\Http\Controllers\Customer\CartController::class, 'checkout'])->name('checkout');
    Route::get('/current', [App\Http\Controllers\Customer\CartController::class, 'getCurrentCustomer'])->name('current');
    Route::get('/orders', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('orders');
    Route::get('/order/{order_number}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('order.show');
});

/*
|--------------------------------------------------------------------------
| Employee-Facing Routes
|--------------------------------------------------------------------------
*/
Route::get('/list', [EmployeeController::class, 'show_employee_code_list'])->name('employee_code_list');

// Route::middleware('mobile')->get('/meat-inventory', function () {
//     return view('meat-inventory.index');
// })->name('meat-inventory.index');

// Route::middleware('mobile')->get('/meat-inventory/mobile-app', function () {
//     return view('meat-inventory.apps');
// })->name('meat-inventory.apps');
