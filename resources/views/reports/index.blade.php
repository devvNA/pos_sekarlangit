@extends('layouts.app')

@section('content')
    <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Laporan</h2>
                <p class="text-sm text-black/60">Laporan harian, bulanan, stok, dan laba rugi.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a class="btn-secondary" href="{{ route('reports.export', ['start_date' => $rangeStart->format('Y-m-d'), 'end_date' => $rangeEnd->format('Y-m-d')]) }}">Export CSV</a>
                <form class="flex flex-wrap gap-2" method="get" action="{{ route('reports.index') }}">
                    <input class="rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" name="start_date" type="date" value="{{ $rangeStart->format('Y-m-d') }}" />
                    <input class="rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" name="end_date" type="date" value="{{ $rangeEnd->format('Y-m-d') }}" />
                    <button class="btn-primary" type="submit">Terapkan</button>
                </form>
            </div>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-black/10 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Penjualan Hari Ini</p>
                <p class="mt-2 text-2xl font-bold">Rp {{ number_format($dailySales, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-black/60">Transaksi: {{ $dailyTransactions }}</p>
                <p class="mt-1 text-xs text-black/60">Laba kotor: Rp {{ number_format($dailyProfit, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-black/10 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Penjualan Bulan Ini</p>
                <p class="mt-2 text-2xl font-bold">Rp {{ number_format($monthlySales, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-black/60">Transaksi: {{ $monthlyTransactions }}</p>
                <p class="mt-1 text-xs text-black/60">Laba kotor: Rp {{ number_format($monthlyProfit, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-black/10 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Ringkasan Rentang</p>
                <p class="mt-2 text-2xl font-bold">Rp {{ number_format($rangeSales, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-black/60">Transaksi: {{ $rangeTransactions }}</p>
                <p class="mt-1 text-xs text-black/60">Laba kotor: Rp {{ number_format($rangeProfit, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-bold">Tren 14 Hari Terakhir</h3>
                <div class="mt-4 h-60">
                    <canvas id="daily-trend"></canvas>
                </div>
            </div>
            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-bold">Tren 6 Bulan Terakhir</h3>
                <div class="mt-4 h-60">
                    <canvas id="monthly-trend"></canvas>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-bold">Pergerakan Stok (Rentang)</h3>
                <div class="mt-4 grid gap-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span>Stok Masuk</span>
                        <span class="font-semibold">{{ number_format($rangeStockIn, 0, ',', '.') }} item</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Stok Keluar</span>
                        <span class="font-semibold">{{ number_format($rangeStockOut, 0, ',', '.') }} item</span>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
                <h3 class="text-lg font-bold">Produk Stok Menipis</h3>
                <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-black/5 text-xs uppercase tracking-wide text-black/60">
                            <tr>
                                <th class="px-3 py-2">Produk</th>
                                <th class="px-3 py-2">Stok</th>
                                <th class="px-3 py-2">Minimum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr class="border-t border-black/10">
                                    <td class="px-3 py-2">{{ $product->name }}</td>
                                    <td class="px-3 py-2">{{ $product->stock }}</td>
                                    <td class="px-3 py-2">{{ $product->min_stock }}</td>
                                </tr>
                            @empty
                                <tr class="border-t border-black/10">
                                    <td class="px-3 py-3" colspan="3">Belum ada produk menipis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold">Detail Penjualan per Item (Rentang)</h3>
            <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/5 text-xs uppercase tracking-wide text-black/60">
                        <tr>
                            <th class="px-3 py-2">Produk</th>
                            <th class="px-3 py-2">Qty Terjual</th>
                            <th class="px-3 py-2">Omzet</th>
                            <th class="px-3 py-2">HPP</th>
                            <th class="px-3 py-2">Laba Kotor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rangeItems as $item)
                            <tr class="border-t border-black/10">
                                <td class="px-3 py-2">{{ $item->product_name }}</td>
                                <td class="px-3 py-2">{{ number_format($item->total_qty, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($item->total_cogs, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($item->total_sales - $item->total_cogs, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr class="border-t border-black/10">
                                <td class="px-3 py-3" colspan="5">Belum ada penjualan pada rentang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold">Detail Transaksi (50 Terbaru)</h3>
            <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black/5 text-xs uppercase tracking-wide text-black/60">
                        <tr>
                            <th class="px-3 py-2">No Struk</th>
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2">Metode</th>
                            <th class="px-3 py-2">Total</th>
                            <th class="px-3 py-2">Bayar</th>
                            <th class="px-3 py-2">Kembali</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rangeSalesList as $sale)
                            <tr class="border-t border-black/10">
                                <td class="px-3 py-2">{{ $sale->receipt_no }}</td>
                                <td class="px-3 py-2">{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2">{{ strtoupper($sale->payment_method) }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($sale->paid, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">Rp {{ number_format($sale->change, 0, ',', '.') }}</td>
                                <td class="px-3 py-2">
                                    <a class="text-xs font-semibold text-emerald-700" href="{{ route('pos.receipt', $sale->id) }}">Lihat Struk</a>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-black/10">
                                <td class="px-3 py-3" colspan="7">Belum ada transaksi pada rentang ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-xs text-black/60">Menampilkan maksimum 50 transaksi terbaru.</p>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const dailyLabels = @json($dailyLabels);
        const dailyValues = @json($dailyValues);
        const monthlyLabels = @json($monthlyLabels);
        const monthlyValues = @json($monthlyValues);

        const dailyCtx = document.getElementById('daily-trend');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Penjualan',
                        data: dailyValues,
                        borderColor: '#0a7c5a',
                        backgroundColor: 'rgba(10, 124, 90, 0.1)',
                        tension: 0.3,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (value) => `Rp ${Number(value).toLocaleString('id-ID')}`,
                            },
                        },
                    },
                },
            });
        }

        const monthlyCtx = document.getElementById('monthly-trend');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Penjualan',
                        data: monthlyValues,
                        backgroundColor: '#e69a2d',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (value) => `Rp ${Number(value).toLocaleString('id-ID')}`,
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush
