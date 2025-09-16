<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/products', [ProductController::class, 'list'])->name('products.list');
Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');
Route::post('/products/preview-images', [ProductController::class, 'previewImages'])->name('products.preview.images');
Route::post('/products/save-images', [ProductController::class, 'saveImages'])->name('products.save.images');



Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
});
