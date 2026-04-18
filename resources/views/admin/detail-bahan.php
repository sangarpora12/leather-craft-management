<?php
$page = 'data-bahan'; // Supaya menu 'Data Bahan Baku' tetap aktif
@include('admin.header')

?>

<main class="flex-1 flex flex-col relative overflow-hidden bg-black">

    <header class="h-20 bg-[#111111] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0">
        <div>
            <a href="data-bahan.php" class="text-xs text-gray-500 hover:text-white flex items-center gap-1 mb-1 transition">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar
            </a>
            <h2 class="text-xl font-bold text-white">Detail Bahan Baku: Kulit Sapi Pull-Up</h2>
        </div>
        <button class="bg-red-500/10 border border-red-500/50 hover:bg-red-500 hover:text-white text-red-500 text-xs font-bold px-4 py-2.5 rounded-lg transition">
            Hapus Bahan
        </button>
    </header>

    <div class="flex-1 overflow-y-auto p-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div class="lg:col-span-2 bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                <h3 class="text-sm font-bold text-gray-400 mb-6 pb-2 border-b border-white/5">Informasi Dasar</h3>

                <div class="grid grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Nama Material</p>
                        <p class="text-sm text-white font-medium">Kulit Sapi Pull-Up (Coklat)</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Kode Bahan (SKU)</p>
                        <p class="text-sm text-orange-500 font-mono font-bold">BAH-K-001</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Kategori</p>
                        <p class="text-sm text-white font-medium">Kulit Asli</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Satuan Utama (UoM)</p>
                        <p class="text-sm text-white font-medium">Square Feet (sq ft)</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Harga Beli Terakhir</p>
                        <p class="text-sm text-white font-medium">Rp 85.000 / sq ft</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Batas Stok Kritis</p>
                        <p class="text-sm text-red-500 font-bold">Min. 50 sq ft</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#111827] border border-blue-900/30 rounded-2xl p-6 flex flex-col justify-center items-center text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-teal-400"></div>

                <h3 class="text-xs font-bold text-gray-400 mb-4 w-full text-left border-b border-white/5 pb-2">Status Stok Saat Ini</h3>

                <div class="my-auto">
                    <h2 class="text-6xl font-bold text-teal-400 tracking-tight">120</h2>
                    <p class="text-sm text-gray-400 mt-1">Square Feet</p>
                    <span class="inline-block mt-4 px-3 py-1 bg-teal-500/20 text-teal-400 text-xs font-bold rounded-full border border-teal-500/30">Stok Aman</span>
                </div>

                <div class="mt-6 w-full pt-4 border-t border-white/5">
                    <p class="text-[10px] text-gray-500">Supplier Default</p>
                    <p class="text-sm font-bold text-orange-500">PT. Kulit Jaya Abadi</p>
                </div>
            </div>
        </div>

        <div class="bg-[#151515] border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-sm font-bold text-white">Riwayat Mutasi (Spesifik Item Ini)</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#1A1A1A] text-gray-500 text-[10px] uppercase tracking-wider">
                            <th class="px-6 py-3 font-semibold">Tanggal & Waktu</th>
                            <th class="px-6 py-3 font-semibold">Tipe Transaksi</th>
                            <th class="px-6 py-3 font-semibold">Jumlah</th>
                            <th class="px-6 py-3 font-semibold">Sisa Stok</th>
                            <th class="px-6 py-3 font-semibold">Sumber Referensi</th>
                            <th class="px-6 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-white/5">

                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-6 py-4 text-gray-300">
                                22 Nov 2023,<br><span class="text-[10px] text-gray-500">09:15</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-red-500/10 text-red-500 border border-red-500/20 px-2 py-1 rounded text-[10px] font-bold">Keluar (Produksi)</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-red-500">- 225 sq ft</td>
                            <td class="px-6 py-4 font-bold text-white">120 sq ft</td>
                            <td class="px-6 py-4 text-blue-400 hover:underline cursor-pointer">SPK-009</td>
                            <td class="px-6 py-4 text-gray-400">Pemakaian untuk produksi 50 pcs Rome Bag.</td>
                        </tr>

                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-6 py-4 text-gray-300">
                                15 Nov 2023,<br><span class="text-[10px] text-gray-500">10:00</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-teal-500/10 text-teal-500 border border-teal-500/20 px-2 py-1 rounded text-[10px] font-bold">Masuk (Beli)</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-teal-500">+ 300 sq ft</td>
                            <td class="px-6 py-4 font-bold text-white">345 sq ft</td>
                            <td class="px-6 py-4 text-blue-400 hover:underline cursor-pointer">PO-SUP-101</td>
                            <td class="px-6 py-4 text-gray-400">Restock dari PT. Kulit Jaya Abadi.</td>
                        </tr>

                        <tr class="hover:bg-white/[0.02]">
                            <td class="px-6 py-4 text-gray-300">
                                10 Nov 2023,<br><span class="text-[10px] text-gray-500">14:20</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-red-900/20 text-red-300 border border-red-500/10 px-2 py-1 rounded text-[10px] font-bold">Keluar (Sample)</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-red-300">- 5 sq ft</td>
                            <td class="px-6 py-4 font-bold text-white">45 sq ft</td>
                            <td class="px-6 py-4 text-blue-400 hover:underline cursor-pointer">ADJ-002</td>
                            <td class="px-6 py-4 text-gray-400">Pembuatan sample produk baru.</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>
</body>

</html>