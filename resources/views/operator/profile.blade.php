@extends('layouts.operator_app')

@section('title', 'Profil Saya')

@section('content')
<div class="min-h-screen bg-[#0B0E14] pb-24">
    <div class="px-6 py-6 safe-top">
        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('operator.dashboard') }}" class="text-white text-xl p-2 -ml-2 rounded-lg hover:bg-white/5 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-white tracking-wider">PROFIL</h1>
            <div class="w-10"></div>
        </div>

        {{-- PROFILE CARD --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-6 mb-6 text-center">
            {{-- Avatar --}}
            @if($user->foto)
                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="w-28 h-28 rounded-full object-cover border-4 border-blue-500/30 mx-auto mb-4">
            @else
                <div class="w-28 h-28 rounded-full bg-blue-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-500/30 mx-auto mb-4">
                    {{ substr($user->name ?? $user->username, 0, 1) }}
                </div>
            @endif

            <h2 class="text-xl font-bold text-white mb-1">{{ $user->name ?? $user->username }}</h2>
            <span class="inline-flex items-center bg-blue-500/15 text-blue-400 text-xs font-bold px-3 py-1 rounded-full border border-blue-500/25 uppercase tracking-wider">
                Operator Produksi
            </span>
        </div>

        {{-- MINI STATS --}}
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 text-center">
                <div class="w-10 h-10 bg-emerald-500/15 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-box text-emerald-400 text-sm"></i>
                </div>
                <p class="text-xl font-bold text-white">{{ number_format($user->productions()->sum('jumlah_produksi')) }}</p>
                <p class="text-gray-500 text-[10px] uppercase tracking-wider mt-1">Total Produksi</p>
            </div>
            <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 text-center">
                <div class="w-10 h-10 bg-blue-500/15 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-check-circle text-blue-400 text-sm"></i>
                </div>
                <p class="text-xl font-bold text-white">{{ $user->productions()->where('status', 'SELESAI')->count() }}</p>
                <p class="text-gray-500 text-[10px] uppercase tracking-wider mt-1">SPK Selesai</p>
            </div>
            <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 text-center">
                <div class="w-10 h-10 bg-amber-500/15 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-gear text-amber-400 text-sm"></i>
                </div>
                <p class="text-xl font-bold text-white">{{ $user->productions()->where('status', 'PROSES')->count() }}</p>
                <p class="text-gray-500 text-[10px] uppercase tracking-wider mt-1">Sedang Proses</p>
            </div>
        </div>

        {{-- INFO AKUN --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-5 mb-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-circle text-blue-400"></i> Informasi Akun
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-3 border-b border-white/5">
                    <span class="text-gray-400 text-sm"><i class="fa-solid fa-user mr-2 text-gray-500"></i>Username</span>
                    <span class="text-white font-medium text-sm">{{ $user->username }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-white/5">
                    <span class="text-gray-400 text-sm"><i class="fa-solid fa-envelope mr-2 text-gray-500"></i>Email</span>
                    <span class="text-white font-medium text-sm">{{ $user->email }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-white/5">
                    <span class="text-gray-400 text-sm"><i class="fa-solid fa-calendar mr-2 text-gray-500"></i>Bergabung</span>
                    <span class="text-white font-medium text-sm">{{ $user->created_at->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM Y') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-gray-400 text-sm"><i class="fa-solid fa-shield mr-2 text-gray-500"></i>Role</span>
                    <span class="text-white font-medium text-sm capitalize">{{ $user->role }}</span>
                </div>
            </div>
        </div>

        {{-- UPDATE FOTO --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-5 mb-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-camera text-emerald-400"></i> Ganti Foto Profil
            </h3>

            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-3 mb-4">
                    <p class="text-green-400 text-sm"><i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-3 mb-4">
                    @foreach($errors->all() as $error)
                        <p class="text-red-400 text-sm"><i class="fa-solid fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('operator.updateFoto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg"
                           class="w-full bg-[#0B0E14] border border-white/10 rounded-xl px-4 py-3 text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:text-sm file:font-medium hover:file:bg-blue-500 transition">
                    <p class="text-gray-500 text-xs mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Foto Profil
                </button>
            </form>
        </div>

        {{-- LOGOUT --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-red-600/10 border border-red-500/20 hover:bg-red-600/20 text-red-400 font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-sign-out-alt"></i>Keluar Akun
            </button>
        </form>
    </div>
</div>
@endsection
