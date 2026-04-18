@php
    $page = 'laporan'; // Penanda menu aktif (Dropdown Laporan)
@endphp
<!DOCTYPE html>
<html lang="id">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Berliano Admin</title>
    <style>
        /* Print Styles */
        @media print {
            body { background: white !important; color: black !important; }
            aside, header, .no-print { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; }
            .bg-card { background: white !important; border: 1px solid #ddd !important; }
            .text-white { color: black !important; }
            .text-gray-400 { color: #555 !important; }
        }
    </style>
</head>
    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-6 md:p-8 md:ml-64 bg-black">
        
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-white/10 pb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Laporan Keuangan</h2>
                <p class="text-gray-400 text-sm mt-1">Ringkasan Pemasukan, Pengeluaran, dan Laba Bersih.</p>
            </div>
            
            <div class="flex gap-3 no-print">
                <div class="relative">
                    <input type="month" value="2025-11" class="bg-[#1A1A1A] border border-white/10 text-white text-sm rounded-lg px-4 py-2.5 focus:border-orange-500 outline-none">
                </div>
                
                <button onclick="window.print()" class="bg-[#1A1A1A] hover:bg-[#222] border border-white/10 text-white px-4 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Laporan
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-[#1A1A1A] p-6 rounded-xl border border-white/5 bg-card relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-8 bg-green-500/5 rounded-bl-full transition group-hover:bg-green-500/10"></div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Pemasukan</p>
                        <p class="text-xs text-green-500 mt-1">▲ 15% dari bulan lalu</p>
                    </div>
                    <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white">Rp 125.400.000</h3>
                <p class="text-[10px] text-gray-500 mt-2">Omzet Penjualan Produk</p>
            </div>

            <div class="bg-[#1A1A1A] p-6 rounded-xl border border-white/5 bg-card relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-8 bg-red-500/5 rounded-bl-full transition group-hover:bg-red-500/10"></div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Total Pengeluaran</p>
                        <p class="text-xs text-red-500 mt-1">▼ 2% lebih hemat</p>
                    </div>
                    <div class="p-2 bg-red-500/10 rounded-lg text-red-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white">Rp 45.200.000</h3>
                <p class="text-[10px] text-gray-500 mt-2">Bahan Baku & Operasional</p>
            </div>

            <div class="bg-[#1A1A1A] p-6 rounded-xl border border-white/5 bg-card relative overflow-hidden group">
                <div class="absolute right-0 top-0 p-8 bg-blue-500/5 rounded-bl-full transition group-hover:bg-blue-500/10"></div>
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Laba Bersih (Net Profit)</p>
                        <p class="text-xs text-blue-500 mt-1">Margin: 64%</p>
                    </div>
                    <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-blue-400">Rp 80.200.000</h3>
                <p class="text-[10px] text-gray-500 mt-2">Cash Flow Positif</p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="lg:col-span-2 bg-[#1A1A1A] p-6 rounded-xl border border-white/5 bg-card">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-white">Analisis Arus Kas (6 Bulan Terakhir)</h3>
                    <div class="flex gap-4 text-[10px]">
                        <span class="flex items-center gap-1"><div class="w-2 h-2 bg-green-500 rounded-full"></div> Pemasukan</span>
                        <span class="flex items-center gap-1"><div class="w-2 h-2 bg-red-500 rounded-full"></div> Pengeluaran</span>
                    </div>
                </div>

                <div class="h-64 flex items-end justify-between gap-4 px-2">
                    <div class="w-full flex gap-1 items-end h-full justify-center group">
                        <div class="w-3 bg-green-500/50 group-hover:bg-green-500 rounded-t transition h-[40%]"></div>
                        <div class="w-3 bg-red-500/50 group-hover:bg-red-500 rounded-t transition h-[20%]"></div>
                    </div>
                    <div class="w-full flex gap-1 items-end h-full justify-center group">
                        <div class="w-3 bg-green-500/50 group-hover:bg-green-500 rounded-t transition h-[55%]"></div>
                        <div class="w-3 bg-red-500/50 group-hover:bg-red-500 rounded-t transition h-[30%]"></div>
                    </div>
                    <div class="w-full flex gap-1 items-end h-full justify-center group">
                        <div class="w-3 bg-green-500/50 group-hover:bg-green-500 rounded-t transition h-[45%]"></div>
                        <div class="w-3 bg-red-500/50 group-hover:bg-red-500 rounded-t transition h-[25%]"></div>
                    </div>
                    <div class="w-full flex gap-1 items-end h-full justify-center group">
                        <div class="w-3 bg-green-500/50 group-hover:bg-green-500 rounded-t transition h-[70%]"></div>
                        <div class="w-3 bg-red-500/50 group-hover:bg-red-500 rounded-t transition h-[35%]"></div>
                    </div>
                    <div class="w-full flex gap-1 items-end h-full justify-center group">
                        <div class="w-3 bg-green-500/50 group-hover:bg-green-500 rounded-t transition h-[60%]"></div>
                        <div class="w-3 bg-red-500/50 group-hover:bg-red-500 rounded-t transition h-[40%]"></div>
                    </div>
                    <div class="w-full flex gap-1 items-end h-full justify-center group">
                        <div class="w-3 bg-green-500 rounded-t h-[85%] relative">
                            <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-[9px] text-green-400">125jt</span>
                        </div>
                        <div class="w-3 bg-red-500 rounded-t h-[45%] relative">
                            <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-[9px] text-red-400">45jt</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between mt-2 text-[10px] text-gray-500 font-mono text-center">
                    <span class="w-full">Jun</span><span class="w-full">Jul</span><span class="w-full">Agu</span>
                    <span class="w-full">Sep</span><span class="w-full">Okt</span><span class="w-full">Nov</span>
                </div>
            </div>

            <div class="bg-[#1A1A1A] p-6 rounded-xl border border-white/5 bg-card">
                <h3 class="font-bold text-white mb-4">Rincian Pengeluaran</h3>
                <div class="space-y-4">
                    
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">Belanja Bahan Baku</span>
                            <span class="font-bold text-white">Rp 21.850.000</span>
                        </div>
                        <div class="w-full bg-black h-2 rounded-full overflow-hidden">
                            <div class="bg-orange-500 h-full" style="width: 48%"></div>
                        </div>
                        <p class="text-[9px] text-gray-500 mt-1">48% dari total pengeluaran</p>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">Gaji Operator</span>
                            <span class="font-bold text-white">Rp 15.000.000</span>
                        </div>
                        <div class="w-full bg-black h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full" style="width: 33%"></div>
                        </div>
                        <p class="text-[9px] text-gray-500 mt-1">33% dari total pengeluaran</p>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">Operasional & Listrik</span>
                            <span class="font-bold text-white">Rp 8.350.000</span>
                        </div>
                        <div class="w-full bg-black h-2 rounded-full overflow-hidden">
                            <div class="bg-purple-500 h-full" style="width: 19%"></div>
                        </div>
                        <p class="text-[9px] text-gray-500 mt-1">19% dari total pengeluaran</p>
                    </div>

                </div>
                
                <div class="mt-6 pt-4 border-t border-white/5">
                    <button class="w-full py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold text-gray-300 transition">
                        Unduh Detail PDF
                    </button>
                </div>
            </div>

        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden bg-card">
            <div class="p-5 border-b border-white/5 flex justify-between items-center">
                <h3 class="font-bold text-white">Riwayat Transaksi Terbaru</h3>
                <a href="#" class="text-xs text-orange-500 hover:text-orange-400 no-print">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-black text-gray-500 uppercase font-bold">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Keterangan</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4 text-right">Jumlah</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-gray-300">
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 font-mono text-gray-500">24 Nov 2025</td>
                            <td class="p-4 font-bold text-white">Penjualan Partai Besar (50 Tas)</td>
                            <td class="p-4 text-green-400">Pemasukan</td>
                            <td class="p-4 text-right font-bold text-white">+ Rp 32.500.000</td>
                            <td class="p-4 text-center"><span class="bg-green-500/10 text-green-500 px-2 py-1 rounded text-[10px] font-bold">Lunas</span></td>
                        </tr>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 font-mono text-gray-500">23 Nov 2025</td>
                            <td class="p-4 font-bold text-white">Belanja Kulit Sapi (CV. Jaya)</td>
                            <td class="p-4 text-red-400">Pengeluaran</td>
                            <td class="p-4 text-right font-bold text-white">- Rp 10.000.000</td>
                            <td class="p-4 text-center"><span class="bg-blue-500/10 text-blue-500 px-2 py-1 rounded text-[10px] font-bold">Paid</span></td>
                        </tr>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 font-mono text-gray-500">22 Nov 2025</td>
                            <td class="p-4 font-bold text-white">Token Listrik Produksi</td>
                            <td class="p-4 text-red-400">Operasional</td>
                            <td class="p-4 text-right font-bold text-white">- Rp 1.500.000</td>
                            <td class="p-4 text-center"><span class="bg-blue-500/10 text-blue-500 px-2 py-1 rounded text-[10px] font-bold">Paid</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>