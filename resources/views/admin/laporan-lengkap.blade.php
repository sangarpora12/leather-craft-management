@extends('layouts.admin') 
{{-- Judul di Header Atas --}}
@section('title_header', 'Laporan Operasional')

@section('content')
{{-- Style Khusus Print (Agar Sidebar Hilang saat di-Print) --}}
<style>
    @media print {
        aside, header, .no-print { display: none !important; }
        body, main { background: white !important; color: black !important; padding: 0 !important; margin: 0 !important; overflow: visible !important; }
        .bg-white-print { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; border: none !important; }
        /* Sembunyikan elemen layout admin */
        nav, .sidebar-container { display: none !important; }
    }
</style>

<div class="min-h-screen">
    
    {{-- HEADER & TOMBOL ACTION (Akan hilang saat diprint) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-6 border-b border-white/10 no-print">
        <div>
            <h2 class="text-2xl font-bold text-white">Laporan Operasional</h2>
            <p class="text-gray-400 text-sm mt-1">Rekapitulasi produksi dan penggunaan bahan baku.</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <div class="bg-[#1A1A1A] border border-white/10 rounded-lg px-4 py-2 flex items-center gap-3">
                <span class="text-[10px] uppercase font-bold text-gray-500">Periode</span>
                <select class="bg-transparent text-sm font-bold text-white outline-none cursor-pointer focus:ring-0 border-none p-0">
                    <option class="bg-black">Agustus 2023</option>
                    <option class="bg-black">Juli 2023</option>
                </select>
            </div>
            <button onclick="window.print()" class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg flex items-center gap-2 transition">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- AREA KERTAS LAPORAN (Desain Asli Kamu) --}}
    <div class="bg-white rounded-xl p-10 text-black shadow-2xl max-w-5xl mx-auto bg-white-print relative min-h-[1000px]">

        {{-- Kop Laporan --}}
        <div class="flex justify-between items-end border-b-2 border-gray-100 pb-6 mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Laporan Operasional</h1>
                <p class="text-sm text-gray-500 mt-1">Rekapitulasi Hasil Produksi & Belanja Bahan</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dibuat Oleh</p>
                <p class="text-sm font-bold text-gray-900">Admin Gudang <span class="bg-gray-200 text-gray-600 text-[10px] px-1.5 py-0.5 rounded ml-1">AD</span></p>
            </div>
        </div>

        {{-- Ringkasan Periode --}}
        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100 mb-10 flex justify-between items-center">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Periode Laporan</p>
                <p class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="far fa-calendar-alt text-gray-400"></i>
                    1 Agustus 2023 - 31 Agustus 2023
                </p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Output</p>
                <p class="text-3xl font-black text-teal-600">440 <span class="text-sm font-medium text-gray-500">Unit</span></p>
            </div>
        </div>

        {{-- Tabel 1: Output Produksi --}}
        <div class="mb-10">
            <h3 class="text-sm font-bold text-gray-900 uppercase border-l-4 border-black pl-3 mb-4">I. Output Produksi</h3>
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-100 text-gray-500 uppercase font-bold border-b border-gray-200">
                        <tr>
                            <th class="p-3">Kode SPK</th>
                            <th class="p-3">Produk Jadi</th>
                            <th class="p-3">Operator</th>
                            <th class="p-3 text-center">Qty</th>
                            <th class="p-3 text-right">HPP / Unit</th>
                            <th class="p-3 text-right bg-gray-200 text-gray-700">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-400 font-mono">SPK-00123</td>
                            <td class="p-3 font-bold text-gray-800">Tas Kulit Klasik (Tan)</td>
                            <td class="p-3 text-gray-500">Atep Sunandar</td>
                            <td class="p-3 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">50</span></td>
                            <td class="p-3 text-right text-gray-500">Rp 150.000</td>
                            <td class="p-3 text-right font-bold text-gray-900 bg-gray-50">Rp 7.500.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-400 font-mono">SPK-00124</td>
                            <td class="p-3 font-bold text-gray-800">Dompet Pria (Black)</td>
                            <td class="p-3 text-gray-500">Budi Santoso</td>
                            <td class="p-3 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">100</span></td>
                            <td class="p-3 text-right text-gray-500">Rp 45.000</td>
                            <td class="p-3 text-right font-bold text-gray-900 bg-gray-50">Rp 4.500.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-400 font-mono">SPK-00125</td>
                            <td class="p-3 font-bold text-gray-800">Ikat Pinggang (Brown)</td>
                            <td class="p-3 text-gray-500">Zulkifli</td>
                            <td class="p-3 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">80</span></td>
                            <td class="p-3 text-right text-gray-500">Rp 35.000</td>
                            <td class="p-3 text-right font-bold text-gray-900 bg-gray-50">Rp 2.800.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-400 font-mono">SPK-00126</td>
                            <td class="p-3 font-bold text-gray-800">Rome Bag (Maroon)</td>
                            <td class="p-3 text-gray-500">Siti Aminah</td>
                            <td class="p-3 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">10</span></td>
                            <td class="p-3 text-right text-gray-500">Rp 450.000</td>
                            <td class="p-3 text-right font-bold text-gray-900 bg-gray-50">Rp 4.500.000</td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 text-gray-400 font-mono">SPK-00127</td>
                            <td class="p-3 font-bold text-gray-800">Key Holder</td>
                            <td class="p-3 text-gray-500">Atep Sunandar</td>
                            <td class="p-3 text-center"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold">200</span></td>
                            <td class="p-3 text-right text-gray-500">Rp 15.000</td>
                            <td class="p-3 text-right font-bold text-gray-900 bg-gray-50">Rp 3.000.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel 2: Penggunaan Bahan Baku --}}
        <div>
            <h3 class="text-sm font-bold text-gray-900 uppercase border-l-4 border-black pl-3 mb-4">II. Penggunaan Bahan Baku (Per Supplier)</h3>
            
            <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                {{-- Supplier 1 --}}
                <div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-2">
                        <span class="text-xs font-bold text-gray-800">Kulit Jaya Abadi</span>
                        <span class="text-[9px] text-gray-400 font-mono">#SUP-001</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Kulit Sapi (Lbr)</span> <span class="font-bold text-gray-700">20</span> <span class="text-gray-800">Rp 10.000.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Kulit Sintetis A</span> <span class="font-bold text-gray-700">15</span> <span class="text-gray-800">Rp 2.500.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Suede Lining</span> <span class="font-bold text-gray-700">50</span> <span class="text-gray-800">Rp 1.500.000</span></div>
                    </div>
                    <div class="text-right text-xs font-bold text-orange-600 mt-2 border-t border-dashed border-gray-200 pt-1">Total: Rp 14.000.000</div>
                </div>

                {{-- Supplier 2 --}}
                <div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-2">
                        <span class="text-xs font-bold text-gray-800">Benang Mas Abadi</span>
                        <span class="text-[9px] text-gray-400 font-mono">#SUP-002</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Benang Nilon (Rol)</span> <span class="font-bold text-gray-700">50</span> <span class="text-gray-800">Rp 1.500.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Resleting YKK</span> <span class="font-bold text-gray-700">200</span> <span class="text-gray-800">Rp 2.850.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Kancing Besi</span> <span class="font-bold text-gray-700">500</span> <span class="text-gray-800">Rp 500.000</span></div>
                    </div>
                    <div class="text-right text-xs font-bold text-orange-600 mt-2 border-t border-dashed border-gray-200 pt-1">Total: Rp 4.850.000</div>
                </div>

                {{-- Supplier 3 --}}
                <div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-2">
                        <span class="text-xs font-bold text-gray-800">CV. Logam Mulia</span>
                        <span class="text-[9px] text-gray-400 font-mono">#SUP-003</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Ring D (2cm)</span> <span class="font-bold text-gray-700">200</span> <span class="text-gray-800">Rp 400.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Gesper Sabuk</span> <span class="font-bold text-gray-700">50</span> <span class="text-gray-800">Rp 750.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Paku Keling</span> <span class="font-bold text-gray-700">1000</span> <span class="text-gray-800">Rp 150.000</span></div>
                    </div>
                    <div class="text-right text-xs font-bold text-orange-600 mt-2 border-t border-dashed border-gray-200 pt-1">Total: Rp 1.300.000</div>
                </div>

                {{-- Supplier 4 --}}
                <div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-2">
                        <span class="text-xs font-bold text-gray-800">Toko Kimia Maju</span>
                        <span class="text-[9px] text-gray-400 font-mono">#SUP-004</span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Lem Kuning</span> <span class="font-bold text-gray-700">10 Kg</span> <span class="text-gray-800">Rp 650.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Cat Pinggir</span> <span class="font-bold text-gray-700">5 L</span> <span class="text-gray-800">Rp 750.000</span></div>
                        <div class="flex justify-between text-[10px] text-gray-500"><span>Finishing Wax</span> <span class="font-bold text-gray-700">2 Klg</span> <span class="text-gray-800">Rp 300.000</span></div>
                    </div>
                    <div class="text-right text-xs font-bold text-orange-600 mt-2 border-t border-dashed border-gray-200 pt-1">Total: Rp 1.700.000</div>
                </div>
            </div>
        </div>

        {{-- Total Grand Total --}}
        <div class="mt-12 border-t-2 border-gray-900 pt-6 flex justify-end">
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Total Biaya Pengadaan Bahan Baku</p>
                <h2 class="text-4xl font-black text-gray-900">Rp 21.850.000</h2>
            </div>
        </div>

        {{-- Footer Laporan --}}
        <div class="absolute bottom-10 left-10 right-10 border-t border-gray-100 pt-4 flex justify-between text-[10px] text-gray-400">
            <p>Dicetak otomatis oleh Sistem Berliano Leather • 24 Nov 2025, 10:00 WIB</p>
            <p>Halaman 1 dari 1</p>
        </div>

    </div>
</div>
@endsection