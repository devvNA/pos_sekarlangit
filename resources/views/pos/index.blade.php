@extends('layouts.app')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold">Kasir Cepat</h2>
                    <p class="text-sm text-black/60">Scan barcode atau input manual.</p>
                </div>
                <div class="flex gap-2">
                    <button class="btn-primary" type="button">Mulai Scan Kamera</button>
                    <button class="btn-secondary" type="button">Input Manual</button>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-black/10 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Pemindaian Barcode</p>
                <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
                    <input id="barcode-input" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" placeholder="Masukkan barcode EAN-13" type="text" />
                    <button class="btn-primary" type="button">Tambah</button>
                </div>
                <div class="mt-4 rounded-lg border border-dashed border-black/30 bg-white/70 p-4">
                    <p class="text-xs font-semibold text-black/60">Area Kamera</p>
                    <div id="scanner-preview" class="mt-2 flex h-40 items-center justify-center rounded-md border border-black/10 bg-black/5 text-xs text-black/50">
                        Preview kamera akan muncul di sini.
                    </div>
                </div>
                <p class="mt-2 text-xs text-black/60">Gunakan kamera/webcam untuk scan, atau ketik barcode secara manual.</p>
            </div>

            <div class="mt-5">
                <h3 class="text-sm font-semibold text-black/70">Daftar Barang</h3>
                <div class="mt-3 overflow-hidden rounded-xl border border-black/10">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-black/5 text-xs uppercase tracking-wide text-black/60">
                            <tr>
                                <th class="px-3 py-2">Produk</th>
                                <th class="px-3 py-2">Qty</th>
                                <th class="px-3 py-2">Harga</th>
                                <th class="px-3 py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-black/10">
                                <td class="px-3 py-3" colspan="4">Belum ada barang.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <aside class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold">Ringkasan Pembayaran</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span>Total</span>
                    <span class="text-lg font-bold">Rp 0</span>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Metode Pembayaran</label>
                    <select class="w-full rounded-lg border border-black/20 bg-white px-3 py-2">
                        <option>Cash</option>
                        <option>Debit</option>
                        <option>Kredit</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Bayar</label>
                    <input class="w-full rounded-lg border border-black/20 bg-white px-3 py-2" type="number" placeholder="0" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Kembalian</label>
                    <input class="w-full rounded-lg border border-black/20 bg-white px-3 py-2" type="text" placeholder="Rp 0" disabled />
                </div>
                <button class="btn-primary w-full" type="button">Simpan & Cetak Struk</button>
            </div>
        </aside>
    </div>
@endsection
