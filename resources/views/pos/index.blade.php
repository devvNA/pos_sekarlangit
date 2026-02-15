@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @error('checkout')
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-bold">Kasir Cepat</h2>
                    <p class="text-sm text-black/60">Scan barcode atau input manual.</p>
                </div>
                <div class="flex gap-2">
                    <button id="scan-toggle" class="btn-primary" type="button">Mulai Scan Kamera</button>
                    <button id="manual-input" class="btn-secondary" type="button">Input Manual</button>
                </div>
            </div>

            <div class="mt-4 rounded-xl border border-black/10 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Pemindaian Barcode</p>
                <form id="pos-add-form" class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]" method="post" action="{{ route('pos.items.add') }}">
                    @csrf
                    <input id="barcode-input" name="barcode" value="{{ old('barcode') }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" placeholder="Masukkan barcode EAN-13" type="text" />
                    <button class="btn-primary" type="submit">Tambah</button>
                </form>
                @error('barcode')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
                <div class="mt-4 rounded-lg border border-dashed border-black/30 bg-white/70 p-4">
                    <p class="text-xs font-semibold text-black/60">Area Kamera</p>
                    <div id="scanner-preview" class="mt-2 flex h-40 items-center justify-center rounded-md border border-black/10 bg-black/5 text-xs text-black/50">
                        Preview kamera akan muncul di sini.
                    </div>
                    <p id="scanner-status" class="mt-2 text-xs text-black/60">Status: siap.</p>
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
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart ?? [] as $item)
                                <tr class="border-t border-black/10">
                                    <td class="px-3 py-3">
                                        <p class="font-semibold">{{ $item['name'] }}</p>
                                    </td>
                                    <td class="px-3 py-3">{{ $item['qty'] }}</td>
                                    <td class="px-3 py-3">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-3">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <form method="post" action="{{ route('pos.items.remove', $item['id']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-semibold text-red-600" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-black/10">
                                    <td class="px-3 py-3" colspan="5">Belum ada barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-black/60">Item: {{ count($cart ?? []) }}</span>
                    <form method="post" action="{{ route('pos.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs font-semibold text-red-600" type="submit">Bersihkan Keranjang</button>
                    </form>
                </div>
            </div>
        </section>

        <aside class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-bold">Ringkasan Pembayaran</h3>
            <form id="checkout-form" class="mt-4 space-y-3 text-sm" method="post" action="{{ route('pos.checkout') }}">
                @csrf
                <div class="flex items-center justify-between">
                    <span>Total</span>
                    <span id="pos-total" class="text-lg font-bold" data-total="{{ $total ?? 0 }}">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</span>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="debit">Debit</option>
                        <option value="kredit">Kredit</option>
                        <option value="kasbon">Kasbon</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Nama Pelanggan (untuk piutang)</label>
                    <input name="customer_name" value="{{ old('customer_name') }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2" type="text" placeholder="Nama pelanggan" />
                    @error('customer_name')
                        <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Bayar</label>
                    <input id="paid-input" name="paid" value="{{ old('paid') }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2" type="number" min="0" placeholder="0" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Kembalian</label>
                    <input id="change-input" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2" type="text" placeholder="Rp 0" disabled />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-black/60">Catatan</label>
                    <textarea name="note" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2" rows="2" placeholder="Catatan transaksi (opsional)">{{ old('note') }}</textarea>
                </div>
                <button class="btn-primary w-full" type="submit">Simpan & Cetak Struk</button>
            </form>
        </aside>
    </div>
@endsection

