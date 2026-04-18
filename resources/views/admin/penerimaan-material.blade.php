@extends('layouts.admin')
@section('title_header', 'Penerimaan Material')

@section('content')
<div class="space-y-6">

    {{-- HEADER PAGE --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-white/10 pb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Penerimaan Material</h2>
            <p class="text-gray-400 text-xs mt-1">Input manual barang masuk dari supplier ke gudang.</p>
        </div>
        <button class="bg-[#111] border border-white/10 hover:bg-[#222] text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition">
            <i class="fas fa-file-download text-emerald-500"></i> Unduh Laporan
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: FORM INPUT --}}
        <div class="lg:col-span-1">
            <div class="bg-[#111] rounded-xl border border-white/10 p-6 sticky top-6 shadow-xl">
                <h3 class="font-bold text-white mb-6 flex items-center gap-2 text-sm">
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-500">
                        <i class="fas fa-plus"></i>
                    </span>
                    Tambah Penerimaan
                </h3>

                <form action="#" class="space-y-5">
                    
                    {{-- Input Nama Material --}}
                    <div>
                        <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Nama Material</label>
                        <div class="relative">
                            <select class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-xs text-white focus:border-emerald-500 outline-none appearance-none cursor-pointer">
                                <option value="" disabled selected>-- Pilih Material --</option>
                                <option>Kain Katun</option>
                                <option>Benang Poliester</option>
                                <option>Kancing Plastik</option>
                                <option>Resleting 20cm</option>
                            </select>
                            <i class="fas fa-chevron-down text-gray-500 text-xs absolute right-4 top-3.5 pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Input Kuantitas --}}
                    <div>
                        <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Kuantitas Masuk</label>
                        <div class="flex items-center">
                            <input type="number" placeholder="0" class="w-full bg-black border border-white/10 rounded-l-lg px-4 py-3 text-xs text-white focus:border-emerald-500 outline-none">
                            <span class="bg-[#222] border border-white/10 border-l-0 rounded-r-lg px-4 py-3 text-xs text-gray-400 font-bold min-w-[60px] text-center">Unit</span>
                        </div>
                    </div>

                    {{-- Input Supplier --}}
                    <div>
                        <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Pemasok / Supplier</label>
                        <input type="text" placeholder="Nama Supplier..." class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-xs text-white focus:border-emerald-500 outline-none">
                    </div>

                    {{-- Input Tanggal --}}
                    <div>
                        <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Tanggal Terima</label>
                        <input type="date" class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-xs text-white focus:border-emerald-500 outline-none">
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="pt-4 border-t border-white/10">
                        <button type="submit" class="w-full py-3 rounded-lg bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-500 shadow-lg shadow-emerald-900/20 transition flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- KOLOM KANAN: RIWAYAT TABEL --}}
        <div class="lg:col-span-2">
            <div class="bg-[#111] rounded-xl border border-white/10 flex flex-col h-full overflow-hidden shadow-xl">
                <div class="p-5 border-b border-white/10 bg-[#151515] flex justify-between items-center">
                    <h3 class="font-bold text-white text-sm">Riwayat Penerimaan</h3>
                    <span class="text-[10px] bg-black border border-white/10 px-2 py-1 rounded text-gray-400">Bulan Ini</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#1a1a1a] text-gray-500 text-[10px] uppercase font-bold">
                            <tr>
                                <th class="p-4">Nama Material</th>
                                <th class="p-4 text-center">Qty</th>
                                <th class="p-4">Pemasok</th>
                                <th class="p-4 text-right">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-gray-300 text-xs">
                            
                            {{-- Row 1 --}}
                            <tr class="hover:bg-white/5 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-white">Kain Katun</div>
                                    <div class="text-[10px] text-gray-500 group-hover:text-emerald-400 font-mono mt-0.5">BAH-K-002</div>
                                </td>
                                <td class="p-4 text-center font-bold text-white">100 <span class="text-[10px] font-normal text-gray-500">mtr</span></td>
                                <td class="p-4 text-xs">PT. Tekstil Jaya</td>
                                <td class="p-4 text-right text-[10px] font-mono text-gray-400">20 Okt 2023</td>
                            </tr>

                            {{-- Row 2 --}}
                            <tr class="hover:bg-white/5 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-white">Benang Poliester</div>
                                    <div class="text-[10px] text-gray-500 group-hover:text-emerald-400 font-mono mt-0.5">BAH-B-005</div>
                                </td>
                                <td class="p-4 text-center font-bold text-white">500 <span class="text-[10px] font-normal text-gray-500">roll</span></td>
                                <td class="p-4 text-xs">CV. Benang Kuat</td>
                                <td class="p-4 text-right text-[10px] font-mono text-gray-400">18 Okt 2023</td>
                            </tr>

                            {{-- Row 3 --}}
                            <tr class="hover:bg-white/5 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-white">Kancing Plastik</div>
                                    <div class="text-[10px] text-gray-500 group-hover:text-emerald-400 font-mono mt-0.5">ACC-K-001</div>
                                </td>
                                <td class="p-4 text-center font-bold text-white">1,000 <span class="text-[10px] font-normal text-gray-500">pcs</span></td>
                                <td class="p-4 text-xs">Toko Kancing Abadi</td>
                                <td class="p-4 text-right text-[10px] font-mono text-gray-400">15 Okt 2023</td>
                            </tr>

                            {{-- Row 4 --}}
                            <tr class="hover:bg-white/5 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-white">Resleting 20cm</div>
                                    <div class="text-[10px] text-gray-500 group-hover:text-emerald-400 font-mono mt-0.5">ACC-R-020</div>
                                </td>
                                <td class="p-4 text-center font-bold text-white">250 <span class="text-[10px] font-normal text-gray-500">pcs</span></td>
                                <td class="p-4 text-xs">PT. Aksesori Garmen</td>
                                <td class="p-4 text-right text-[10px] font-mono text-gray-400">12 Okt 2023</td>
                            </tr>

                            {{-- Row 5 --}}
                            <tr class="hover:bg-white/5 transition group">
                                <td class="p-4">
                                    <div class="font-bold text-white">Lem Aica Aibon</div>
                                    <div class="text-[10px] text-gray-500 group-hover:text-emerald-400 font-mono mt-0.5">KIM-L-001</div>
                                </td>
                                <td class="p-4 text-center font-bold text-white">20 <span class="text-[10px] font-normal text-gray-500">klg</span></td>
                                <td class="p-4 text-xs">CV. Kimia Sejahtera</td>
                                <td class="p-4 text-right text-[10px] font-mono text-gray-400">10 Okt 2023</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-white/10 flex justify-between items-center text-[10px] text-gray-500 bg-[#151515]">
                    <span>Menampilkan 5 data terbaru</span>
                    <div class="flex gap-1">
                        <button class="px-2 py-1 border border-white/10 rounded hover:bg-white/10">Prev</button>
                        <button class="px-2 py-1 bg-emerald-600 text-white rounded font-bold">1</button>
                        <button class="px-2 py-1 border border-white/10 rounded hover:bg-white/10">2</button>
                        <button class="px-2 py-1 border border-white/10 rounded hover:bg-white/10">Next</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection