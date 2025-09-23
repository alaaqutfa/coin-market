<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Routes لا تتطلب مصادقة
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

// Routes المنتجات
Route::apiResource('products', ProductController::class);
Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
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
