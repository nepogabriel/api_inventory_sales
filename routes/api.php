<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(InventoryController::class)->group(function () {
    Route::get('/inventory', 'index')->name('inventory.index');
    Route::post('/inventory', 'store')->name('inventory.store');
});
