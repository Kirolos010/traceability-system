<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TraceabilityController;
use App\Http\Controllers\TransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Suppliers
Route::apiResource('suppliers', SupplierController::class);

// Products
Route::apiResource('products', ProductController::class);

// Locations
Route::apiResource('locations', LocationController::class);

// Batches
Route::apiResource('batches', BatchController::class);

// Inventory Movements
Route::apiResource('inventory-movements', InventoryMovementController::class)->except(['update', 'destroy']);

// Productions
Route::apiResource('productions', ProductionController::class)->except(['destroy']);

// Transfers
Route::apiResource('transfers', TransferController::class)->except(['update', 'destroy']);
Route::post('transfers/{id}/complete', [TransferController::class, 'complete']);

// Sales
Route::apiResource('sales', SaleController::class)->except(['update', 'destroy']);

// Traceability
Route::prefix('trace')->group(function () {
    Route::get('backward/{batchId}', [TraceabilityController::class, 'traceBackward']);
    Route::get('forward/{batchId}', [TraceabilityController::class, 'traceForward']);
    Route::get('full/{batchId}', [TraceabilityController::class, 'fullTrace']);
    Route::get('sale/{saleId}', [TraceabilityController::class, 'traceFromSale']);
});
