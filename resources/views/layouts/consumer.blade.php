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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F5F0E1; background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%238B4513' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
        h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Playfair Display', serif; }
        .leather-header { background: linear-gradient(135deg, #5C3317 0%, #8B4513 50%, #A0522D 100%); }
        .leather-topbar { background: #3E2723; }
        .product-card { transition: transform 0.3s, box-shadow 0.3s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(92,51,23,0.18); }
        .product-image { aspect-ratio: 1; object-fit: cover; }
        .price-tag { color: #8B4513; }
        .btn-primary { background: #8B4513; }
        .btn-primary:hover { background: #5C3317; }
        .btn-outline { border: 1.5px solid #8B4513; color: #8B4513; }
        .btn-outline:hover { background: #8B4513; color: white; }
        .search-input:focus { border-color: #A0522D; box-shadow: 0 0 0 2px rgba(160,82,45,0.25); }
        .leather-card { background: white; border: 1px solid rgba(139,69,19,0.12); }
        .gold-accent { color: #C5A059; }
        .leather-gradient-text { background: linear-gradient(135deg, #8B4513, #C5A059); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-banner { background: linear-gradient(135deg, #3E2723 0%, #5C3317 30%, #8B4513 70%, #A0522D 100%); }
        .hero-overlay { background: radial-gradient(ellipse at top right, rgba(197,160,89,0.15), transparent 60%); }
        .category-pill { transition: all 0.2s; }
        .category-pill:hover { background: #A0522D; color: white; }
        .category-pill.active { background: #8B4513; color: white; box-shadow: 0 2px 8px rgba(139,69,19,0.3); }
        .leather-divider { height: 3px; background: linear-gradient(90deg, transparent, #C5A059, transparent); }
        .footer-leather { background: linear-gradient(180deg, #3E2723 0%, #2C1810 100%); }
        .badge-gold { background: linear-gradient(135deg, #C5A059, #D4AF37); color: #3E2723; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @php
        $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
    @endphp
</head>
<body class="min-h-screen">
    <!-- Top Header Bar -->
    <div class="leather-topbar text-white text-xs py-1.5">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.46a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="text-white/80">Berliano Leather — Premium Leather Goods</span>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->role === 'customer')
                        <span class="text-white/90">Halo, {{ Auth::user()->name }}</span>
                    @else
                        <a href="{{ route('consumer.login') }}" class="hover:text-[#C5A059] transition">Login Konsumen</a>
                    @endif
                @else
                    <a href="{{ route('consumer.login') }}" class="hover:text-[#C5A059] transition">Login</a>
                    <span class="text-white/30">|</span>
                    <a href="{{ route('consumer.register') }}" class="hover:text-[#C5A059] transition">Daftar</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="leather-header py-3 sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between">
                <!-- Left Side: Three Dots Menu (Kebab Menu) for Mobile + Logo -->
                <div class="flex items-center gap-1">
                    <!-- Three Dots Menu (Kebab Menu) for Mobile - Left Corner -->
                    <div x-data="{ open: false }" class="relative md:hidden">
                        <button @click="open = !open" @click.outside="open = false" class="p-2 text-white hover:text-[#C5A059] transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <!-- Dropdown Menu - Left aligned -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="absolute left-0 mt-2 w-56 bg-[#F5F0E1] rounded-lg shadow-xl border border-[#8B4513]/10 overflow-hidden z-50">
                            <div class="py-2">
                                <a href="{{ route('consumer.hubungi-kami') }}" @click="open = false" class="block px-4 py-3 text-[#3E2723] hover:bg-[#8B4513] hover:text-white transition font-medium">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Hubungi Kami
                                    </div>
                                </a>
                                <a href="{{ route('consumer.pengembalian-barang') }}" @click="open = false" class="block px-4 py-3 text-[#3E2723] hover:bg-[#8B4513] hover:text-white transition font-medium">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Pengembalian Barang
                                    </div>
                                </a>
                                <a href="{{ route('consumer.garansi-produk') }}" @click="open = false" class="block px-4 py-3 text-[#3E2723] hover:bg-[#8B4513] hover:text-white transition font-medium">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        Garansi Produk
                                    </div>
                                </a>
                                <a href="{{ route('consumer.bantuan') }}" @click="open = false" class="block px-4 py-3 text-[#3E2723] hover:bg-[#8B4513] hover:text-white transition font-medium">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Bantuan (FAQ)
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Logo -->
                    <a href="{{ route('consumer.catalog') }}" class="flex items-center space-x-2 shrink-0">
                        @if($logoPerusahaan)
                            <img src="{{ asset('storage/' . $logoPerusahaan) }}" alt="Logo" class="h-10 w-10 rounded-lg object-cover border-2 border-[#C5A059]/40">
                        @else
                            <div class="w-10 h-10 bg-[#C5A059] rounded-lg flex items-center justify-center shadow-md">
                                <span class="text-[#3E2723] font-bold text-sm font-display">BL</span>
                            </div>
                        @endif
                        <div class="flex flex-col leading-none">
                            <span class="text-xl font-bold text-white font-display">Berliano</span>
                            <span class="text-[10px] text-[#C5A059] tracking-widest uppercase">Leather Craft</span>
                        </div>
                    </a>
                </div>

                <!-- Right Side Icons: Cart, Notification, Profile -->
                <div class="flex items-center gap-2">
                    <!-- Cart Icon -->
                    @php
                        if (Auth::check() && Auth::user()->role === 'customer') {
                            $cartCount = \App\Models\Cart::where('user_id', Auth::id())->count();
                        } else {
                            $cartCount = \App\Models\Cart::where('session_id', session()->getId())->count();
                        }
                    @endphp
                    <a href="{{ route('consumer.cart') }}" class="relative p-2 text-white hover:text-[#C5A059] transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-[#C5A059] text-[#3E2723] text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @auth
                        @if(Auth::user()->role === 'customer')
                            @php
                                $userOrders = \App\Models\Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->limit(5)->get();
                                $orderNotifCount = \App\Models\Order::where('user_id', Auth::id())->whereIn('status', ['pending', 'processing'])->count();
                            @endphp
                            <!-- Notification Bell -->
                            <div class="relative">
                                <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" class="p-2 text-white hover:text-[#C5A059] transition relative">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    @if($orderNotifCount > 0)
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">{{ $orderNotifCount }}</span>
                                    @endif
                                </button>
                                <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-2xl border border-[#8B4513]/10 overflow-hidden z-50">
                                    <div class="bg-[#8B4513] text-white px-4 py-3">
                                        <h3 class="font-bold text-sm">Notifikasi Pesanan</h3>
                                    </div>
                                    <div class="max-h-72 overflow-y-auto">
                                        @forelse($userOrders as $order)
                                            @php
                                                $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'processing' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800'];
                                                $statusLabels = ['pending' => 'Pending', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                                            @endphp
                                            <a href="{{ route('consumer.profile.order.detail', $order->id) }}" class="block px-4 py-3 border-b border-gray-100 hover:bg-[#F5F0E1] transition">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs font-bold text-[#3E2723] truncate">{{ $order->order_number }}</p>
                                                        <p class="text-[10px] text-gray-500 mt-0.5">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                                        <p class="text-[9px] text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                </svg>
                                                Belum ada pesanan
                                            </div>
                                        @endforelse
                                    </div>
                                    <a href="{{ route('consumer.profile') }}" class="block text-center text-xs py-3 bg-[#F5F0E1] text-[#8B4513] hover:bg-[#8B4513] hover:text-white transition font-medium">
                                        Lihat Semua Pesanan →
                                    </a>
                                </div>
                            </div>
                            <!-- Profile Icon -->
                            <a href="{{ route('consumer.profile') }}" class="p-2 text-white hover:text-[#C5A059] transition">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages (Toast Notifications) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-xl shadow-2xl',
                        title: 'text-sm font-semibold'
                    }
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-xl shadow-2xl',
                        title: 'text-sm font-semibold'
                    }
                });
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: '{{ $error }}',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                @endforeach
            @endif
        });
    </script>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-leather text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-[#C5A059] rounded-lg flex items-center justify-center">
                            <span class="text-[#3E2723] font-bold text-sm font-display">BL</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-white font-display text-lg">Berliano Leather</h4>
                            <p class="text-xs text-[#C5A059] tracking-widest uppercase">Premium Leather Craft</p>
                        </div>
                    </div>
                    <p class="text-sm text-white/60 max-w-md">Toko resmi produk kulit berkualitas tinggi. Dibuat dengan keahlian artisan, 100% kulit asli pilihan terbaik untuk gaya dan ketahanan maksimal.</p>
                </div>
                <div>
                    <h4 class="font-bold text-[#C5A059] mb-3 uppercase text-sm tracking-wider">Layanan</h4>
                    <ul class="space-y-2 text-sm text-white/60">
                        <li class="hover:text-[#C5A059] transition cursor-pointer">Bantuan</li>
                        <li class="hover:text-[#C5A059] transition cursor-pointer">Pengembalian Barang</li>
                        <li class="hover:text-[#C5A059] transition cursor-pointer">Garansi Produk</li>
                        <li class="hover:text-[#C5A059] transition cursor-pointer">Hubungi Kami</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-[#C5A059] mb-3 uppercase text-sm tracking-wider">Pembayaran</h4>
                    <p class="text-sm text-white/60 mb-3">Transfer Bank • QRIS • COD</p>
                    <div class="flex gap-2">
                        <span class="badge-gold text-xs font-bold px-3 py-1 rounded-full">100% Kulit Asli</span>
                    </div>
                </div>
            </div>
            <div class="leather-divider my-6"></div>
            <div class="text-center text-sm text-white/40">
                &copy; {{ date('Y') }} Berliano Leather. Hak cipta dilindungi.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>