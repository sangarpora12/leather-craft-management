@extends('layouts.consumer')

@section('title', 'Daftar - Berliano Leather')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 relative" style="background-image: url('{{ asset('storage/bahan/Kulit Sapi Premium.jpg') }}'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative z-10 w-full max-w-md">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                @php
                    $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
                @endphp
                @if($logoPerusahaan)
                    <img src="{{ asset('storage/' . $logoPerusahaan) }}" alt="Logo Berliano Leather" class="mx-auto w-20 h-20 object-contain mb-4 drop-shadow-lg">
                @else
                    <div class="mx-auto w-16 h-16 bg-gradient-to-br from-[#C5A059] to-[#8B4513] rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <span class="text-[#3E2723] font-bold text-2xl font-display">BL</span>
                    </div>
                @endif
                <h2 class="text-3xl font-bold text-[#3E2723] font-display">Daftar Akun Baru</h2>
                <p class="mt-2 text-[#8B4513]/60 text-sm">Isi data diri Anda untuk mendaftar</p>
            </div>

            <form method="POST" action="{{ route('consumer.register.process') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-[#5C3317] mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723] transition @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-[#5C3317] mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723] transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-[#5C3317] mb-1">No. HP (Opsional)</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                        class="w-full px-4 py-2.5 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723] transition @error('no_hp') border-red-500 @enderror">
                    @error('no_hp')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-[#5C3317] mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2.5 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723] transition @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#5C3317] mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723] transition">
                </div>

                <button type="submit" class="w-full btn-primary text-white py-2.5 px-4 rounded-lg transition font-medium shadow-md hover:shadow-lg">
                    Daftar
                </button>

                <div class="text-center text-sm pt-2">
                    <p class="text-[#8B4513]/60">
                        Sudah punya akun? 
                        <a href="{{ route('consumer.login') }}" class="text-[#8B4513] hover:text-[#5C3317] font-medium transition">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
