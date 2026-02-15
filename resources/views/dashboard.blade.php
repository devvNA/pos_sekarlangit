@extends('layouts.app')

@section('content')
    <section class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <p class="text-base font-semibold text-black/60">Penjualan Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-green-600">Rp 0</p>
            <p class="mt-1 text-sm text-black/60">Belum ada transaksi.</p>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <p class="text-base font-semibold text-black/60">Kas di Tangan</p>
            <p class="mt-2 text-3xl font-bold text-green-600">Rp 0</p>
            <p class="mt-1 text-sm text-black/60">Total kas masuk minus keluar.</p>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <p class="text-base font-semibold text-black/60">Stok Menipis</p>
            <p class="mt-2 text-3xl font-bold text-red-600">0 Produk</p>
            <p class="mt-1 text-sm text-black/60">Belum ada alert.</p>
        </div>
    </section>

    <section class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold">Ringkasan Cepat</h2>
            <div class="mt-4 space-y-3 text-base">
                <div class="flex items-center justify-between border-b border-black/10 pb-3">
                    <span class="text-black/70">Total Transaksi</span>
                    <span class="text-xl font-bold">0</span>
                </div>
                <div class="flex items-center justify-between border-b border-black/10 pb-3">
                    <span class="text-black/70">Produk Aktif</span>
                    <span class="text-xl font-bold">0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-black/70">Piutang Berjalan</span>
                    <span class="text-xl font-bold">Rp 0</span>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold">Checklist Persiapan</h2>
            <ul class="mt-4 space-y-2 text-base">
                <li class="flex items-center gap-2 rounded-lg border border-dashed border-black/20 px-3 py-2 text-black/70">
                    <span class="text-green-600">✓</span>
                    <span>Input produk awal & stok masuk.</span>
                </li>
                <li class="flex items-center gap-2 rounded-lg border border-dashed border-black/20 px-3 py-2 text-black/70">
                    <span class="text-green-600">✓</span>
                    <span>Cek koneksi printer thermal.</span>
                </li>
                <li class="flex items-center gap-2 rounded-lg border border-dashed border-black/20 px-3 py-2 text-black/70">
                    <span class="text-green-600">✓</span>
                    <span>Uji pemindaian barcode kamera.</span>
                </li>
            </ul>
        </div>
    </section>
@endsection
