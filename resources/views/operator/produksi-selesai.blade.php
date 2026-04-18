<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesai Produksi - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50 border-b border-white/5">
            <a href="{{ route('operator.tugas.detail') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Status Produksi</h1>
            <span class="text-gray-400 text-xs">SPK #0123</span>
        </header>

        <div class="p-6">

             <div class="bg-green-600/20 border border-green-500/50 p-4 rounded-xl mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-bold text-green-500">Produksi Selesai!</p>
                        <p class="text-xs text-gray-300">Semua tahapan telah dikerjakan. Silakan buat laporan akhir.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#1A1A1A] p-4 rounded-2xl border border-white/5 relative mb-6">
                <h4 class="text-sm font-bold text-white mb-1">Sling Bag Premium</h4>
                <p class="text-[10px] text-gray-500 font-mono mb-3">Kode: PRO-T-001</p>
                <div class="flex justify-between items-center text-xs text-gray-400 border-t border-white/10 pt-3">
                    <p class="font-bold text-white">20/20 Pcs Selesai</p>
                    <div class="w-16 h-1 bg-green-500 rounded-full"></div>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                <h4 class="text-sm font-bold text-gray-400 uppercase">Tahapan Produksi</h4>
                
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Persiapan Bahan</p>
                        <p class="text-xs text-gray-500">Selesai: 08:30</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Proses Penjahitan</p>
                        <p class="text-xs text-gray-500">Selesai: 12:45</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Finishing & QC</p>
                        <p class="text-xs text-gray-500">Selesai: 15:30</p>
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <a href="#" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-4 rounded-xl flex justify-center transition active:scale-95 shadow-xl shadow-green-900/20">
                    Buat Laporan Produksi
                </a>
            </div>
        </div>
        
        @include('components.bottom-nav-operator')

    </div>
</body>
</html>