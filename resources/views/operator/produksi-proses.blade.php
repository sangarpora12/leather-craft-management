<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Produksi - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .step-progress { display: flex; justify-content: space-between; align-items: center; position: relative; margin-top: 20px; }
        .step-progress::before { content: ''; position: absolute; width: 100%; height: 4px; background: #2A2A2A; top: 50%; transform: translateY(-50%); z-index: 1; }
        .step-progress .progress-line { position: absolute; height: 4px; background: #00C853; top: 50%; transform: translateY(-50%); z-index: 2; width: 50%; /* Simulasi progress */ }
        .step-progress .step { width: 24px; height: 24px; background: #2A2A2A; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; z-index: 3; font-size: 10px; border: 2px solid #000; }
        .step-progress .step.done { background: #00C853; }
        .step-progress .step.active { background: #FF9800; }
    </style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50 border-b border-white/5">
            <a href="{{ route('operator.tugas.detail') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Proses Produksi</h1>
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

            <div class="px-2">
                <div class="step-progress relative">
                    <div class="progress-line" style="width: 50%;"></div>
                    <div class="step done">✓</div>
                    <div class="step active">2</div>
                    <div class="step">3</div>
                </div>
                <div class="flex justify-between text-[10px] text-gray-500 mt-2">
                    <span>Mulai</span>
                    <span>Proses</span>
                    <span>Selesai</span>
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
                        <p class="text-xs text-gray-500">Cek kelengkapan kulit & aksesoris. Selesai: 08:30 WIB</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m5.632 6.368a8.001 8.001 0 0010.582-1.474M13 13v5h.01"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white">Proses Penjahitan</p>
                        <p class="text-xs text-gray-500">Gabungkan pola utama dengan lining di dalam.</p>
                        <span class="bg-blue-600/20 text-blue-500 text-[10px] font-bold px-2 py-0.5 rounded mt-1 inline-block">SEDANG BERJALAN</span>
                    </div>
                </div>

                <div class="flex items-start gap-4 opacity-50">
                    <div class="w-8 h-8 rounded-full bg-[#1A1A1A] border border-white/10 flex items-center justify-center text-gray-500 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-500">Finishing & QC</p>
                        <p class="text-xs text-gray-600">Bersihkan sisa benang & lem.</p>
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <a href="{{ route('operator.produksi.selesai') }}" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl flex justify-center transition active:scale-95 shadow-xl shadow-blue-900/20">
                    Tandai Selesai & Lapor →
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