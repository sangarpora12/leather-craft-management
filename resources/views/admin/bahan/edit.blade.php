@extends('layouts.admin')

@section('title_header', 'Edit Bahan Baku')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6">Edit Material</h2>

        <form action="{{ route('admin.bahan.update', $bahan->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Nama Material</label>
                <input type="text" name="nama" value="{{ $bahan->nama_material }}" required
                       class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Stok</label>
                    <input type="number" name="stok" value="{{ $bahan->stok }}" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Satuan</label>
                    <input type="text" name="satuan" value="{{ $bahan->satuan }}" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Min Stok</label>
                <input type="number" name="stok_minimum" value="{{ $bahan->stok_minimum }}" required
                       class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
            </div>

            <div class="flex gap-4 pt-6">
                <a href="{{ route('admin.bahan.index') }}"
                   class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-bold text-center transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold transition shadow-lg shadow-red-900/20">
                    Update Material
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
