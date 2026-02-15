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
Route::get('/inventori', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/pemasok', [SupplierController::class, 'index'])->name('suppliers.index');
Route::get('/piutang', [ReceivableController::class, 'index'])->name('receivables.index');
Route::get('/kas', [CashLedgerController::class, 'index'])->name('cash.index');
Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
