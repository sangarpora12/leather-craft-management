<?php
$page = 'produk-jadi'; // Menu tetap aktif
@include('admin.header')

?>

<main class="flex-1 flex flex-col relative overflow-hidden bg-black">

    <header class="h-20 bg-[#111111] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0">
        <div class="flex flex-col">
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="produk-jadi.php" class="hover:text-white transition">Produk Jadi</a>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-white">Detail Produk</span>
            </div>
        </div>
        <div class="flex gap-3">
            <button class="bg-[#202020] hover:bg-white hover:text-black text-gray-300 text-xs font-bold px-4 py-2.5 rounded-lg transition border border-white/10 flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Data
            </button>
            <button class="bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat SPK Produksi
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div class="lg:col-span-2 bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                <div class="flex gap-6">
                    <div class="w-48 h-48 bg-[#202020] rounded-xl flex-shrink-0 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-white mb-1">Rome Bag – Dark Brown</h1>
                                <p class="text-sm text-gray-500 mb-4">SKU: PRO-T-001 • Kategori: Tas Wanita</p>
                            </div>
                            <span class="bg-red-500/10 border border-red-500/20 text-red-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Stok Kritis</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-3 bg-[#1A1A1A] rounded-lg border border-white/5">
                                <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Harga Jual</p>
                                <p class="text-lg font-bold text-white">Rp 650.000</p>
                            </div>
                            <div class="p-3 bg-[#1A1A1A] rounded-lg border border-white/5">
                                <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Estimasi HPP</p>
                                <p class="text-lg font-bold text-teal-500">Rp 450.000</p>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 leading-relaxed">
                            Tas selempang kulit sapi asli dengan finishing pull-up premium. Menggunakan aksesoris gold YKK dan lining suede lembut. Produk best-seller 2024.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/5">
                    <div class="p-2 bg-gray-800 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-sm">Status Inventaris</h3>
                </div>

                <div class="flex justify-between items-end mb-2">
                    <span class="text-xs text-gray-500">Stok Saat Ini</span>
                    <span class="text-3xl font-bold text-red-500">3 Pcs</span>
                </div>

                <div class="w-full h-2 bg-gray-800 rounded-full mb-1">
                    <div class="h-full bg-red-500 rounded-full" style="width: 15%"></div>
                </div>
                <div class="flex justify-between text-[9px] text-gray-500 mb-6">
                    <span>0</span>
                    <span>Target Min: 20 Pcs</span>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-[#1A1A1A] rounded-lg">
                        <span class="text-xs text-gray-400 flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div> Sedang Diproduksi
                        </span>
                        <span class="text-sm font-bold text-white">12 Pcs</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-[#1A1A1A] rounded-lg">
                        <span class="text-xs text-gray-400 flex items-center gap-2">
                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div> Terjual Bulan Ini
                        </span>
                        <span class="text-sm font-bold text-white">45 Pcs</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-white">Bill of Materials (Resep)</h3>
                    <span class="text-[10px] bg-gray-800 px-2 py-1 rounded text-gray-400">Per 1 Unit Produk</span>
                </div>

                <div class="overflow-hidden rounded-xl border border-white/5">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#1A1A1A] text-gray-500 uppercase font-bold text-[9px]">
                            <tr>
                                <th class="p-3 pl-4">Nama Bahan</th>
                                <th class="p-3">Kebutuhan</th>
                                <th class="p-3">Stok Gudang</th>
                                <th class="p-3 text-right pr-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-3 pl-4 font-medium text-white">Kulit Sapi Full Grain</td>
                                <td class="p-3 text-gray-400">2.5 Feet</td>
                                <td class="p-3 text-white">120 Feet</td>
                                <td class="p-3 text-right pr-4 text-green-500 font-bold">Ready</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-3 pl-4 font-medium text-white">Resleting YKK Gold (30cm)</td>
                                <td class="p-3 text-gray-400">1 Pcs</td>
                                <td class="p-3 text-white">200 Pcs</td>
                                <td class="p-3 text-right pr-4 text-green-500 font-bold">Ready</td>
                            </tr>
                            <tr class="bg-red-900/10 hover:bg-red-900/20">
                                <td class="p-3 pl-4 font-medium text-white">Benang Nilon Waxed</td>
                                <td class="p-3 text-gray-400">10 Meter</td>
                                <td class="p-3 text-red-400 font-bold">2 Roll</td>
                                <td class="p-3 text-right pr-4 text-red-500 font-bold">Low</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-3 pl-4 font-medium text-white">Kain Suede (Lining)</td>
                                <td class="p-3 text-gray-400">0.5 Meter</td>
                                <td class="p-3 text-white">45 Meter</td>
                                <td class="p-3 text-right pr-4 text-green-500 font-bold">Ready</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-3 bg-red-900/20 border border-red-500/20 rounded-lg flex gap-3 items-start">
                    <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h4 class="text-xs font-bold text-red-400">Perhatian: Stok Benang Menipis</h4>
                        <p class="text-[10px] text-red-300/70 mt-1">Produksi massal mungkin terhambat jika tidak segera restock benang.</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-white">Riwayat Produksi (SPK)</h3>
                    <a href="#" class="text-[10px] text-orange-500 hover:text-white transition">Lihat Semua</a>
                </div>

                <div class="space-y-4 relative border-l border-gray-800 ml-2">
                    <div class="ml-6 relative bg-[#1A1A1A] p-4 rounded-xl border border-white/5">
                        <div class="absolute -left-[31px] top-4 w-3 h-3 bg-blue-500 rounded-full border-4 border-[#151515]"></div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-white">SPK #0125</span>
                            <span class="text-[9px] bg-blue-500/10 text-blue-400 px-2 py-0.5 rounded border border-blue-500/20">Proses</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mb-2">Sedang dikerjakan oleh <span class="text-white font-semibold">Zulkifli</span>.</p>
                        <div class="flex justify-between text-[10px] text-gray-500 border-t border-white/5 pt-2">
                            <span>Target: 12 Pcs</span>
                            <span>Deadline: Besok</span>
                        </div>
                    </div>

                    <div class="ml-6 relative bg-[#1A1A1A] p-4 rounded-xl border border-white/5 opacity-70">
                        <div class="absolute -left-[31px] top-4 w-3 h-3 bg-green-500 rounded-full border-4 border-[#151515]"></div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-white">SPK #0110</span>
                            <span class="text-[9px] bg-green-500/10 text-green-400 px-2 py-0.5 rounded border border-green-500/20">Selesai</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mb-2">Diselesaikan oleh <span class="text-white font-semibold">Budi Santoso</span>.</p>
                        <div class="flex justify-between text-[10px] text-gray-500 border-t border-white/5 pt-2">
                            <span>Output: 20 Pcs</span>
                            <span>20 Okt 2025</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>
</body>

</html>