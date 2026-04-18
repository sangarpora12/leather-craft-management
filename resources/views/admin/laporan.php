@php
    // Ganti ini sesuai halaman biar sidebar aktif
    // Opsinya: 'laporan' (default untuk grup laporan)
    $page = 'laporan'; 
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Admin - Berliano Leather</title> 
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        
        /* Print Styles: Agar saat diprint backgroundnya putih & sidebar hilang */
        @media print {
            body { background-color: white !important; color: black !important; }
            aside, .no-print { display: none !important; }
            main { margin-left: 0 !important; padding: 0 !important; }
            .bg-dark { background-color: white !important; border: 1px solid #ddd !important; }
            .text-light { color: black !important; }
        }
    </style>
</head>
<body class="bg-black text-white flex h-screen overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-6 md:p-8 md:ml-64 bg-black">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-white/10 pb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Laporan & Analisis</h2>
                <p class="text-gray-400 text-sm mt-1">Rekapitulasi data periode bulan ini.</p>
            </div>
            
            <div class="flex gap-3 no-print">
                <div class="flex items-center gap-2 bg-[#1A1A1A] border border-white/10 px-3 py-2 rounded-lg">
                    <span class="text-gray-500 text-xs">Periode:</span>
                    <select class="bg-transparent text-sm font-bold text-white outline-none cursor-pointer">
                        <option class="bg-black">Oktober 2025</option>
                        <option class="bg-black">September 2025</option>
                    </select>
                </div>

                <button onclick="window.print()" class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak / PDF
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-[#1A1A1A] p-5 rounded-xl border border-white/5 bg-dark">
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-white text-light">1,250 Item</h3>
            </div>
            <div class="bg-[#1A1A1A] p-5 rounded-xl border border-white/5 bg-dark">
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Total Nilai (Estimasi)</p>
                <h3 class="text-2xl font-bold text-green-500">Rp 150.000.000</h3>
            </div>
            <div class="bg-[#1A1A1A] p-5 rounded-xl border border-white/5 bg-dark">
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Status Kinerja</p>
                <h3 class="text-2xl font-bold text-blue-500">Optimal (98%)</h3>
            </div>
        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 overflow-hidden bg-dark">
            <div class="p-5 border-b border-white/10 flex justify-between items-center">
                <h3 class="font-bold text-white text-light">Detail Laporan</h3>
                <button class="text-xs text-orange-500 font-bold hover:underline no-print">Lihat Selengkapnya</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-light">
                    <thead class="bg-black text-gray-500 text-[10px] uppercase font-bold bg-dark">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Keterangan / Aktivitas</th>
                            <th class="p-4">PIC / Operator</th>
                            <th class="p-4">Jumlah</th>
                            <th class="p-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-gray-300">
                        
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 text-white font-mono text-xs text-light">10 Okt 2025</td>
                            <td class="p-4">
                                <div class="font-bold text-white text-light">Produksi Batch #202</div>
                                <div class="text-[10px] text-gray-500">Rome Bag - Coklat</div>
                            </td>
                            <td class="p-4"><span class="bg-[#222] px-2 py-1 rounded text-xs text-gray-300">Zulkifli</span></td>
                            <td class="p-4 font-bold text-white text-light">50 Pcs</td>
                            <td class="p-4 text-right">
                                <span class="text-[10px] bg-green-500/10 text-green-500 border border-green-500/20 px-2 py-1 rounded font-bold">Selesai</span>
                            </td>
                        </tr>

                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 text-white font-mono text-xs text-light">09 Okt 2025</td>
                            <td class="p-4">
                                <div class="font-bold text-white text-light">Bahan Masuk (Gudang)</div>
                                <div class="text-[10px] text-gray-500">Kulit Sapi Full Grain</div>
                            </td>
                            <td class="p-4"><span class="bg-[#222] px-2 py-1 rounded text-xs text-gray-300">Admin Gudang</span></td>
                            <td class="p-4 font-bold text-white text-light">120 Feet</td>
                            <td class="p-4 text-right">
                                <span class="text-[10px] bg-blue-500/10 text-blue-500 border border-blue-500/20 px-2 py-1 rounded font-bold">Diterima</span>
                            </td>
                        </tr>

                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 text-white font-mono text-xs text-light">08 Okt 2025</td>
                            <td class="p-4">
                                <div class="font-bold text-white text-light">Pengeluaran - Reject</div>
                                <div class="text-[10px] text-gray-500">Milano Wallet (Cacat Jahit)</div>
                            </td>
                            <td class="p-4"><span class="bg-[#222] px-2 py-1 rounded text-xs text-gray-300">QC Team</span></td>
                            <td class="p-4 font-bold text-red-500">5 Pcs</td>
                            <td class="p-4 text-right">
                                <span class="text-[10px] bg-red-500/10 text-red-500 border border-red-500/20 px-2 py-1 rounded font-bold">Dibuang</span>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-white/10 text-center text-xs text-gray-500 bg-[#151515] bg-dark">
                Menampilkan 3 data terbaru dari total 1,250 data.
            </div>
        </div>

    </main>

</body>
</html>