<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siap Produksi - Operator</title>
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
            <h1 class="text-lg font-bold">Siap Produksi</h1>
            <span class="text-gray-400 text-xs">SPK #0123</span>
        </header>

        <div class="p-6">
            
            <div class="bg-[#1A1A1A] p-4 rounded-2xl border border-white/5 relative mb-6">
                <h4 class="text-sm font-bold text-white mb-1">Sling Bag Premium</h4>
                <p class="text-[10px] text-gray-500 font-mono mb-3">Kode: PRO-T-001 • Maroon</p>
                <div class="grid grid-cols-2 text-xs text-gray-400">
                    <div>
                        <p>Target</p>
                        <p class="font-bold text-white">20 Pcs</p>
                    </div>
                    <div class="text-right">
                        <p>Deadline</p>
                        <p class="font-bold text-red-500">16:00 WIB</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <h4 class="text-sm font-bold text-gray-400 uppercase">Tahapan Pekerjaan</h4>

                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Mulai Pekerjaan</p>
                        <p class="text-xs text-gray-500">Klik tombol di bawah untuk memulai sesi.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 opacity-50">
                    <div class="w-8 h-8 rounded-full bg-[#1A1A1A] border border-white/10 flex items-center justify-center text-gray-500 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m5.632 6.368a8.001 8.001 0 0010.582-1.474M13 13v5h.01"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-500">Proses Pengerjaan</p>
                        <p class="text-xs text-gray-600">Menunggu inisiasi.</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-4 opacity-50">
                    <div class="w-8 h-8 rounded-full bg-[#1A1A1A] border border-white/10 flex items-center justify-center text-gray-500 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-500">Selesaikan Tugas</p>
                        <p class="text-xs text-gray-600">Finalisasi hasil produksi.</p>
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <p class="text-center text-gray-500 text-xs mb-3">Pastikan alokasi bahan sudah memulai sebelum memulai.</p>
                <a href="{{ route('operator.produksi.proses') }}" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-xl flex justify-center transition active:scale-95 shadow-xl shadow-orange-900/20">
                    Mulai Produksi
                </a>
                <button class="w-full bg-black/10 text-gray-500 font-bold py-2 mt-2 rounded-xl text-xs hover:bg-black/20">
                    Lapor Masalah
                </button>
            </div>
        </div>
        
        @include('components.bottom-nav-operator')

    </div>
</body>
</html>