<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Bahan - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50 border-b border-white/10">
            <a href="{{ route('operator.bahan.create') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Detail Bahan Baru</h1>
            <div class="w-6 h-6"></div>
        </header>

        <div class="p-6">
            
            @php
                // Ambil data dari session. Gunakan fungsi number_format untuk harga.
                $kode = session('kode') ?? 'BAH-XXXX';
                $nama = session('nama') ?? 'Nama Bahan';
                $stok = session('stok_masuk') ?? 0;
                $satuan = session('satuan_bahan') ?? 'Unit';
                $harga_unit = number_format(session('harga_unit') ?? 0, 0, ',', '.');
                $total_transaksi = number_format(session('total_transaksi') ?? 0, 0, ',', '.');
            @endphp

            <div class="bg-green-600/20 border border-green-500/50 p-4 rounded-xl mb-6 text-center">
                <svg class="w-10 h-10 text-green-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-bold text-green-500 text-lg">Stok Bahan Berhasil Ditambahkan!</p>
                <p class="text-xs text-gray-300 mt-1">Data sudah tercatat di sistem inventaris.</p>
            </div>

            <div class="bg-[#1A1A1A] p-5 rounded-2xl border border-white/10 space-y-5">
                <h3 class="font-bold text-white text-lg border-b border-white/10 pb-3">Ringkasan Item</h3>

                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-400">Kode Material Baru</p>
                    <p class="text-lg font-extrabold text-orange-400 font-mono">{{ $kode }}</p> 
                </div>
                
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-400">Nama Bahan</p>
                    <p class="text-sm font-bold text-white text-right">{{ $nama }}</p>
                </div>
                
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-400">Stok Masuk</p>
                    <p class="text-sm font-bold text-white">{{ $stok }} {{ $satuan }}</p>
                </div>
                
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-400">Harga Satuan</p>
                    <p class="text-sm font-bold text-white">Rp {{ $harga_unit }}</p>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-white/10">
                    <p class="text-base font-bold text-gray-300">Total Transaksi</p>
                    <p class="text-xl font-extrabold text-orange-400">Rp {{ $total_transaksi }}</p>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <a href="{{ route('operator.dashboard') }}" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 transition active:scale-95 shadow-lg shadow-blue-900/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Pergi ke Dashboard
                </a>
                <a href="{{ route('operator.bahan.create') }}" class="w-full bg-black/10 text-gray-400 font-bold py-3 rounded-xl flex items-center justify-center gap-2 border border-white/10 hover:bg-white/5 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Input Bahan Lain
                </a>
            </div>
            
        </div>
        
        @include('components.bottom-nav-operator')
        
    </div>
</body>
</html>