@extends('layouts.admin')

@section('title', 'Edit Akun')
@section('title_header', 'Edit Akun')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
        <div class="mb-6 border-b border-white/5 pb-4">
            <h2 class="text-lg font-bold text-white">Edit Akun: {{ $operator->name }}</h2>
            <p class="text-xs text-gray-400">Perbarui informasi akun operator.</p>
        </div>

        <form action="{{ route('admin.operator.update', $operator->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $operator->name) }}" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" required>
                    @error('name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $operator->email) }}" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" required>
                    @error('email') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1">Role</label>
                    <select name="sub_role" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                        <option value="pekerja" {{ old('sub_role', $operator->sub_role) === 'pekerja' ? 'selected' : '' }}>Pekerja (Tidak Bisa Login)</option>
                        <option value="kepala_gudang" {{ old('sub_role', $operator->sub_role) === 'kepala_gudang' ? 'selected' : '' }}>Kepala Gudang (Bisa Login)</option>
                    </select>
                    @error('sub_role') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1">Foto (Opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                    @error('foto') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    @if($operator->foto)
                        <p class="text-xs text-gray-500 mt-1">Foto saat ini: <a href="{{ asset('storage/' . $operator->foto) }}" target="_blank" class="text-teal-500">Lihat</a></p>
                    @endif
                </div>
            </div>

            <div class="bg-yellow-500/5 border border-yellow-500/10 p-4 rounded-xl mt-4">
                <p class="text-[10px] text-yellow-500 font-bold mb-2">Ganti Password (Opsional)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <input type="password" name="password" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="Password Baru">
                        @error('password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="Konfirmasi Password">
                    </div>
                </div>
                <p class="text-[10px] text-gray-500 mt-2">*Kosongkan jika tidak ingin mengganti password.</p>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white text-sm font-bold py-2.5 px-6 rounded-xl transition shadow-lg shadow-teal-900/20">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.operator.index') }}" class="text-gray-500 hover:text-white text-sm font-bold py-2.5 px-4 transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection