@extends('layouts.app')

@section('content')
    <!-- Stats Cards -->
    <section class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-xl border border-black/10 bg-[#fdf8ef] p-3">
            <p class="text-xs font-semibold text-black/70">Penjualan Hari Ini</p>
            <p class="mt-2 text-xl font-bold text-emerald-700">Rp {{ number_format($dailySales, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-black/60">
                @if ($dailyTransactions > 0)
                    {{ $dailyTransactions }} transaksi hari ini
                @else
                    Belum ada transaksi
                @endif
            </p>
        </div>
        <div class="rounded-xl border border-black/10 bg-[#fdf8ef] p-3">
            <p class="text-xs font-semibold text-black/70">Kas di Tangan</p>
            <p class="mt-2 text-xl font-bold {{ $cashBalance >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                Rp {{ number_format($cashBalance, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-black/60">Total kas masuk minus keluar</p>
        </div>
        <div class="rounded-xl border border-black/10 bg-[#fdf8ef] p-3">
            <p class="text-xs font-semibold text-black/70">Stok Menipis</p>
            <p class="mt-2 text-xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-emerald-700' }}">
                {{ $lowStockCount }} Produk
            </p>
            <p class="mt-1 text-xs text-black/60">
                @if ($lowStockCount > 0)
                    Perlu restock segera
                @else
                    Stok aman
                @endif
            </p>
        </div>
    </section>

    <!-- Content Sections -->
    <section class="mt-4 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-black/10 bg-white p-4 shadow-sm">
            <h2 class="text-base font-bold">Ringkasan Cepat</h2>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between rounded-lg border border-black/10 bg-black/5 px-3 py-2">
                    <span class="text-black/70">Total Transaksi</span>
                    <span class="text-base font-bold">{{ number_format($totalTransactions, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg border border-black/10 bg-black/5 px-3 py-2">
                    <span class="text-black/70">Produk Aktif</span>
                    <span class="text-base font-bold">{{ number_format($activeProducts, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between rounded-lg border border-black/10 bg-black/5 px-3 py-2">
                    <span class="text-black/70">Piutang Berjalan</span>
                    <span class="text-base font-bold {{ $totalReceivables > 0 ? 'text-amber-600' : '' }}">
                        Rp {{ number_format($totalReceivables, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-4 shadow-sm">
            <h2 class="text-base font-bold">Checklist Persiapan</h2>
            <ul class="mt-4 space-y-2 text-sm">
                <li
                    class="flex items-center gap-2 rounded-lg border border-black/10 {{ $activeProducts > 0 ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-3 py-2">
                    <span class="{{ $activeProducts > 0 ? 'text-emerald-600' : 'text-amber-600' }} text-lg">
                        {{ $activeProducts > 0 ? '✓' : '○' }}
                    </span>
                    <span>Input produk awal & stok masuk
                        @if ($activeProducts > 0)
                            <span class="text-xs">({{ $activeProducts }} produk)</span>
                        @endif
                    </span>
                </li>
                <li
                    class="flex items-center gap-2 rounded-lg border border-black/10 bg-emerald-50 px-3 py-2 text-emerald-800">
                    <span class="text-emerald-600 text-lg">✓</span>
                    <span>Cek koneksi printer thermal</span>
                </li>
                <li
                    class="flex items-center gap-2 rounded-lg border border-black/10 bg-emerald-50 px-3 py-2 text-emerald-800">
                    <span class="text-emerald-600 text-lg">✓</span>
                    <span>Uji pemindaian barcode kamera</span>
                </li>
            </ul>
        </div>
    </section>
@endsection
