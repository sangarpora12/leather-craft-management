@extends('layouts.admin') {{-- <--- INI KUNCINYA: Pakai Layout Utama --}}

@section('title_header', 'Laporan Hasil Produksi')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & FILTER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-white/10 pb-6 gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Laporan Hasil Produksi</h2>
            <p class="text-gray-400 text-xs mt-1">Log aktivitas operator dan bukti penyelesaian produk.</p>
        </div>
        
        <div class="flex gap-3 w-full md:w-auto">
            <div class="relative group">
                <input type="date" class="bg-[#111] border border-white/10 rounded-lg py-2 px-3 text-xs text-white focus:border-emerald-500 outline-none cursor-pointer">
            </div>
            <button class="bg-[#111] hover:bg-[#222] border border-white/10 text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition">
                <i class="fas fa-file-excel text-green-500"></i> Unduh Excel
            </button>
        </div>
    </div>

    {{-- 2. GRID KARTU LAPORAN (KONTEN ASLI KAMU) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- CARD 1 --}}
        <div class="bg-[#111] rounded-xl border border-white/10 overflow-hidden hover:border-emerald-500/30 transition group">
            <div class="px-5 py-3 border-b border-white/5 flex justify-between items-center bg-[#151515]">
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded">SPK-00123</span>
                <span class="text-[10px] text-gray-500">24 Nov • 14:30</span>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white shadow-lg">Z</div>
                    <div>
                        <h4 class="text-sm font-bold text-white">Zulkifli</h4>
                        <p class="text-[10px] text-gray-500">Operator Gudang</p>
                    </div>
                </div>

                <div class="flex gap-3 mb-4 bg-black/40 p-2 rounded-lg border border-white/5">
                    <div class="w-10 h-10 bg-[#222] rounded flex-shrink-0 flex items-center justify-center text-gray-600"><i class="fas fa-box"></i></div> 
                    <div>
                        <h5 class="text-xs font-bold text-white">Rome Bag</h5>
                        <p class="text-[10px] text-gray-500">Varian: Tan Leather</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-[#1a1a1a] p-2 rounded border border-white/5 text-center">
                        <p class="text-[9px] text-gray-500 uppercase font-bold">Target</p>
                        <p class="text-sm font-bold text-white">12</p>
                    </div>
                    <div class="bg-emerald-900/20 p-2 rounded border border-emerald-500/20 text-center">
                        <p class="text-[9px] text-emerald-500 uppercase font-bold">Hasil OK</p>
                        <p class="text-sm font-bold text-emerald-400">12 OK</p>
                    </div>
                </div>

                <button class="w-full py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:text-white transition flex items-center justify-center gap-2">
                    <i class="fas fa-image"></i> Lihat Bukti
                </button>
            </div>
        </div>

        {{-- CARD 2 --}}
        <div class="bg-[#111] rounded-xl border border-white/10 overflow-hidden hover:border-emerald-500/30 transition group">
            <div class="px-5 py-3 border-b border-white/5 flex justify-between items-center bg-[#151515]">
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded">SPK-00124</span>
                <span class="text-[10px] text-gray-500">24 Nov • 10:15</span>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-xs font-bold text-white shadow-lg">B</div>
                    <div>
                        <h4 class="text-sm font-bold text-white">Budi S.</h4>
                        <p class="text-[10px] text-gray-500">Operator Jahit</p>
                    </div>
                </div>

                <div class="flex gap-3 mb-4 bg-black/40 p-2 rounded-lg border border-white/5">
                    <div class="w-10 h-10 bg-[#222] rounded flex-shrink-0 flex items-center justify-center text-gray-600"><i class="fas fa-wallet"></i></div>
                    <div>
                        <h5 class="text-xs font-bold text-white">Milano Wallet</h5>
                        <p class="text-[10px] text-gray-500">Varian: Black Premium</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-[#1a1a1a] p-2 rounded border border-white/5 text-center">
                        <p class="text-[9px] text-gray-500 uppercase font-bold">Target</p>
                        <p class="text-sm font-bold text-white">50</p>
                    </div>
                    <div class="bg-orange-900/10 p-2 rounded border border-orange-500/20 text-center relative overflow-hidden">
                        <p class="text-[9px] text-orange-500 uppercase font-bold">Hasil OK</p>
                        <p class="text-sm font-bold text-orange-400">48 OK</p>
                        <span class="absolute top-0 right-0 bg-red-600 text-white text-[8px] px-1.5 py-0.5 rounded-bl font-bold">2 Reject</span>
                    </div>
                </div>

                <button class="w-full py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:text-white transition flex items-center justify-center gap-2">
                    <i class="fas fa-image"></i> Lihat Bukti
                </button>
            </div>
        </div>

        {{-- CARD 3 --}}
        <div class="bg-[#111] rounded-xl border border-white/10 overflow-hidden hover:border-emerald-500/30 transition group">
            <div class="px-5 py-3 border-b border-white/5 flex justify-between items-center bg-[#151515]">
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded">SPK-00120</span>
                <span class="text-[10px] text-gray-500">23 Nov • 16:00</span>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-xs font-bold text-white shadow-lg">A</div>
                    <div>
                        <h4 class="text-sm font-bold text-white">Andi L.</h4>
                        <p class="text-[10px] text-gray-500">Operator Cutting</p>
                    </div>
                </div>

                <div class="flex gap-3 mb-4 bg-black/40 p-2 rounded-lg border border-white/5">
                    <div class="w-10 h-10 bg-[#222] rounded flex-shrink-0 flex items-center justify-center text-gray-600"><i class="fas fa-cut"></i></div>
                    <div>
                        <h5 class="text-xs font-bold text-white">Belt Classic</h5>
                        <p class="text-[10px] text-gray-500">Varian: Brown</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-[#1a1a1a] p-2 rounded border border-white/5 text-center">
                        <p class="text-[9px] text-gray-500 uppercase font-bold">Target</p>
                        <p class="text-sm font-bold text-white">30</p>
                    </div>
                    <div class="bg-green-900/20 p-2 rounded border border-green-500/20 text-center">
                        <p class="text-[9px] text-green-500 uppercase font-bold">Hasil OK</p>
                        <p class="text-sm font-bold text-green-400">30 OK</p>
                    </div>
                </div>

                <button class="w-full py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:text-white transition flex items-center justify-center gap-2">
                    <i class="fas fa-image"></i> Lihat Bukti
                </button>
            </div>
        </div>

    </div>

    {{-- 3. PAGINATION --}}
    <div class="flex justify-between items-center border-t border-white/5 pt-6">
        <span class="text-xs text-gray-500">Menampilkan 6 dari 124 data</span>
        <div class="flex gap-1">
            <button class="px-3 py-1 text-xs border border-white/10 rounded hover:bg-white/5 text-gray-400">Prev</button>
            <button class="px-3 py-1 text-xs bg-emerald-600 text-white rounded font-bold">1</button>
            <button class="px-3 py-1 text-xs border border-white/10 rounded hover:bg-white/5 text-gray-400">2</button>
            <button class="px-3 py-1 text-xs border border-white/10 rounded hover:bg-white/5 text-gray-400">Next</button>
        </div>
    </div>

</div>
@endsection