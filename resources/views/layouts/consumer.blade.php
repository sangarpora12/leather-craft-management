<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berliano Leather - @yield('title', 'Katalog Produk Kulit')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @php
        $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
    @endphp
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('consumer.catalog') }}" class="flex items-center space-x-2">
                        @if($logoPerusahaan)
                            <img src="{{ asset('storage/' . $logoPerusahaan) }}" alt="Logo" class="h-8 w-8 rounded-lg object-cover border border-gray-200">
                        @else
                            <div class="w-8 h-8 bg-amber-700 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">BL</span>
                            </div>
                        @endif
                        <span class="text-xl font-bold text-gray-900">Berliano Leather</span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        @if(Auth::user()->role === 'customer')
                            <a href="{{ route('consumer.cart') }}" class="relative p-2 text-gray-600 hover:text-gray-900">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                @php
                                    $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count();
                                @endphp
                                @if($cartCount > 0)
                                    <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-700">Halo, {{ Auth::user()->name }}</span>
                                <form method="POST" action="{{ route('consumer.logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Admin/operator yang sedang login: tampilkan link login konsumen --}}
                            <a href="{{ route('consumer.login') }}" class="text-gray-600 hover:text-gray-900">Login Konsumen</a>
                            <a href="{{ route('consumer.register') }}" class="bg-amber-700 text-white px-4 py-2 rounded-lg hover:bg-amber-800 transition">
                                Daftar
                            </a>
                        @endif
                    @else
                        <a href="{{ route('consumer.login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="{{ route('consumer.register') }}" class="bg-amber-700 text-white px-4 py-2 rounded-lg hover:bg-amber-800 transition">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Berliano Leather. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>

</parameter>