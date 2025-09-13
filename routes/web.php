<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/filter-products', [ProductController::class, 'filter'])->name('products.filter');

Route::prefix('api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::get('products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
});
