<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeatInventoryController;
use App\Http\Controllers\Api\MeatProductController;
use App\Http\Controllers\Api\MeatPurchaseController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Routes لا تتطلب مصادقة
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

// Routes المنتجات
Route::apiResource('products', ProductController::class);
Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('products.findByBarcode');
Route::put('products/barcode/{barcode}', [ProductController::class, 'updateByBarcode']);

// Routes الحضور والانصراف (بدون مصادقة)
Route::post('/employee/check-in', [AttendanceController::class, 'checkIn']);
Route::post('/employee/check-out', [AttendanceController::class, 'checkOut']);
Route::get('/employee/attendance-today', [AttendanceController::class, 'attendanceToday']);
Route::get('/employee/attendance-today-paginated', [AttendanceController::class, 'attendanceTodayPaginated']);

// Routes تتطلب مصادقة (إذا كنت تحتاجها لشيء آخر)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/employee/attendance', [AttendanceController::class, 'index']);
    Route::post('/employee/logout', [AuthController::class, 'logout']);
});

// مجموعة مسارات نظام الملحمه
Route::prefix('meat-inventory')->name('meat-inventory.')->group(function () {

    // مسارات المنتجات
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [MeatProductController::class, 'index'])->name('index');
        Route::post('/', [MeatProductController::class, 'store'])->name('store');
        Route::get('/{meatProduct}', [MeatProductController::class, 'show'])->name('show');
        Route::put('/{meatProduct}', [MeatProductController::class, 'update'])->name('update');
        Route::delete('/{meatProduct}', [MeatProductController::class, 'destroy'])->name('destroy');
    });

    // مسارات فواتير الشراء
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [MeatPurchaseController::class, 'index'])->name('index');
        Route::post('/', [MeatPurchaseController::class, 'store'])->name('store');
        Route::get('/{meatPurchase}', [MeatPurchaseController::class, 'show'])->name('show');
        Route::put('/{meatPurchase}', [MeatPurchaseController::class, 'update'])->name('update');
        Route::delete('/{meatPurchase}', [MeatPurchaseController::class, 'destroy'])->name('destroy');
    });

    // مسارات حركات المخزون
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/movements', [MeatInventoryController::class, 'index'])->name('movements.index');
        Route::post('/sales', [MeatInventoryController::class, 'recordSale'])->name('sales.record');
        Route::post('/returns', [MeatInventoryController::class, 'recordReturn'])->name('returns.record');
        Route::post('/waste', [MeatInventoryController::class, 'recordWaste'])->name('waste.record');
        Route::get('/reports/daily', [MeatInventoryController::class, 'dailyReport'])->name('reports.daily');
    });

});
