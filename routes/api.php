<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/employee/logout', [AuthController::class, 'logout']);
    Route::post('/employee/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/employee/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/employee/attendance', [AttendanceController::class, 'index']);
});

Route::apiResource('products', ProductController::class);
Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
Route::put('products/barcode/{barcode}', [ProductController::class, 'updateByBarcode']);

// route اختبار
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
