<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS Sekar Langit</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10">
            <!-- Header / Logo -->
            <div class="flex flex-col items-center mb-8 text-center">
                <!-- Logo Icon -->
                <div class="mb-4">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16 38C10.4772 38 6 33.5228 6 28C6 22.4772 10.4772 18 16 18C16.8543 18 17.6748 18.1033 18.4546 18.3003C20.0699 10.5969 26.4902 4.66666 34.3333 4.66666C43.5381 4.66666 51 12.1286 51 21.3333C51 21.9167 50.9698 22.4881 50.9113 23.0465C55.7067 24.2875 59.3333 28.4896 59.3333 33.8333C59.3333 39.8164 50.4832 44.6667 44.5 44.6667H16"
                            fill="#0EA5E9" fill-opacity="0.9" />
                        <path d="M32 48L48 32M48 32H36M48 32V42" stroke="#EAB308" stroke-width="4"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900">POS Sekar Langit</h1>
                <p class="text-gray-500 text-sm mt-1">Sistem Penjualan & Inventory</p>
            </div>

            <!-- Error Message -->
            @if (session('warning'))
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 rounded-r text-sm">
                    <p class="font-medium">Sesi Berakhir</p>
                    <p>{{ session('warning') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r text-sm">
                    <p class="font-medium">Login Gagal</p>
                    <p>{{ $errors->first('email') }}</p>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('authenticate') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        placeholder="admin@sekarlangit.com">
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        placeholder="••••••••">
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-600">
                            Remember Me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-gray-500 hover:text-blue-600 transition duration-150">
                            Forgot Password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    Login
                </button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('submit', function(e) {
            var btn = e.submitter || e.target.querySelector('button[type="submit"]');
            if (!btn || btn.disabled) return;
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="inline h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity:0.25"></circle>' +
                '<path fill="currentColor" style="opacity:0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                '</svg> Masuk...';
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    </script>
</body>

</html>
