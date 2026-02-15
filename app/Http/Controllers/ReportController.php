<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockInItem;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $rangeStart = $today->copy();
        $rangeEnd = $today->copy()->endOfDay();

        $startInput = $request->input('start_date');
        $endInput = $request->input('end_date');
        if ($startInput && $endInput) {
            $rangeStart = Carbon::createFromFormat('Y-m-d', $startInput)->startOfDay();
            $rangeEnd = Carbon::createFromFormat('Y-m-d', $endInput)->endOfDay();
        }

        $dailySales = Sale::whereDate('sold_at', $today)->sum('total');
        $dailyTransactions = Sale::whereDate('sold_at', $today)->count();
        $dailyCogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.sold_at', $today)
            ->sum(DB::raw('sale_items.cost * sale_items.qty'));

        $monthlySales = Sale::whereBetween('sold_at', [$monthStart, $monthEnd])->sum('total');
        $monthlyTransactions = Sale::whereBetween('sold_at', [$monthStart, $monthEnd])->count();
        $monthlyCogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sold_at', [$monthStart, $monthEnd])
            ->sum(DB::raw('sale_items.cost * sale_items.qty'));

        $rangeSales = Sale::whereBetween('sold_at', [$rangeStart, $rangeEnd])->sum('total');
        $rangeTransactions = Sale::whereBetween('sold_at', [$rangeStart, $rangeEnd])->count();
        $rangeCogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sold_at', [$rangeStart, $rangeEnd])
            ->sum(DB::raw('sale_items.cost * sale_items.qty'));

        $rangeStockIn = StockInItem::join('stock_ins', 'stock_in_items.stock_in_id', '=', 'stock_ins.id')
            ->whereBetween('stock_ins.received_at', [$rangeStart, $rangeEnd])
            ->sum('stock_in_items.qty');
        $rangeStockOut = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sold_at', [$rangeStart, $rangeEnd])
            ->sum('sale_items.qty');

        $rangeItems = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sold_at', [$rangeStart, $rangeEnd])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc(DB::raw('SUM(sale_items.qty)'))
            ->get([
                'products.name as product_name',
                DB::raw('SUM(sale_items.qty) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_sales'),
                DB::raw('SUM(sale_items.cost * sale_items.qty) as total_cogs'),
            ]);

        $rangeSalesList = Sale::whereBetween('sold_at', [$rangeStart, $rangeEnd])
            ->orderByDesc('sold_at')
            ->limit(50)
            ->get(['id', 'receipt_no', 'sold_at', 'payment_method', 'total', 'paid', 'change']);

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        $dailyStart = Carbon::now()->subDays(13)->startOfDay();
        $dailyEnd = Carbon::now()->endOfDay();
        $dailyRaw = Sale::query()
            ->selectRaw('DATE(sold_at) as date, SUM(total) as total')
            ->whereBetween('sold_at', [$dailyStart, $dailyEnd])
            ->groupBy('date')
            ->pluck('total', 'date');

        $dailyLabels = [];
        $dailyValues = [];
        foreach (CarbonPeriod::create($dailyStart, $dailyEnd) as $day) {
            $key = $day->format('Y-m-d');
            $dailyLabels[] = $day->format('d/m');
            $dailyValues[] = (float) ($dailyRaw[$key] ?? 0);
        }

        $monthStartTrend = Carbon::now()->startOfMonth()->subMonths(5);
        $monthEndTrend = Carbon::now()->endOfMonth();
        $monthlyRaw = Sale::query()
            ->selectRaw('DATE_FORMAT(sold_at, "%Y-%m") as month, SUM(total) as total')
            ->whereBetween('sold_at', [$monthStartTrend, $monthEndTrend])
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyLabels = [];
        $monthlyValues = [];
        $cursor = $monthStartTrend->copy();
        while ($cursor <= $monthEndTrend) {
            $key = $cursor->format('Y-m');
            $monthlyLabels[] = $cursor->format('M Y');
            $monthlyValues[] = (float) ($monthlyRaw[$key] ?? 0);
            $cursor->addMonthNoOverflow();
        }

        return view('reports.index', [
            'dailySales' => $dailySales,
            'dailyTransactions' => $dailyTransactions,
            'dailyProfit' => $dailySales - $dailyCogs,
            'monthlySales' => $monthlySales,
            'monthlyTransactions' => $monthlyTransactions,
            'monthlyProfit' => $monthlySales - $monthlyCogs,
            'rangeSales' => $rangeSales,
            'rangeTransactions' => $rangeTransactions,
            'rangeProfit' => $rangeSales - $rangeCogs,
            'rangeStockIn' => $rangeStockIn,
            'rangeStockOut' => $rangeStockOut,
            'rangeItems' => $rangeItems,
            'rangeSalesList' => $rangeSalesList,
            'lowStockProducts' => $lowStockProducts,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'dailyLabels' => $dailyLabels,
            'dailyValues' => $dailyValues,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $rangeStart = Carbon::today()->startOfDay();
        $rangeEnd = Carbon::today()->endOfDay();

        $startInput = $request->input('start_date');
        $endInput = $request->input('end_date');
        if ($startInput && $endInput) {
            $rangeStart = Carbon::createFromFormat('Y-m-d', $startInput)->startOfDay();
            $rangeEnd = Carbon::createFromFormat('Y-m-d', $endInput)->endOfDay();
        }

        $rows = SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sold_at', [$rangeStart, $rangeEnd])
            ->orderBy('sales.sold_at')
            ->get([
                'sales.receipt_no',
                'sales.sold_at',
                'sales.payment_method',
                'products.name as product_name',
                'sale_items.qty',
                'sale_items.price',
                'sale_items.cost',
                'sale_items.subtotal',
            ]);

        $filename = 'laporan_penjualan_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'No Struk',
                'Tanggal',
                'Metode Pembayaran',
                'Produk',
                'Qty',
                'Harga Jual',
                'HPP',
                'Subtotal',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->receipt_no,
                    Carbon::parse($row->sold_at)->format('d/m/Y H:i'),
                    $row->payment_method,
                    $row->product_name,
                    $row->qty,
                    $row->price,
                    $row->cost,
                    $row->subtotal,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
