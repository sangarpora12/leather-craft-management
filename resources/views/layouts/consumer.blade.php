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
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .shopee-header { background: linear-gradient(135deg, #ee4d2d 0%, #ff7337 100%); }
        .product-card { transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .product-image { aspect-ratio: 1; object-fit: cover; }
        .price-tag { color: #ee4d2d; }
        .btn-primary { background: #ee4d2d; }
        .btn-primary:hover { background: #d63e1f; }
        .btn-outline { border: 1px solid #ee4d2d; color: #ee4d2d; }
        .btn-outline:hover { background: #ee4d2d; color: white; }
        .search-input:focus { border-color: #ee4d2d; box-shadow: 0 0 0 2px rgba(238,77,45,0.2); }
    </style>
    @php
        $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
    @endphp
</head>
<body class="min-h-screen">
    <!-- Top Header Bar -->
    <div class="bg-[#d63e1f] text-white text-xs py-1">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="text-white/80">Berliano Leather Official Store</span>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->role === 'customer')
                        <span class="text-white/90">Halo, {{ Auth::user()->name }}</span>
                    @else
                        <a href="{{ route('consumer.login') }}" class="hover:text-white transition">Login Konsumen</a>
                    @endif
                @else
                    <a href="{{ route('consumer.login') }}" class="hover:text-white transition">Login</a>
                    <span class="text-white/40">|</span>
                    <a href="{{ route('consumer.register') }}" class="hover:text-white transition">Daftar</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="shopee-header py-3 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center gap-4">
                <!-- Logo -->
                <a href="{{ route('consumer.catalog') }}" class="flex items-center space-x-2 shrink-0">
                    @if($logoPerusahaan)
                        <img src="{{ asset('storage/' . $logoPerusahaan) }}" alt="Logo" class="h-10 w-10 rounded-lg object-cover border-2 border-white/30">
                    @else
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <span class="text-[#ee4d2d] font-bold text-sm">BL</span>
                        </div>
                    @endif
                    <span class="text-xl font-bold text-white">Berliano</span>
                </a>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl">
                    <form method="GET" action="{{ route('consumer.catalog') }}" class="flex">
                        <input type="text" name="search" placeholder="Cari produk kulit..." value="{{ request('search') }}"
                            class="search-input w-full px-4 py-2.5 rounded-l-lg border-0 outline-none text-sm">
                        <button type="submit" class="bg-white text-[#ee4d2d] px-6 py-2.5 rounded-r-lg hover:bg-gray-50 transition font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Cart Icon -->
                @auth
                    @if(Auth::user()->role === 'customer')
                        <a href="{{ route('consumer.cart') }}" class="relative p-2 text-white hover:text-white/80 transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            @php $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count(); @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 bg-yellow-400 text-[#ee4d2d] text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 mt-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="font-bold text-gray-900 mb-3">Berliano Leather</h4>
                    <p class="text-sm text-gray-500">Toko resmi produk kulit berkualitas tinggi. 100% Kulit Asli.</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li>Bantuan</li>
                        <li>Pengembalian Barang</li>
                        <li>Hubungi Kami</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-3">Pembayaran</h4>
                    <p class="text-sm text-gray-500">Transfer Bank • QRIS • COD</p>
                </div>
            </div>
            <div class="border-t mt-6 pt-4 text-center text-sm text-gray-400">
                &copy; {{ date('Y') }} Berliano Leather. Hak cipta dilindungi.
            </div>
        </div>
    </footer>
</body>
</html>