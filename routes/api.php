<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DailySalesController;
use App\Http\Controllers\Api\MeatInventoryController;
use App\Http\Controllers\Api\MeatProductController;
use App\Http\Controllers\Api\MeatPurchaseController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
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
        Route::get('/{meatPurchase}/details', [MeatPurchaseController::class, 'showDetails'])->name('showDetails');
        Route::get('/{meatPurchase}/print', [MeatPurchaseController::class, 'print'])->name('print');
    });

    // مسارات حركات المخزون
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/movements', [MeatInventoryController::class, 'index'])->name('movements.index');
        Route::post('/sales', [MeatInventoryController::class, 'recordSale'])->name('sales.record');
        Route::post('/returns', [MeatInventoryController::class, 'recordReturn'])->name('returns.record');
        Route::post('/waste', [MeatInventoryController::class, 'recordWaste'])->name('waste.record');
        Route::get('/reports/daily', [MeatInventoryController::class, 'dailyReport'])->name('reports.daily');
        Route::get('/reports/range', [MeatInventoryController::class, 'rangeReport'])->name('reports.range');
    });

    // مسارات المبيعات اليومية
    Route::prefix('daily-sales')->name('daily-sales.')->group(function () {
        Route::get('/create', [DailySalesController::class, 'create'])->name('create');
        Route::post('/store', [DailySalesController::class, 'store'])->name('store');
        Route::get('/report', [DailySalesController::class, 'report'])->name('report');
        Route::get('/daily-summary', [DailySalesController::class, 'dailySummary'])->name('daily-summary');
        // إضافة هذا المسار لتحديث معلومات المنتج
        Route::get('/products/get-stock', function (Request $request) {
            $product = \App\Models\MeatProduct::find($request->input('product_id'));

            if ($product) {
                return response()->json([
                    'success' => true,
                    'stock'   => $product->current_stock,
                ]);
            }

            return response()->json(['success' => false]);
        })->name('products.get-stock');
    });

});
