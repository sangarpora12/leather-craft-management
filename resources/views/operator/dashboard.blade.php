@extends('layouts.operator_app')

@section('content')
<div class="min-h-screen bg-[#0B0E14] pb-24">

    {{-- HEADER HALAMAN --}}
    <div class="px-6 py-6 safe-top bg-gray-800/90 backdrop-blur-sm border-b border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <button onclick="window.dispatchEvent(new CustomEvent('operator:toggleSidebar'))" class="text-white text-xl p-2 -ml-2 rounded-lg hover:bg-white/5 transition">
                <i class="fa-solid fa-bars"></i>
            </button>
            
            <h1 class="text-xl font-bold text-white tracking-wider">DASHBOARD</h1>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('operator.notifications') }}" class="relative text-white text-xl p-2 -mr-2 rounded-lg hover:bg-white/5 transition">
                    <i class="fa-solid fa-bell"></i>
                    @if(($unreadCount ?? 0) > 0)
                    <div class="absolute top-0 right-0 min-w-[18px] h-[18px] px-1 bg-red-500 rounded-full border-2 border-[#0B0E14] flex items-center justify-center text-[10px] font-bold text-white">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </div>
                    @endif
                </a>
                <a href="{{ route('operator.profile') }}" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold hover:bg-blue-500 transition overflow-hidden">
                    @if(Auth::user()->foto)
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="w-full h-full object-cover rounded-full" alt="Profil">
                    @elseif(Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" class="w-full h-full object-cover rounded-full" alt="Profil">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </a>
            </div>
        </div>

        {{-- SEARCH BAR --}}
        <form action="{{ route('operator.search') }}" method="GET" class="relative mb-6">
            <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <input type="text" name="q"
                   placeholder="Cari bahan, produk, atau SPK..." 
                   class="w-full bg-[#1C222D] border border-white/5 rounded-2xl px-5 py-4 pl-14 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-blue-500 transition-all">
        </form>

        {{-- PROFIL CARD STATISTIK --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-6 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">{{ Auth::user()->name }}</h2>
                    <span class="inline-flex items-center mt-2 bg-green-500/15 text-green-400 text-sm font-medium px-3 py-1 rounded-full border border-green-500/25">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        {{ Auth::user()->jabatan ?? 'Operator' }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-gray-400 text-sm">{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMM Y') }}</p>
                    <p class="text-white font-semibold text-sm mt-1">{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('H:i') }} WIB</p>
                </div>
            </div>

            {{-- 3 KOLOM STATISTIK --}}
            <div class="grid grid-cols-3 divide-x divide-white/10">
                <div class="text-center">
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Tugas</p>
                    <p class="text-3xl font-bold text-white mb-1">{{ $tugasAktifCount }}</p>
                    <p class="text-gray-500 text-xs">{{ $tugasPendingCount }} Pending</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Pengadaan</p>
                    <p class="text-3xl font-bold text-green-400 mb-1">{{ number_format($totalPengadaan) }}</p>
                    <p class="text-gray-500 text-xs">Total Tugas</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Total Bahan</p>
                    <p class="text-3xl font-bold text-blue-400 mb-1">{{ number_format($totalStokBahan) }}</p>
                    <p class="text-gray-500 text-xs">Stok Saat Ini</p>
                </div>
            </div>
        </div>

        {{-- 3 CARD STATUS PRODUKSI --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            {{-- PERINTAH BARU --}}
            <a href="{{ route('operator.tugas.index') }}?filter=pending" class="bg-[#1C222D] rounded-2xl border border-white/5 p-5 relative overflow-hidden hover:border-blue-500/30 transition">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-l-2xl"></div>
                <div class="w-10 h-10 bg-blue-500/15 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-check text-blue-400"></i>
                </div>
                <p class="text-2xl font-bold text-white text-center mb-1">{{ $perintahBaruCount }}</p>
                <p class="text-gray-400 text-xs text-center">Perintah Baru</p>
            </a>

            {{-- SEDANG AKTIF --}}
            <a href="{{ route('operator.tugas.index') }}?filter=proses" class="bg-[#1C222D] rounded-2xl border border-white/5 p-5 relative overflow-hidden hover:border-amber-500/30 transition">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500 rounded-l-2xl"></div>
                <div class="w-10 h-10 bg-amber-500/15 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-gear text-amber-400"></i>
                </div>
                <p class="text-2xl font-bold text-white text-center mb-1">{{ $sedangAktifCount }}</p>
                <p class="text-gray-400 text-xs text-center">Sedang Aktif</p>
            </a>

            {{-- TUNGGU VALIDASI --}}
            <a href="{{ route('operator.tugas.index') }}?filter=selesai" class="bg-[#1C222D] rounded-2xl border border-white/5 p-5 relative overflow-hidden hover:border-green-500/30 transition">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500 rounded-l-2xl"></div>
                <div class="w-10 h-10 bg-green-500/15 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-clipboard-check text-green-400"></i>
                </div>
                <p class="text-2xl font-bold text-white text-center mb-1">{{ number_format((int)$jumlahSelesai ?? 0) }}</p>
                <p class="text-gray-400 text-xs text-center">Selesai</p>
            </a>
        </div>

        {{-- RINGKASAN LENGKAP --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-5 mb-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-blue-400"></i> Ringkasan Operasional
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400 text-sm">Bahan Masuk Bulan Ini</span>
                    <span class="text-white font-semibold text-sm">{{ number_format($bahanMasukBulanIni ?? 0) }} unit</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400 text-sm">Bahan Keluar (Produksi)</span>
                    <span class="text-white font-semibold text-sm">{{ number_format($bahanKeluarBulanIni ?? 0) }} unit</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-white/5">
                    <span class="text-gray-400 text-sm">Total Nilai Beli</span>
                    <span class="text-green-400 font-semibold text-sm">Rp {{ number_format($totalHargaBeli ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-gray-400 text-sm">Produksi Selesai Hari Ini</span>
                    <span class="text-blue-400 font-semibold text-sm">{{ number_format((int)$selesaiHariIni ?? 0) }} pcs</span>
                </div>
            </div>
        </div>

        {{-- SHORTCUT MENU --}}
        <div class="grid grid-cols-2 gap-3 mb-6">
            <a href="{{ route('operator.scan.index') }}" class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 flex items-center gap-3 hover:border-blue-500/30 transition">
                <div class="w-10 h-10 bg-blue-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-camera text-blue-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Scan Nota</p>
                    <p class="text-gray-500 text-xs">Input bahan via OCR</p>
                </div>
            </a>
            <a href="{{ route('operator.bahan.tambah') }}" class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 flex items-center gap-3 hover:border-emerald-500/30 transition">
                <div class="w-10 h-10 bg-emerald-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-plus text-emerald-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Tambah Bahan</p>
                    <p class="text-gray-500 text-xs">Input manual stok</p>
                </div>
            </a>
            <a href="{{ route('operator.pengadaan.index') }}" class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 flex items-center gap-3 hover:border-purple-500/30 transition">
                <div class="w-10 h-10 bg-purple-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-cart-flatbed text-purple-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Pengadaan</p>
                    <p class="text-gray-500 text-xs">Daftar belanja</p>
                </div>
            </a>
            <a href="{{ route('operator.resep-produk.index') }}" class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 flex items-center gap-3 hover:border-orange-500/30 transition">
                <div class="w-10 h-10 bg-orange-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-book text-orange-400"></i>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">Resep Produk</p>
                    <p class="text-gray-500 text-xs">Katalog produksi SOP</p>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection