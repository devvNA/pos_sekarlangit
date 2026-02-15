@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Inventori Produk</h2>
                <p class="text-sm text-black/60">Kelola stok dan harga jual/beli.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form class="flex gap-2" method="get" action="{{ route('inventory.index') }}">
                    <input name="q" value="{{ $search }}" class="rounded-lg border border-black/20 bg-white px-3 py-2 text-sm" type="text" placeholder="Cari produk/barcode" />
                    <button class="btn-secondary" type="submit">Cari</button>
                </form>
                <a class="btn-primary" href="{{ route('inventory.create') }}">Tambah Produk</a>
            </div>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-sm">
                <thead class="bg-black/5 text-xs uppercase tracking-wide text-black/60">
                    <tr>
                        <th class="px-3 py-2">Produk</th>
                        <th class="px-3 py-2">Barcode</th>
                        <th class="px-3 py-2">Stok</th>
                        <th class="px-3 py-2">Harga Jual</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-t border-black/10">
                            <td class="px-3 py-3">
                                <p class="font-semibold">{{ $product->name }}</p>
                                <p class="text-xs text-black/50">{{ $product->supplier?->name ?? 'Tanpa pemasok' }}</p>
                            </td>
                            <td class="px-3 py-3">{{ $product->barcode }}</td>
                            <td class="px-3 py-3">
                                {{ $product->stock }} {{ $product->unit }}
                                @if ($product->stock <= $product->min_stock)
                                    <span class="ml-2 text-xs font-semibold text-red-600">Menipis</span>
                                @endif
                            </td>
                            <td class="px-3 py-3">Rp {{ number_format($product->price_sell, 0, ',', '.') }}</td>
                            <td class="px-3 py-3">
                                @if ($product->active)
                                    <span class="text-xs font-semibold text-emerald-700">Aktif</span>
                                @else
                                    <span class="text-xs font-semibold text-black/50">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a class="text-xs font-semibold text-emerald-700" href="{{ route('inventory.edit', $product->id) }}">Edit</a>
                                    <form method="post" action="{{ route('inventory.destroy', $product->id) }}" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-semibold text-red-600" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-t border-black/10">
                            <td class="px-3 py-3" colspan="6">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </section>
@endsection
