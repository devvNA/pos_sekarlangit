<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashBookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
})->name('welcome');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate')->middleware('guest');

// Protected Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
    Route::post('/pemasok', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/pemasok/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/pemasok/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/piutang', [ReceivableController::class, 'index'])->name('receivables.index');
    Route::post('/piutang', [ReceivableController::class, 'store'])->name('receivables.store');
    Route::put('/piutang/{receivable}', [ReceivableController::class, 'update'])->name('receivables.update');
    Route::delete('/piutang/{receivable}', [ReceivableController::class, 'destroy'])->name('receivables.destroy');
    Route::post('/piutang/{receivable}/bayar', [ReceivableController::class, 'addPayment'])->name('receivables.addPayment');

    Route::get('/kas', [CashBookController::class, 'index'])->name('cash.index');
    Route::post('/kas', [CashBookController::class, 'store'])->name('cash.store');
    Route::put('/kas/{cashLedgerEntry}', [CashBookController::class, 'update'])->name('cash.update');
    Route::delete('/kas/{cashLedgerEntry}', [CashBookController::class, 'destroy'])->name('cash.destroy');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
