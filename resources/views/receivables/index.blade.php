@extends('layouts.app')

@section('content')
    <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Buku Piutang</h2>
                <p class="text-sm text-black/60">Catat hutang pelanggan dan pembayaran.</p>
            </div>
            <button class="btn-primary" type="button">Tambah Piutang</button>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-sm">
                <thead class="background-color: #1e3a5f; tracking-wide text-black/60">
                    <tr>
                        <th class="px-3 py-2">Pelanggan</th>
                        <th class="px-3 py-2">Total</th>
                        <th class="px-3 py-2">Sisa</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-black/10">
                        <td class="px-3 py-3" colspan="4">Belum ada piutang.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
