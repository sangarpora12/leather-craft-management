@extends('layouts.admin')

@section('title', 'Catat Barang Keluar')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Catat Barang Keluar</h1>
            <p class="text-gray-400 text-sm mt-1">Kurangi stok untuk penjualan, kerusakan, atau sample</p>
        </div>
        <a href="{{ route('admin.pakai-produk.index') }}" 
           class="bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold py-2 px-4 rounded-xl border border-gray-700 transition flex items-center gap-2">
            &larr; Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-xl">
        <form action="{{ route('admin.pakai-produk.store') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Produk Selection --}}
            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2">Pilih Produk</label>
                <select name="produk_jadi_id" class="w-full bg-black/20 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 transition">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" class="bg-gray-900">
                            {{ $product->kode_produk }} - {{ $product->nama_produk }} (Stok: {{ $product->stok }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Jumlah --}}
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2">Jumlah Keluar</label>
                    <input type="number" name="jumlah_keluar" min="1" required
                           class="w-full bg-black/20 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 transition"
                           placeholder="0">
                </div>

                {{-- Alasan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2">Alasan</label>
                    <select name="alasan" class="w-full bg-black/20 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 transition">
                        @foreach($alasan as $a)
                            <option value="{{ $a }}" class="bg-gray-900">{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2">Tanggal Transaksi</label>
                <input type="date" name="tanggal_keluar" value="{{ date('Y-m-d') }}" required
                       class="w-full bg-black/20 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 transition">
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-bold text-gray-400 mb-2">Catatan (Opsional)</label>
                <textarea name="catatan" rows="3" 
                          class="w-full bg-black/20 border border-gray-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500 transition"
                          placeholder="Keterangan tambahan..."></textarea>
            </div>

            <div class="pt-4 border-t border-white/10 flex justify-end">
                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-red-900/20 transition transform hover:scale-[1.02]">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection