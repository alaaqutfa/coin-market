<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\ApiOrderController;
use App\Http\Controllers\Api\ApiEmployeeController;
use Illuminate\Support\Facades\Route;

// Routes لا تتطلب مصادقة
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

// Routes المنتجات
Route::apiResource('products', ProductController::class);
Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('products.findByBarcode');
Route::put('products/barcode/{barcode}', [ProductController::class, 'updateByBarcode']);
Route::post('products/preview-images', [ProductController::class, 'previewImages']);
Route::post('products/save-images', [ProductController::class, 'saveImages']);
Route::post('/import-today-invoices', [ProductController::class, 'importTodayInvoices'])->name('products.importTodayInvoices');


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

Route::prefix('employee')->group(function () {
    // existing endpoints (orders, statistics)...
    Route::get('/orders', [ApiOrderController::class, 'index']);
    Route::get('/orders/{id}', [ApiOrderController::class, 'show']);
    Route::put('/orders/{id}/status', [ApiOrderController::class, 'updateStatus']);
    Route::get('/statistics', [ApiOrderController::class, 'statistics']);

    // new endpoints for full employee access
    Route::get('/customers', [ApiEmployeeController::class, 'customers']);
    Route::get('/customers/{id}', [ApiEmployeeController::class, 'showCustomer']);
    Route::put('/customers/{id}', [ApiEmployeeController::class, 'updateCustomer']);
    Route::put('/customers/{id}/password', [ApiEmployeeController::class, 'updateCustomerPassword']);

    Route::get('/carts/{customer_id}', [ApiEmployeeController::class, 'getCart']);
    Route::post('/carts/{customer_id}/items', [ApiEmployeeController::class, 'addCartItem']);
    Route::put('/cart-items/{item_id}', [ApiEmployeeController::class, 'updateCartItem']);
    Route::delete('/cart-items/{item_id}', [ApiEmployeeController::class, 'deleteCartItem']);
    Route::post('/carts/{customer_id}/checkout', [ApiEmployeeController::class, 'checkoutCart']);

    Route::post('/orders/{order_id}/items', [ApiEmployeeController::class, 'addOrderItem']);
    Route::put('/order-items/{item_id}', [ApiEmployeeController::class, 'updateOrderItem']);
    Route::delete('/order-items/{item_id}', [ApiEmployeeController::class, 'deleteOrderItem']);
});
