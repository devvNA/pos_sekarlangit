<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Penjualan Hari Ini
        $dailySales        = Sale::whereDate('sold_at', today())->sum('total');
        $dailyTransactions = Sale::whereDate('sold_at', today())->count();

        // Kas di Tangan (total pemasukan - pengeluaran)
        // Jika tabel cash_ledger_entries belum ada data, default ke 0
        $cashBalance = DB::table('cash_ledger_entries')
            ->selectRaw("SUM(CASE WHEN type = 'in' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) as balance")
            ->value('balance') ?? 0;

        // Stok Menipis (stock <= min_stock)
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('active', true)
            ->count();

        // Total Transaksi (all time)
        $totalTransactions = Sale::count();

        // Produk Aktif
        $activeProducts = Product::where('active', true)->count();

        // Piutang Berjalan (belum lunas)
        $totalReceivables = Receivable::where('status', 'belum_lunas')
            ->sum('remaining');

        return view('dashboard', compact(
            'dailySales',
            'dailyTransactions',
            'cashBalance',
            'lowStockCount',
            'totalTransactions',
            'activeProducts',
            'totalReceivables'
        ));
    }
}
