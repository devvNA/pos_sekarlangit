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
                    <p class="text-sm text-black/60">Tambahkan barang ke keranjang.</p>
                </div>
                <div class="flex gap-2">
                    <button id="open-scan-modal" class="btn-primary flex items-center gap-2" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Scan Barcode
                    </button>
                </div>
            </div>

            {{-- Predictive Search Barang --}}
            <div class="mt-4 rounded-xl border border-black/10 bg-[#fdf8ef] p-4">
                <p class="text-sm font-semibold">Cari Produk</p>
                <form id="pos-add-form" class="mt-3 relative" method="post" action="{{ route('pos.items.add') }}">
                    @csrf
                    <input type="hidden" name="barcode" id="selected-barcode" />
                    <div class="relative">
                        <input id="product-search" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2.5 pl-10 text-sm" placeholder="Ketik nama produk..." type="text" autocomplete="off" />
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    {{-- Dropdown hasil pencarian --}}
                    <div id="search-results" class="absolute z-20 mt-1 hidden w-full max-w-lg rounded-lg border border-black/10 bg-white shadow-lg">
                        <ul id="search-results-list" class="max-h-60 overflow-y-auto py-1"></ul>
                    </div>
                    @error('barcode')
                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </form>
                <p class="mt-2 text-xs text-black/60">Ketik nama produk untuk mencari, atau klik tombol "Scan Barcode" untuk scan.</p>
            </div>

            <div class="mt-5">
                <h3 class="text-sm font-semibold text-black/70">Daftar Barang</h3>
                <div class="mt-3 overflow-hidden rounded-xl border border-black/10">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#1e3a5f] text-sm font-semibold text-white/70 p-2 rounded-lg text-xs tracking-wide text-black/60">
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
                                    <td class="px-3 py-3 text-center text-gray-500" colspan="5">Belum ada barang.</td>
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

    {{-- Modal Scan Barcode --}}
    <div id="scan-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl border border-black/10 bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold">Scan Barcode</h3>
                <button id="close-scan-modal" type="button" class="rounded-full p-1 text-gray-500 hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mt-4">
                <p class="text-sm text-black/60">Arahkan barcode ke kamera untuk scan otomatis.</p>
                
                {{-- Area Kamera --}}
                <div class="mt-4 rounded-lg border border-dashed border-black/30 bg-white/70 p-4">
                    <div id="scanner-wrapper" class="scanner-wrapper relative mt-2 overflow-hidden rounded-md border border-black/10">
                        <div id="scanner-preview" class="flex h-48 items-center justify-center bg-black/5 text-xs text-black/50">
                            Preview kamera akan muncul di sini.
                        </div>
                        <div id="scan-line" class="scan-line pointer-events-none absolute left-0 right-0 top-0 h-0.5 bg-red-500 opacity-0 shadow-[0_0_8px_rgba(239,68,68,0.8)]"></div>
                        <div id="scan-overlay" class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300">
                            <div class="relative h-full w-full">
                                <div class="absolute left-4 top-4 h-6 w-6 border-l-2 border-t-2 border-red-500"></div>
                                <div class="absolute right-4 top-4 h-6 w-6 border-r-2 border-t-2 border-red-500"></div>
                                <div class="absolute bottom-4 left-4 h-6 w-6 border-b-2 border-l-2 border-red-500"></div>
                                <div class="absolute bottom-4 right-4 h-6 w-6 border-b-2 border-r-2 border-red-500"></div>
                            </div>
                        </div>
                    </div>
                    <p id="scanner-status" class="mt-2 text-center text-xs text-black/60">Status: siap.</p>
                </div>

                <div class="mt-4 flex gap-2">
                    <button id="scan-toggle" class="btn-primary w-full" type="button">Mulai Scan Kamera</button>
                </div>

                {{-- Input Manual Barcode --}}
                <div class="mt-4 border-t border-black/10 pt-4">
                    <p class="text-xs font-semibold text-black/60">Atau input manual:</p>
                    <form id="modal-add-form" class="mt-2 flex gap-2" method="post" action="{{ route('pos.items.add') }}">
                        @csrf
                        <input id="modal-barcode-input" name="barcode" class="flex-1 rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" placeholder="Masukkan barcode" type="text" />
                        <button class="btn-primary" type="submit">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Data produk untuk predictive search
    const products = @json($products ?? []);
    
    const productSearch = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    const searchResultsList = document.getElementById('search-results-list');
    const selectedBarcode = document.getElementById('selected-barcode');
    const posAddForm = document.getElementById('pos-add-form');
    
    // Modal elements
    const openScanModal = document.getElementById('open-scan-modal');
    const closeScanModal = document.getElementById('close-scan-modal');
    const scanModal = document.getElementById('scan-modal');
    const modalBarcodeInput = document.getElementById('modal-barcode-input');
    
    // Scanner elements (dari app.js, perlu diakses di sini juga)
    const scanToggle = document.getElementById('scan-toggle');
    const scannerWrapper = document.getElementById('scanner-wrapper');
    const scanLine = document.getElementById('scan-line');
    
    // Fungsi pencarian produk
    function searchProducts(query) {
        if (!query || query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        
        const filtered = products.filter(p => 
            p.name.toLowerCase().includes(query.toLowerCase()) ||
            (p.barcode && p.barcode.includes(query))
        ).slice(0, 10);
        
        if (filtered.length === 0) {
            searchResultsList.innerHTML = '<li class="px-4 py-3 text-sm text-gray-500">Produk tidak ditemukan</li>';
        } else {
            searchResultsList.innerHTML = filtered.map(p => `
                <li class="cursor-pointer px-4 py-3 hover:bg-gray-50" data-barcode="${p.barcode || ''}" data-name="${p.name}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${p.name}</p>
                            <p class="text-xs text-gray-500">${p.barcode || 'Tanpa barcode'} · Stok: ${p.stock}</p>
                        </div>
                        <span class="text-sm font-semibold text-green-600">Rp ${parseInt(p.price_sell).toLocaleString('id-ID')}</span>
                    </div>
                </li>
            `).join('');
            
            // Event listener untuk setiap item
            searchResultsList.querySelectorAll('li').forEach(li => {
                li.addEventListener('click', () => {
                    const barcode = li.dataset.barcode;
                    const name = li.dataset.name;
                    
                    if (barcode) {
                        selectedBarcode.value = barcode;
                        posAddForm.submit();
                    } else {
                        alert('Produk "' + name + '" tidak memiliki barcode. Silakan tambahkan barcode di menu Inventori.');
                    }
                    searchResults.classList.add('hidden');
                    productSearch.value = '';
                });
            });
        }
        
        searchResults.classList.remove('hidden');
    }
    
    // Event listener untuk input pencarian
    productSearch.addEventListener('input', (e) => {
        searchProducts(e.target.value);
    });
    
    // Tutup dropdown saat klik di luar
    document.addEventListener('click', (e) => {
        if (!productSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
    
    // Modal controls - Buka modal
    if (openScanModal) {
        openScanModal.addEventListener('click', () => {
            scanModal.classList.remove('hidden');
            scanModal.classList.add('flex');
            if (modalBarcodeInput) modalBarcodeInput.focus();
        });
    }
    
    // Modal controls - Tutup modal
    if (closeScanModal) {
        closeScanModal.addEventListener('click', () => {
            // Dapatkan status scanner dari variabel global yang di-set di app.js
            const isScanning = window.posScanner && window.posScanner.isScanning;
            const scanner = window.posScanner && window.posScanner.instance;
            
            if (scanner && isScanning) {
                scanner.stop().then(() => {
                    if (window.posScanner) window.posScanner.isScanning = false;
                    if (scanToggle) scanToggle.textContent = 'Mulai Scan Kamera';
                    if (scannerWrapper) scannerWrapper.classList.remove('scanning-active');
                    if (scanLine) scanLine.classList.remove('scanning');
                }).catch(() => {});
            }
            scanModal.classList.add('hidden');
            scanModal.classList.remove('flex');
        });
    }
    
    // Tutup modal saat klik di luar
    if (scanModal) {
        scanModal.addEventListener('click', (e) => {
            if (e.target === scanModal && closeScanModal) {
                closeScanModal.click();
            }
        });
    }
</script>
@endpush
