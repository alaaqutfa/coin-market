<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/products', [ProductController::class, 'list'])->name('products.list');
Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');
Route::get('/show-catalog', [ProductController::class, 'showCatalog'])->name('showCatalog');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
    Route::put('products/barcode/{barcode}', [ProductController::class, 'updateByBarcode']);
});
