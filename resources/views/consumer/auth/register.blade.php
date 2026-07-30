@extends('layouts.consumer')

@section('title', 'Daftar - Berliano Leather')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-amber-700 rounded-lg flex items-center justify-center mb-4">
                <span class="text-white font-bold text-2xl">BL</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h2>
            <p class="mt-2 text-gray-600">Bergabung dengan Berliano Leather</p>
        </div>

        <form method="POST" action="{{ route('consumer.register.process') }}" class="mt-8 space-y-6 bg-white p-8 rounded-lg shadow-md">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">No. HP (Opsional)</label>
                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('no_hp') border-red-500 @enderror">
                @error('no_hp')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
            </div>

            <button type="submit" class="w-full bg-amber-700 text-white py-2 px-4 rounded-lg hover:bg-amber-800 transition font-medium">
                Daftar
            </button>

            <div class="text-center text-sm">
                <p class="text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('consumer.login') }}" class="text-amber-700 hover:text-amber-800 font-medium">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

