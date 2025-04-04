<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(InventoryController::class)->group(function () {
    Route::get('/inventory', 'getAll')->name('inventory.index');
    Route::post('/inventory', 'save')->name('inventory.store');
});

Route::controller(SaleController::class)->group(function () {
    Route::post('/sales', 'createSale')->name('sale.create');
});
