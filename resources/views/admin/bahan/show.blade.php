@extends('layouts.admin')

@section('title_header', 'Detail Bahan Baku')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Detail Material</h2>
            <a href="{{ route('admin.bahan.index') }}"
               class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-bold text-sm transition">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase">Nama Material</label>
                    <p class="text-white text-lg font-medium">{{ $bahan->nama_material }}</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase">Kode Material</label>
                    <p class="text-white text-lg font-medium">{{ $bahan->kode_material }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase">Stok</label>
                    <p class="text-white text-lg font-medium {{ $bahan->stok < $bahan->stok_minimum ? 'text-red-500' : 'text-green-500' }}">
                        {{ $bahan->stok }} {{ $bahan->satuan }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase">Min Stok</label>
                    <p class="text-white text-lg font-medium">{{ $bahan->stok_minimum }} {{ $bahan->satuan }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="{{ route('admin.bahan.edit', $bahan->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold transition">
                Edit Material
            </a>
            <form action="{{ route('admin.bahan.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Hapus bahan ini?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold transition">
                    Hapus Material
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
