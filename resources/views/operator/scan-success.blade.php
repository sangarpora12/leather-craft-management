@extends('layouts.operator_app')

@section('title', 'Sukses - Scan Nota')

@section('content')
<div class="w-full max-w-md mx-auto bg-[#0B0E14] min-h-screen flex flex-col items-center justify-center px-6 py-8">

    <div class="text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-emerald-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/30">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Berhasil Disimpan</h1>
        <p class="text-gray-400 mb-6">Data bahan masuk telah berhasil disimpan dan menunggu verifikasi admin.</p>
    </div>

    @if(isset($bahanMasuk))
    <div class="w-full bg-[#1A1A1A] rounded-2xl shadow-xl border border-white/10 p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-300 mb-3 border-b border-white/10 pb-2">Detail Input</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-400">No. Transaksi:</span>
                <span class="font-mono text-white">{{ $bahanMasuk->no_transaksi }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Nama Bahan:</span>
                <span class="font-semibold text-white text-right">{{ $bahanMasuk->nama_material }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Jumlah:</span>
                <span class="font-semibold text-white">{{ rtrim(rtrim(number_format($bahanMasuk->jumlah, 2, '.', ''), '0'), '.') }} {{ $bahanMasuk->satuan }}</span>
            </div>
             @if($bahanMasuk->harga_satuan > 0)
            <div class="flex justify-between">
                <span class="text-gray-400">Harga:</span>
                <span class="font-semibold text-white">Rp {{ number_format($bahanMasuk->harga_satuan, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="w-full space-y-3">
        <a href="{{ route('operator.scan.index') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl shadow-lg transition-all duration-200">
            Scan Lagi
        </a>
        <a href="{{ route('operator.dashboard') }}" class="block w-full text-center bg-gray-700 hover:bg-gray-600 text-white font-semibold py-3 rounded-xl shadow-lg transition-all duration-200">
            Kembali ke Menu
        </a>
    </div>

</div>
@endsection