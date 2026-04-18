<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengadaan - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50">
            <a href="{{ route('operator.tugas.pengadaan') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Status Pengadaan</h1>
            <span class="text-xs text-gray-400">#REQ-2024-088</span>
        </header>

        <div class="p-6">
            <div class="bg-blue-600/20 border border-blue-500/50 p-4 rounded-xl mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m5.632 6.368a8.001 8.001 0 0010.582-1.474M13 13v5h.01"/></svg>
                    <div>
                        <p class="font-bold text-blue-500">Tugas Telah Diterima!</p>
                        <p class="text-xs text-gray-300">Anda bertanggung jawab memproses permintaan ini.</p>
                    </div>
                </div>
            </div>

            <h3 class="text-sm text-gray-400 font-bold mb-4 uppercase">Status Proses</h3>
            
            <div class="bg-[#1A1A1A] p-5 rounded-2xl border border-white/5 space-y-4">
                
                <div class="flex items-center gap-4">
                    <div class="w-6 h-6 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">Permintaan Dibuat</p>
                        <p class="text-[10px] text-gray-500">Oleh Admin Zulkifli, 2 jam lalu.</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4 border-l-2 border-dashed border-blue-500 pl-4 py-2 relative -ml-3">
                    <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0 absolute left-[-13px] top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m5.632 6.368a8.001 8.001 0 0010.582-1.474M13 13v5h.01"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">Diterima Operator</p>
                        <p class="text-[10px] text-blue-400">Anda mengkonfirmasi tugas sekarang.</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-l-2 border-dashed border-gray-700 pl-4 py-2 relative -ml-3 opacity-50">
                    <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-white flex-shrink-0 absolute left-[-13px] top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-500 text-sm">Proses Pembelian</p>
                        <p class="text-[10px] text-gray-600">Menunggu penerbitan PO (Purchase Order).</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-l-2 border-dashed border-gray-700 pl-4 py-2 relative -ml-3 opacity-50">
                    <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-white flex-shrink-0 absolute left-[-13px] top-1/2 -translate-y-1/2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-500 text-sm">Diterima Gudang</p>
                        <p class="text-[10px] text-gray-600">Selesai.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                 <h3 class="text-sm text-gray-400 font-bold mb-3 uppercase">Detail Item Pesanan</h3>
                 <p class="text-xs text-gray-500">Lihat item yang harus dibeli di Tugas Pengadaan.</p>
                 <a href="{{ route('operator.tugas.pengadaan') }}" class="text-blue-500 text-xs mt-2 underline block">Lihat Daftar Belanja</a>
            </div>

        </div>

    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-black p-4 max-w-md mx-auto z-50">
        <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
            Update Status ke Pembelian PO
        </button>
    </div>

    @include('components.bottom-nav-operator')

</body>
</html>