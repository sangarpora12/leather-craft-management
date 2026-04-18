<?php
$page = 'pengadaan-material'; // Menu aktif
@include('admin.header')

?>

<main class="flex-1 flex flex-col relative overflow-hidden bg-black">

    <header class="h-20 bg-[#111111] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0">
        <div>
            <h2 class="text-xl font-bold text-white">PT. Kulit Jaya Abadi</h2>
            <p class="text-xs text-gray-500 mt-1">Supplier Utama: Kulit Sapi & Domba</p>
        </div>
        <button class="bg-[#202020] hover:bg-white hover:text-black text-gray-300 text-xs font-bold px-4 py-2.5 rounded-lg transition border border-white/10">
            Edit Data Supplier
        </button>
    </header>

    <div class="flex-1 overflow-y-auto p-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-4">Informasi Kontak</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded bg-[#252525] flex items-center justify-center"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500">Kontak Person</p>
                            <p class="text-white font-medium">Bpk. Santoso (Sales)</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded bg-[#252525] flex items-center justify-center"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500">Telepon / WA</p>
                            <p class="text-white font-medium">0812-3456-7890</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded bg-[#252525] flex items-center justify-center"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500">Email</p>
                            <p class="text-white font-medium">sales@kulitjaya.com</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded bg-[#252525] flex items-center justify-center"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg></div>
                        <div>
                            <p class="text-[10px] text-gray-500">Alamat</p>
                            <p class="text-white font-medium">Jl. Magelang KM 5, Sleman, Yogyakarta</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                        <p class="text-[10px] text-gray-500 font-bold uppercase mb-2">Total Transaksi</p>
                        <h3 class="text-3xl font-bold text-white">24<span class="text-lg text-gray-500 ml-1">PO</span></h3>
                    </div>
                    <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                        <p class="text-[10px] text-gray-500 font-bold uppercase mb-2">Total Pembelian</p>
                        <h3 class="text-3xl font-bold text-green-500">Rp 128.5 Jt</h3>
                    </div>
                    <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                        <p class="text-[10px] text-gray-500 font-bold uppercase mb-2">Avg. Harga Beli</p>
                        <h3 class="text-3xl font-bold text-blue-500">Rp 5.3 Jt</h3>
                        <p class="text-[9px] text-gray-500 mt-1">Per Transaksi</p>
                    </div>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 flex items-center gap-4">
                    <p class="text-xs text-gray-500 font-bold uppercase">Produk Supply:</p>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-[#252525] rounded-full text-xs text-gray-300 border border-white/5">Kulit Sapi (Pull-up)</span>
                        <span class="px-3 py-1 bg-[#252525] rounded-full text-xs text-gray-300 border border-white/5">Kulit Domba (Nappa)</span>
                        <span class="px-3 py-1 bg-[#252525] rounded-full text-xs text-gray-300 border border-white/5">Kulit Sapi (Nabati)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-white text-sm">Riwayat Pembelian Material</h3>
                <a href="#" class="text-[10px] text-gray-500 hover:text-white bg-[#202020] px-3 py-1.5 rounded transition">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto rounded-xl border border-white/5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#1A1A1A] text-gray-500 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">No. Invoice/PO</th>
                            <th class="p-4">Item Material</th>
                            <th class="p-4 text-center">Qty</th>
                            <th class="p-4 text-right">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#0F0F0F]">
                        <tr class="hover:bg-[#1A1A1A] transition">
                            <td class="p-4 text-gray-500">20 Nov 2023</td>
                            <td class="p-4 text-orange-500 font-mono">PO-SUP-105</td>
                            <td class="p-4 text-white font-bold">Kulit Sapi Pull-Up Grade A</td>
                            <td class="p-4 text-center"><span class="bg-[#202020] px-2 py-1 rounded text-gray-300">300 sq ft</span></td>
                            <td class="p-4 text-right text-green-500 font-bold">Rp 25.500.000</td>
                        </tr>
                        <tr class="hover:bg-[#1A1A1A] transition">
                            <td class="p-4 text-gray-500">10 Okt 2023</td>
                            <td class="p-4 text-orange-500 font-mono">PO-SUP-098</td>
                            <td class="p-4 text-white font-bold">Kulit Domba Nappa</td>
                            <td class="p-4 text-center"><span class="bg-[#202020] px-2 py-1 rounded text-gray-300">150 sq ft</span></td>
                            <td class="p-4 text-right text-green-500 font-bold">Rp 12.750.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>
</body>

</html>