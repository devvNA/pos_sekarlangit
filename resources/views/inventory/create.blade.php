@extends('layouts.app')

@section('content')
    <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Tambah Produk</h2>
                <p class="text-sm text-black/60">Isi data produk baru.</p>
            </div>
            <a class="btn-secondary" href="{{ route('inventory.index') }}">Kembali</a>
        </div>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali input berikut:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-4 grid gap-4 md:grid-cols-2" method="post" action="{{ route('inventory.store') }}">
            @csrf
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Nama Produk</label>
                <input name="name" value="{{ old('name') }}"
                    class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text" required />
                @error('name')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Barcode (EAN-13) <span class="font-normal text-black/40">- Opsional</span></label>
                <div class="flex gap-2">
                    <input id="inventory-barcode-input" name="barcode" value="{{ old('barcode') }}"
                        class="flex-1 rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text"
                        placeholder="Kosongkan jika tidak ada" />
                    <button id="open-inventory-scan-modal" class="btn-primary flex items-center gap-2" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Scan
                    </button>
                </div>
                @error('barcode')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Satuan</label>
                <select name="unit" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" required>
                    @php($selectedUnit = old('unit', 'pcs'))
                    <option value="pcs" @selected($selectedUnit === 'pcs')>pcs</option>
                    <option value="pak" @selected($selectedUnit === 'pak')>pak</option>
                    <option value="box" @selected($selectedUnit === 'box')>box</option>
                    <option value="dus" @selected($selectedUnit === 'dus')>dus</option>
                    <option value="lusin" @selected($selectedUnit === 'lusin')>lusin</option>
                    <option value="kodi" @selected($selectedUnit === 'kodi')>kodi</option>
                    <option value="ikat" @selected($selectedUnit === 'ikat')>ikat</option>
                    <option value="lembar" @selected($selectedUnit === 'lembar')>lembar</option>
                    <option value="set" @selected($selectedUnit === 'set')>set</option>
                    <option value="sachet" @selected($selectedUnit === 'sachet')>sachet</option>
                    <option value="botol" @selected($selectedUnit === 'botol')>botol</option>
                    <option value="kaleng" @selected($selectedUnit === 'kaleng')>kaleng</option>
                    <option value="bungkus" @selected($selectedUnit === 'bungkus')>bungkus</option>
                    <option value="galon" @selected($selectedUnit === 'galon')>galon</option>
                    <option value="kg" @selected($selectedUnit === 'kg')>kg</option>
                    <option value="gram" @selected($selectedUnit === 'gram')>gram</option>
                    <option value="liter" @selected($selectedUnit === 'liter')>liter</option>
                    <option value="ml" @selected($selectedUnit === 'ml')>ml</option>
                    <option value="meter" @selected($selectedUnit === 'meter')>meter</option>
                    <option value="roll" @selected($selectedUnit === 'roll')>roll</option>
                </select>
                @error('unit')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Pemasok (Opsional)</label>
                <select name="supplier_id" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm">
                    <option value="">Tanpa pemasok</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Harga Beli</label>
                <input name="price_buy" value="{{ old('price_buy') }}"
                    class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text"
                    inputmode="numeric" data-rupiah autocomplete="off" required />
                @error('price_buy')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Harga Jual</label>
                <input name="price_sell" value="{{ old('price_sell') }}"
                    class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text"
                    inputmode="numeric" data-rupiah autocomplete="off" required />
                @error('price_sell')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Stok Awal</label>
                <input name="stock" value="{{ old('stock', 0) }}"
                    class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="number"
                    min="0" required />
                @error('stock')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Minimum Stok</label>
                <input name="min_stock" value="{{ old('min_stock', 0) }}"
                    class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="number"
                    min="0" required />
                @error('min_stock')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input name="active" value="1" class="h-4 w-4" type="checkbox" @checked(old('active') === null ? true : (bool) old('active')) />
                    <span>Produk aktif</span>
                </label>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button class="btn-primary" type="submit">Simpan Produk</button>
                <a class="btn-secondary" href="{{ route('inventory.index') }}">Batal</a>
            </div>
        </form>
    </section>

    {{-- Modal Scan Barcode --}}
    <div id="inventory-scan-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-black/10 bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold">Scan Barcode</h3>
                <button id="close-inventory-scan-modal" type="button"
                    class="rounded-full p-1 text-gray-500 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-4">
                <p class="text-sm text-black/60">Arahkan barcode ke kamera untuk scan otomatis.</p>

                {{-- Area Kamera --}}
                <div class="mt-4 rounded-lg border border-dashed border-black/30 bg-white/70 p-4">
                    <div id="inventory-scanner-wrapper"
                        class="scanner-wrapper relative mt-2 overflow-hidden rounded-md border border-black/10">
                        <div id="inventory-scanner-preview"
                            class="flex h-48 items-center justify-center bg-black/5 text-xs text-black/50">
                            Preview kamera akan muncul di sini.
                        </div>
                        <div id="inventory-scan-line"
                            class="scan-line pointer-events-none absolute left-0 right-0 top-0 h-0.5 bg-red-500 opacity-0 shadow-[0_0_8px_rgba(239,68,68,0.8)]">
                        </div>
                        <div id="inventory-scan-overlay"
                            class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300">
                            <div class="relative h-full w-full">
                                <div class="absolute left-4 top-4 h-6 w-6 border-l-2 border-t-2 border-red-500"></div>
                                <div class="absolute right-4 top-4 h-6 w-6 border-r-2 border-t-2 border-red-500"></div>
                                <div class="absolute bottom-4 left-4 h-6 w-6 border-b-2 border-l-2 border-red-500"></div>
                                <div class="absolute bottom-4 right-4 h-6 w-6 border-b-2 border-r-2 border-red-500"></div>
                            </div>
                        </div>
                    </div>
                    <p id="inventory-scanner-status" class="mt-2 text-center text-xs text-black/60">Status: siap.</p>
                </div>

                <div class="mt-4 flex gap-2">
                    <button id="inventory-scan-toggle-modal" class="btn-primary w-full" type="button">Mulai Scan
                        Kamera</button>
                </div>

                {{-- Input Manual Barcode --}}
                <div class="mt-4 border-t border-black/10 pt-4">
                    <p class="text-xs font-semibold text-black/60">Atau input manual:</p>
                    <div class="mt-2 flex gap-2">
                        <input id="modal-inventory-barcode-input"
                            class="flex-1 rounded-lg border border-black/20 bg-white px-3 py-2 text-sm"
                            placeholder="Masukkan barcode (opsional)" type="text" />
                        <button id="modal-inventory-barcode-submit" class="btn-primary" type="button">Pakai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modal controls
        const openInventoryScanModal = document.getElementById('open-inventory-scan-modal');
        const closeInventoryScanModal = document.getElementById('close-inventory-scan-modal');
        const inventoryScanModal = document.getElementById('inventory-scan-modal');
        const modalInventoryBarcodeInput = document.getElementById('modal-inventory-barcode-input');
        const modalInventoryBarcodeSubmit = document.getElementById('modal-inventory-barcode-submit');
        const inventoryBarcodeInput = document.getElementById('inventory-barcode-input');

        // Buka modal
        if (openInventoryScanModal) {
            openInventoryScanModal.addEventListener('click', () => {
                inventoryScanModal.classList.remove('hidden');
                inventoryScanModal.classList.add('flex');
                if (modalInventoryBarcodeInput) modalInventoryBarcodeInput.focus();
            });
        }

        // Tutup modal
        if (closeInventoryScanModal) {
            closeInventoryScanModal.addEventListener('click', () => {
                // Hentikan scanner jika sedang berjalan
                if (window.inventoryScanner && window.inventoryScanner.isScanning) {
                    const scanner = window.inventoryScanner.instance;
                    if (scanner) {
                        scanner.stop().then(() => {
                            window.inventoryScanner.isScanning = false;
                            const scanToggleBtn = document.getElementById('inventory-scan-toggle-modal');
                            if (scanToggleBtn) scanToggleBtn.textContent = 'Mulai Scan Kamera';
                        }).catch(() => {});
                    }
                }
                inventoryScanModal.classList.add('hidden');
                inventoryScanModal.classList.remove('flex');
            });
        }

        // Tutup modal saat klik di luar
        if (inventoryScanModal) {
            inventoryScanModal.addEventListener('click', (e) => {
                if (e.target === inventoryScanModal && closeInventoryScanModal) {
                    closeInventoryScanModal.click();
                }
            });
        }

        // Submit barcode dari modal ke form
        if (modalInventoryBarcodeSubmit && modalInventoryBarcodeInput && inventoryBarcodeInput) {
            modalInventoryBarcodeSubmit.addEventListener('click', () => {
                const barcode = modalInventoryBarcodeInput.value.trim();
                if (barcode) {
                    inventoryBarcodeInput.value = barcode;
                    inventoryScanModal.classList.add('hidden');
                    inventoryScanModal.classList.remove('flex');
                    modalInventoryBarcodeInput.value = '';
                }
            });

            modalInventoryBarcodeInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    modalInventoryBarcodeSubmit.click();
                }
            });
        }
    </script>
@endpush
