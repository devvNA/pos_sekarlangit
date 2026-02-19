<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sekar Langit</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    @auth
        <!-- Auto redirect jika sudah login -->
        <script>
            window.location.href = "{{ route('dashboard') }}";
        </script>
    @else
        <!-- Welcome Page untuk user yang belum login -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <!-- Logo & Title -->
                <h1 class="text-4xl font-bold text-gray-800 mb-2">POS Sekar Langit</h1>
                <p class="text-gray-600 mb-6">Sistem Penjualan & Inventory</p>

                <!-- Description -->
                <p class="text-gray-700 mb-8 leading-relaxed">
                    Kelola penjualan, stok produk, piutang, dan laporan keuangan dengan mudah dan efisien.
                </p>

                <!-- Login Button -->
                <a href="{{ route('login') }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200">
                    Masuk sebagai Admin
                </a>
            </div>
        </div>
    @endauth
</body>

</html>
