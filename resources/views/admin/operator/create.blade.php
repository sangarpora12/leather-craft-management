@extends('layouts.admin')

@section('title', 'Tambah Operator')
@section('title_header', 'Tambah Operator Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
        <div class="mb-6 border-b border-white/5 pb-4">
            <h2 class="text-lg font-bold text-white">Formulir Operator Baru</h2>
            <p class="text-xs text-gray-400">Isi data di bawah ini untuk membuat akun operator.</p>
        </div>

        <form action="{{ route('admin.operator.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1">NAMA LENGKAP</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="Contoh: Budi Santoso" required>
                @error('name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1">EMAIL LOGIN</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="budi@berliano.com" required>
                @error('email') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1">JABATAN</label>
                <select name="jabatan" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" required>
                    <option value="Staff Produksi" {{ old('jabatan') == 'Staff Produksi' ? 'selected' : '' }}>Staff Produksi</option>
                    <option value="Operator Jahit" {{ old('jabatan') == 'Operator Jahit' ? 'selected' : '' }}>Operator Jahit</option>
                    <option value="Operator Potong" {{ old('jabatan') == 'Operator Potong' ? 'selected' : '' }}>Operator Potong</option>
                    <option value="Operator Finishing" {{ old('jabatan') == 'Operator Finishing' ? 'selected' : '' }}>Operator Finishing</option>
                    <option value="Kepala Gudang" {{ old('jabatan') == 'Kepala Gudang' ? 'selected' : '' }}>Kepala Gudang</option>
                </select>
                @error('jabatan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>



            <div>
                <label class="block text-xs font-bold text-gray-400 mb-1">PASSWORD DEFAULT</label>
                <input type="password" name="password" value="123456" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500 outline-none transition" placeholder="123456" required>
                <p class="text-xs text-gray-500 mt-1">*Password default: 123456 (Bisa diganti operator nanti)</p>
                @error('password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white text-sm font-bold py-2.5 px-6 rounded-xl transition shadow-lg shadow-teal-900/20">
                    Simpan Operator
                </button>
                <a href="{{ route('admin.operator.index') }}" class="text-gray-500 hover:text-white text-sm font-bold py-2.5 px-4 transition">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>
@endsection