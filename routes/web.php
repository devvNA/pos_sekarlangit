<?php

use App\Http\Controllers\CashLedgerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos/items', [PosController::class, 'addItem'])->name('pos.items.add');
Route::delete('/pos/items/{productId}', [PosController::class, 'removeItem'])->name('pos.items.remove');
Route::delete('/pos/clear', [PosController::class, 'clearCart'])->name('pos.clear');
Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
Route::get('/inventori', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventori/tambah', [InventoryController::class, 'create'])->name('inventory.create');
Route::post('/inventori', [InventoryController::class, 'store'])->name('inventory.store');
Route::get('/inventori/{product}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::put('/inventori/{product}', [InventoryController::class, 'update'])->name('inventory.update');
Route::delete('/inventori/{product}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
Route::get('/pemasok', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/piutang', [ReceivableController::class, 'index'])->name('receivables.index');
Route::get('/kas', [CashLedgerController::class, 'index'])->name('cash.index');
Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
