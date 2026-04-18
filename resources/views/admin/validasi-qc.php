<?php
$page = 'monitoring'; // Menu aktif
@include('admin.header')

?>

<main class="flex-1 flex flex-col relative overflow-hidden bg-black items-center justify-center">

    <div class="bg-[#151515] border border-white/10 rounded-2xl w-full max-w-4xl shadow-2xl overflow-hidden flex">

        <div class="w-5/12 bg-[#202020] relative">
            <div class="absolute inset-0 flex items-center justify-center opacity-10">
                <svg class="w-32 h-32 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black to-transparent z-10"></div>
            <div class="absolute bottom-6 left-6 z-20">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bukti Fisik</p>
                <h2 class="text-2xl font-bold text-white mb-2">Rome Bag – Tan</h2>
                <div class="flex items-center gap-2 text-[10px] text-gray-400">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Diambil pada 24 Nov 2025, 14:35 WIB
                </div>
            </div>
        </div>

        <div class="w-7/12 p-8 relative">
            <a href="monitoring-produksi.php" class="absolute top-4 right-4 text-gray-500 hover:text-white"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg></a>

            <div class="flex justify-between items-start mb-8">
                <div>
                    <h3 class="text-lg font-bold text-white">Detail Penyelesaian</h3>
                    <p class="text-xs text-gray-500 font-mono mt-1">ID Laporan: LOG-2025-11-24-001</p>
                </div>
                <div class="bg-green-500/10 border border-green-500/20 px-3 py-1 rounded-full flex items-center gap-2">
                    <svg class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] font-bold text-green-500">Terverifikasi</span>
                </div>
            </div>

            <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 flex items-center gap-4 mb-6">
                <div class="w-10 h-10 rounded-full bg-[#252525] flex items-center justify-center text-sm font-bold text-white">Z</div>
                <div>
                    <p class="text-[10px] text-gray-500">Penanggung Jawab</p>
                    <h4 class="text-sm font-bold text-white">Zulkifli Syahputra</h4>
                    <p class="text-[10px] text-gray-400">Operator Produksi (Shift Pagi)</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] text-gray-500 mb-1">Target SPK</p>
                    <h4 class="text-xl font-bold text-white">12 <span class="text-sm text-gray-500">Pcs</span></h4>
                </div>
                <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] text-gray-500 mb-1">Hasil Selesai</p>
                    <h4 class="text-xl font-bold text-green-500">12 <span class="text-sm text-green-500/50">Pcs</span></h4>
                </div>
            </div>

            <div class="bg-[#1A1A1A] border border-white/5 rounded-xl p-4 mb-8">
                <p class="text-[10px] text-gray-500 mb-2 font-bold uppercase">Catatan Operator</p>
                <p class="text-xs text-gray-300 italic leading-relaxed">
                    "Produksi berjalan lancar. Kualitas kulit batch ini sangat bagus, tidak ada cacat permukaan. Finishing edge paint sudah kering sempurna."
                </p>
            </div>

            <div class="flex gap-4">
                <button class="flex-1 py-3 border border-white/10 rounded-xl text-xs font-bold text-white hover:bg-white/5 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Label
                </button>
                <button class="flex-1 py-3 bg-orange-600 hover:bg-orange-700 rounded-xl text-xs font-bold text-white shadow-lg shadow-orange-900/20 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Validasi QC
                </button>
            </div>

        </div>

    </div>
</main>
</body>

</html>