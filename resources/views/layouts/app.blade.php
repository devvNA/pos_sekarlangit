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
        <div class="min-h-screen md:flex">
            <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-40 hidden w-72 flex-col border-r border-black/10 bg-white px-5 py-6 shadow-lg md:static md:flex md:shadow-none">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-black text-white">TS</div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-black/60">Toko Sekarlangit</p>
                            <h1 class="text-lg font-bold">POS Sekarlangit</h1>
                        </div>
                    </div>
                    <button id="sidebar-close" class="rounded-full border border-black/10 px-2 py-1 text-xs md:hidden" type="button">Tutup</button>
                </div>

                <div class="mt-6 rounded-xl border border-black/10 bg-[#fdf8ef] p-3 text-xs text-black/70">
                    <p class="font-semibold">Karanggintung</p>
                    <p class="mt-1">JL Flamboyan</p>
                </div>

                <nav class="mt-6 space-y-5 text-sm">
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-black/40">Utama</p>
                        <a class="flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('dashboard') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('dashboard') }}">
                            <span>Beranda</span>
                        </a>
                        <a class="mt-2 flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('pos.*') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('pos.index') }}">
                            <span>Penjualan</span>
                        </a>
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-black/40">Produk</p>
                        <a class="flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('inventory.*') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('inventory.index') }}">
                            <span>Inventori</span>
                        </a>
                        <a class="mt-2 flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('suppliers.*') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('suppliers.index') }}">
                            <span>Pemasok</span>
                        </a>
                    </div>
                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-black/40">Keuangan</p>
                        <a class="flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('receivables.*') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('receivables.index') }}">
                            <span>Piutang</span>
                        </a>
                        <a class="mt-2 flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('cash.*') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('cash.index') }}">
                            <span>Buku Kas</span>
                        </a>
                        <a class="mt-2 flex items-center gap-3 rounded-xl px-3 py-2 font-semibold {{ request()->routeIs('reports.*') ? 'bg-black text-white' : 'text-black/70 hover:bg-black/5' }}" href="{{ route('reports.index') }}">
                            <span>Laporan</span>
                        </a>
                    </div>
                </nav>
            </aside>

            <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/30 md:hidden"></div>

            <div class="flex-1">
                <header class="flex items-center justify-between border-b border-black/10 bg-white/80 px-4 py-4 shadow-sm backdrop-blur md:px-8">
                    <button id="sidebar-toggle" class="rounded-xl border border-black/10 px-3 py-2 text-sm font-semibold md:hidden" type="button">Menu</button>
                    <div class="text-sm text-black/60">Sistem POS ringan untuk operasional harian.</div>
                </header>

                <main class="px-4 py-6 md:px-8 md:py-8">
                    <div class="w-full">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
