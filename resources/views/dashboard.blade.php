@extends('layouts.app')

@section('content')
    <section class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-black/60">Penjualan Hari Ini</p>
            <p class="mt-2 text-3xl font-bold">Rp 0</p>
            <p class="mt-1 text-xs text-black/50">Belum ada transaksi.</p>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-black/60">Kas di Tangan</p>
            <p class="mt-2 text-3xl font-bold">Rp 0</p>
            <p class="mt-1 text-xs text-black/50">Total kas masuk minus keluar.</p>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-black/60">Stok Menipis</p>
            <p class="mt-2 text-3xl font-bold">0 Produk</p>
            <p class="mt-1 text-xs text-black/50">Belum ada alert.</p>
        </div>
    </section>

    <section class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold">Ringkasan Cepat</h2>
            <div class="mt-4 grid gap-3 text-sm">
                <div class="flex items-center justify-between">
                    <span>Total Transaksi</span>
                    <span class="font-semibold">0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Produk Aktif</span>
                    <span class="font-semibold">0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Piutang Berjalan</span>
                    <span class="font-semibold">Rp 0</span>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold">Checklist Persiapan</h2>
            <ul class="mt-4 space-y-2 text-sm">
                <li class="rounded-lg border border-dashed border-black/20 px-3 py-2">Input produk awal & stok masuk.</li>
                <li class="rounded-lg border border-dashed border-black/20 px-3 py-2">Cek koneksi printer thermal.</li>
                <li class="rounded-lg border border-dashed border-black/20 px-3 py-2">Uji pemindaian barcode kamera.</li>
            </ul>
        </div>
    </section>
@endsection
