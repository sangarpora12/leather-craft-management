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
                    <label class="block text-xs font-bold text-gray-400 mb-1">Posisi / Jabatan</label>
                    <select name="jabatan" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition">
                        <option value="Operator Jahit" {{ old('jabatan', $operator->jabatan) === 'Operator Jahit' ? 'selected' : '' }}>Operator Jahit</option>
                        <option value="Operator Potong" {{ old('jabatan', $operator->jabatan) === 'Operator Potong' ? 'selected' : '' }}>Operator Potong</option>
                        <option value="Operator Finishing" {{ old('jabatan', $operator->jabatan) === 'Operator Finishing' ? 'selected' : '' }}>Operator Finishing</option>
                        <option value="Kepala Gudang" {{ old('jabatan', $operator->jabatan) === 'Kepala Gudang' ? 'selected' : '' }}>Kepala Gudang</option>
                    </select>
                    @error('jabatan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
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
                        <label class="block text-xs font-bold text-gray-400 mb-1">Password Baru</label>
                        <input type="password" name="password" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="Password Baru">
                        @error('password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="Konfirmasi Password">
                    </div>
                </div>
                <p class="text-[10px] text-gray-500 mt-2">*Kosongkan jika tidak ingin mengganti password.</p>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="bg-orange-600 hover:bg-orange-500 text-white text-sm font-bold py-2.5 px-6 rounded-xl transition shadow-lg shadow-orange-900/20">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.pekerja-master.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white text-sm font-bold py-2.5 px-4 rounded-xl transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection