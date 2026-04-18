<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Stok - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">

    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-green-500/20 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-[0_0_30px_rgba(34,197,94,0.4)] animate-bounce">
                <svg class="w-10 h-10 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Stok Bahan Berhasil Ditambahkan!</h1>
            <p class="text-gray-400 text-sm mt-2">Data sudah tercatat di sistem inventaris.</p>
        </div>

        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-white mb-6 border-b border-white/10 pb-4">Ringkasan Item</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Kode Material Baru</span>
                    <span class="text-orange-500 font-mono font-bold text-lg">
                        {{ $kode ?? 'ERROR' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Nama Bahan</span>
                    <span class="text-white font-medium text-right max-w-[60%] truncate">
                        {{ $nama ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Stok Masuk</span>
                    <span class="text-white font-bold">
                        {{ $stok ?? 0 }} {{ $satuan ?? '' }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Harga Satuan</span>
                    <span class="text-white">
                        Rp {{ number_format($harga ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                <div class="border-t border-dashed border-white/20 my-2"></div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-bold">Total Transaksi</span>
                    <span class="text-green-400 font-extrabold text-xl">
                        Rp {{ number_format($total ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="mt-8 space-y-3">
            <a href="{{ route('operator.dashboard') }}" class="block w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl text-center transition shadow-lg shadow-blue-900/30">
                🏠 Pergi ke Dashboard
            </a>
            <a href="{{ route('operator.scan.manual') }}" class="block w-full bg-[#2A2A2A] hover:bg-[#333] text-gray-300 font-semibold py-4 rounded-xl text-center border border-white/5 transition">
                + Input Stok Lainnya
            </a>
        </div>

    </div>

</body>
</html>