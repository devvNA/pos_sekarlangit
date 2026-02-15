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
                <input name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text" required />
                @error('name')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Barcode (EAN-13)</label>
                <div class="flex flex-wrap gap-2">
                    <input id="inventory-barcode-input" name="barcode" value="{{ old('barcode') }}" class="flex-1 rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text" required />
                    <button id="inventory-scan-toggle" class="btn-primary" type="button">Scan</button>
                    <button id="inventory-manual-input" class="btn-secondary" type="button">Ketik</button>
                </div>
                <div class="mt-3 rounded-lg border border-dashed border-black/30 bg-white/70 p-4">
                    <p class="text-xs font-semibold text-black/60">Area Kamera</p>
                    <div id="inventory-scanner-preview" class="mt-2 flex h-48 items-center justify-center rounded-md border border-black/10 bg-black/5 text-xs text-black/50">
                        Preview kamera akan muncul di sini.
                    </div>
                    <p id="inventory-scanner-status" class="mt-2 text-xs text-black/60">Status: siap.</p>
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
                <input name="price_buy" value="{{ old('price_buy') }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="number" min="0" required />
                @error('price_buy')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Harga Jual</label>
                <input name="price_sell" value="{{ old('price_sell') }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="number" min="0" required />
                @error('price_sell')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Stok Awal</label>
                <input name="stock" value="{{ old('stock', 0) }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="number" min="0" required />
                @error('stock')
                    <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-black/60">Minimum Stok</label>
                <input name="min_stock" value="{{ old('min_stock', 0) }}" class="w-full rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="number" min="0" required />
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
@endsection
