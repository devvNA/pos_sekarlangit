@extends('layouts.app')

@section('content')
    <section class="rounded-2xl border border-black/10 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold">Pemasok</h2>
                <p class="text-sm text-black/60">Daftar pemasok dan kontak.</p>
            </div>
            <button class="btn-primary" type="button">Tambah Pemasok</button>
        </div>

        <div class="mt-4 overflow-hidden rounded-xl border border-black/10">
            <table class="w-full text-left text-sm">
                <thead class="bg-black/5 text-xs uppercase tracking-wide text-black/60">
                    <tr>
                        <th class="px-3 py-2">Nama</th>
                        <th class="px-3 py-2">Telepon</th>
                        <th class="px-3 py-2">Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-black/10">
                        <td class="px-3 py-3" colspan="3">Belum ada pemasok.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection
