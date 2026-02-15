<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=sora:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="min-h-screen px-4 py-6 md:px-8 md:py-8">
            <header class="mb-6 flex flex-col gap-4 rounded-2xl border border-black/10 bg-white/90 p-4 shadow-sm backdrop-blur md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-black/60">Toko Sekarlangit</p>
                    <h1 class="text-2xl font-bold">POS Sekarlangit</h1>
                </div>
                <nav class="flex flex-wrap gap-2 text-sm font-semibold">
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('pos.index') }}">POS</a>
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('inventory.index') }}">Inventori</a>
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('suppliers.index') }}">Pemasok</a>
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('receivables.index') }}">Piutang</a>
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('cash.index') }}">Buku Kas</a>
                    <a class="rounded-full border border-black/10 bg-white px-4 py-2 hover:border-black" href="{{ route('reports.index') }}">Laporan</a>
                </nav>
            </header>

            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
