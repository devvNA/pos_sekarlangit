<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛒</text></svg>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=sora:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes scanMove {

            0%,
            100% {
                top: 10%;
            }

            50% {
                top: 90%;
            }
        }

        .scan-line.scanning {
            animation: scanMove 1.5s ease-in-out infinite;
            opacity: 1 !important;
        }

        .scanning-active #scan-overlay {
            opacity: 1 !important;
        }

        #scanner-wrapper.scanning-active {
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.4);
        }

        /* Predictive search dropdown */
        #search-results {
            left: 0;
            right: 0;
        }

        #search-results:not(.hidden) {
            display: block;
        }

        /* Hilangkan frame putih default dari html5-qrcode */
        #scanner-wrapper video {
            border: none !important;
            outline: none !important;
        }

        #scanner-wrapper canvas {
            border: none !important;
            outline: none !important;
        }

        /* Hilangkan qr-shaded-region (frame putih) */
        #scanner-wrapper .qr-shaded-region {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="min-h-screen md:flex">
        <aside id="app-sidebar"
            class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col px-4 py-4 shadow-lg md:static md:flex md:shadow-none"
            style="background-color: #1e3a5f;">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-gray-900 font-bold text-lg">
                        TS</div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-white/80">Toko Sekarlangit</p>
                        <h1 class="text-xl font-bold text-white">POS Sekarlangit</h1>
                    </div>
                </div>
                <button id="sidebar-close"
                    class="rounded-full border-2 border-white px-2 py-1 text-xs font-bold text-white md:hidden"
                    type="button">Tutup</button>
            </div>

            <div class="mt-4 rounded-xl border-2 border-white/30 bg-white/10 p-3 text-xs text-white">
                <p class="font-bold text-base">Karanggintung</p>
                <p class="mt-1 text-sm">JL Flamboyan</p>
            </div>

            <nav class="mt-6 space-y-6 text-base">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-white/60">Utama</p>
                    <a class="flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('dashboard') }}">
                        <span>Beranda</span>
                    </a>
                    <a class="mt-2 flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('pos.*') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('pos.index') }}">
                        <span>Penjualan</span>
                    </a>
                </div>
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-white/60">Produk</p>
                    <a class="flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('inventory.*') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('inventory.index') }}">
                        <span>Inventori</span>
                    </a>
                    <a class="mt-2 flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('suppliers.*') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('suppliers.index') }}">
                        <span>Pemasok</span>
                    </a>
                </div>
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-white/60">Keuangan</p>
                    <a class="flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('receivables.*') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('receivables.index') }}">
                        <span>Piutang</span>
                    </a>
                    <a class="mt-2 flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('cash.*') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('cash.index') }}">
                        <span>Buku Kas</span>
                    </a>
                    <a class="mt-2 flex items-center gap-2 rounded-xl px-3 py-2 font-bold {{ request()->routeIs('reports.*') ? 'bg-green-600 text-white' : 'text-white hover:bg-white/10' }}"
                        href="{{ route('reports.index') }}">
                        <span>Laporan</span>
                    </a>
                </div>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-black/50 md:hidden"></div>

        <div class="flex-1">
            <header class="flex items-center justify-between border-b-4 px-4 py-4 shadow-md md:px-6"
                style="border-color: #1e3a5f; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);">
                <button id="sidebar-toggle"
                    class="rounded-lg border-2 px-3 py-2 text-base font-bold text-white md:hidden" type="button"
                    style="background-color: #1b6b42; border-color: #1b6b42;">Menu</button>
                <div class="text-lg font-semibold text-gray-900">Sistem POS untuk Toko Sekarlangit</div>

                <!-- User Profile & Logout -->
                @auth
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-600">Admin</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold text-white transition hover:bg-red-600"
                                style="background-color: #1b6b42;">
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                @endauth
            </header>

            <main class="px-4 py-6 md:px-6 md:py-8" style="background-color: #f9fafb;">
                <div class="w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
