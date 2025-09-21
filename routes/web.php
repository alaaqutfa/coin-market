<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;

Route::get('/test-browsershot', function () {
    Browsershot::html('<h1 style="color:red">Hello World</h1>')
        ->setNodeBinary('C:\Program Files\nodejs\node.exe')
        ->setNpmBinary('C:\Program Files\nodejs\npm.cmd')
        ->setChromePath('C:\Users\AMR Service Center\.cache\puppeteer\chrome\win64-140.0.7339.82\chrome-win64\chrome.exe')
        ->windowSize(800, 600)
        ->save(public_path('test.png'));

    return response()->download(public_path('test.png'));
});

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/products', [ProductController::class, 'list'])->name('products.list');
Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');
Route::post('/exportCatalog', [ProductController::class, 'exportCatalog'])->name('exportCatalog');
Route::get('/showCatalog', [ProductController::class, 'showCatalog'])->name('showCatalog');

Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
    Route::put('products/barcode/{barcode}', [ProductController::class, 'updateByBarcode']);

    // Authorization: Bearer {token}
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance', [AttendanceController::class, 'index']);
    });
});

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');

Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
