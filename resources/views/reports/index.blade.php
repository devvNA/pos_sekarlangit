@extends('layouts.app')

@section('content')
    <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Laporan</h2>
                <p class="text-sm text-black/60">Laporan harian, bulanan, stok, dan laba rugi.</p>
            </div>
            <button class="btn-primary" type="button">Unduh Laporan</button>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-dashed border-black/20 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Penjualan Harian</p>
                <p class="mt-1 text-xs text-black/60">Ringkasan transaksi per hari.</p>
            </div>
            <div class="rounded-xl border border-dashed border-black/20 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Pergerakan Stok</p>
                <p class="mt-1 text-xs text-black/60">Masuk/keluar barang.</p>
            </div>
            <div class="rounded-xl border border-dashed border-black/20 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Laba Rugi</p>
                <p class="mt-1 text-xs text-black/60">HPP vs penjualan.</p>
            </div>
        </div>
    </section>
@endsection
