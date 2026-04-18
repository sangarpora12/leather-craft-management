@extends('layouts.admin') {{-- <--- KUNCI: Otomatis panggil Sidebar & CSS Admin --}}

@section('title_header', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & FILTER PERIODE --}}
    <div class="flex flex-col md:flex-row justify-between items-center bg-[#111] border border-white/10 rounded-xl p-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Ringkasan Finansial</h2>
            <p class="text-gray-400 text-xs mt-1">Pantau arus kas (Cash Flow) perusahaan secara real-time.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-black border border-white/10 rounded-lg p-1">
            <button class="px-4 py-1.5 text-xs font-bold bg-white text-black rounded shadow transition">Bulan Ini</button>
            <button class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-white transition">Triwulan</button>
            <button class="px-4 py-1.5 text-xs font-bold text-gray-500 hover:text-white transition">Tahunan</button>
        </div>
    </div>

    {{-- 2. KARTU STATISTIK KEUANGAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Card Pemasukan --}}
        <div class="bg-gradient-to-br from-emerald-900/40 to-[#111] p-6 rounded-2xl border border-emerald-500/20 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition"><i class="fas fa-wallet text-6xl text-emerald-500"></i></div>
            <p class="text-emerald-500 font-bold uppercase text-[10px] tracking-widest mb-1">Total Pemasukan</p>
            <h3 class="text-3xl font-black text-white">Rp 245.500.000</h3>
            <div class="mt-3 flex items-center gap-2">
                <span class="bg-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded">+15%</span>
                <span class="text-[10px] text-gray-500">dari bulan lalu</span>
            </div>
        </div>

        {{-- Card Pengeluaran --}}
        <div class="bg-gradient-to-br from-red-900/40 to-[#111] p-6 rounded-2xl border border-red-500/20 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition"><i class="fas fa-money-bill-wave text-6xl text-red-500"></i></div>
            <p class="text-red-500 font-bold uppercase text-[10px] tracking-widest mb-1">Total Pengeluaran</p>
            <h3 class="text-3xl font-black text-white">Rp 120.000.000</h3>
            <div class="mt-3 flex items-center gap-2">
                <span class="bg-red-500/20 text-red-400 text-[10px] font-bold px-2 py-0.5 rounded">+2%</span>
                <span class="text-[10px] text-gray-500">biaya operasional</span>
            </div>
        </div>

        {{-- Card Laba Bersih --}}
        <div class="bg-gradient-to-br from-blue-900/40 to-[#111] p-6 rounded-2xl border border-blue-500/30 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition"><i class="fas fa-chart-line text-6xl text-blue-500"></i></div>
            <p class="text-blue-400 font-bold uppercase text-[10px] tracking-widest mb-1">Laba Bersih (Net Profit)</p>
            <h3 class="text-3xl font-black text-white">Rp 125.500.000</h3>
            <div class="mt-3">
                <span class="text-[10px] bg-blue-500/20 px-2 py-1 rounded text-blue-300 font-bold border border-blue-500/30">Margin Profit: 51%</span>
            </div>
        </div>
    </div>

    {{-- 3. CHART & TABEL TRANSAKSI --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- GRAFIK ARUS KAS --}}
        <div class="lg:col-span-2 bg-[#111] rounded-xl border border-white/10 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Grafik Arus Kas (2026)</h3>
                <button class="text-gray-500 hover:text-white"><i class="fas fa-download"></i></button>
            </div>
            <div class="relative h-80 w-full">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        {{-- TABEL TRANSAKSI TERAKHIR --}}
        <div class="bg-[#111] rounded-xl border border-white/10 p-6 flex flex-col">
            <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-4">Transaksi Terakhir</h3>
            
            <div class="space-y-4 overflow-y-auto flex-1 custom-scrollbar pr-2" style="max-height: 320px;">
                
                {{-- Item 1 (Masuk) --}}
                <div class="flex justify-between items-center pb-3 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Penjualan Partai Besar</p>
                            <p class="text-[10px] text-gray-500">Distributor Jkt Selatan</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-emerald-400">+ Rp 45.000.000</p>
                        <p class="text-[10px] text-gray-600">Hari ini</p>
                    </div>
                </div>

                {{-- Item 2 (Keluar) --}}
                <div class="flex justify-between items-center pb-3 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Belanja Kulit Sapi</p>
                            <p class="text-[10px] text-gray-500">Supplier: CV. Maju</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-red-400">- Rp 12.500.000</p>
                        <p class="text-[10px] text-gray-600">Kemarin</p>
                    </div>
                </div>

                {{-- Item 3 (Keluar) --}}
                <div class="flex justify-between items-center pb-3 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Gaji Karyawan</p>
                            <p class="text-[10px] text-gray-500">Periode Januari</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-red-400">- Rp 25.000.000</p>
                        <p class="text-[10px] text-gray-600">28 Jan</p>
                    </div>
                </div>

                {{-- Item 4 (Masuk) --}}
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-500">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Penjualan Eceran</p>
                            <p class="text-[10px] text-gray-500">Toko Cabang Malioboro</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-emerald-400">+ Rp 8.200.000</p>
                        <p class="text-[10px] text-gray-600">27 Jan</p>
                    </div>
                </div>

            </div>
            
            <button class="w-full mt-4 py-2 border border-white/10 hover:bg-white/5 text-xs text-gray-400 hover:text-white rounded-lg transition">
                Lihat Semua Transaksi
            </button>
        </div>
    </div>

</div>

{{-- SCRIPT CHART JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financeChart').getContext('2d');
    
    // Gradient Hijau
    let gradGreen = ctx.createLinearGradient(0, 0, 0, 300);
    gradGreen.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
    gradGreen.addColorStop(1, 'rgba(16, 185, 129, 0)');

    // Gradient Merah
    let gradRed = ctx.createLinearGradient(0, 0, 0, 300);
    gradRed.addColorStop(0, 'rgba(239, 68, 68, 0.5)');
    gradRed.addColorStop(1, 'rgba(239, 68, 68, 0)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            datasets: [
                { 
                    label: 'Pemasukan', 
                    data: [150, 180, 160, 200, 220, 245], 
                    backgroundColor: '#10b981', 
                    borderRadius: 4,
                    barPercentage: 0.6
                },
                { 
                    label: 'Pengeluaran', 
                    data: [100, 110, 95, 120, 130, 120], 
                    backgroundColor: '#ef4444', 
                    borderRadius: 4,
                    barPercentage: 0.6
                }
            ]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { 
                legend: { position: 'bottom', labels: { color: '#9ca3af', font: { size: 10 } } } 
            }, 
            scales: { 
                y: { 
                    grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false }, 
                    ticks: { color: '#6b7280', font: { size: 10 } } 
                }, 
                x: { 
                    grid: { display: false }, 
                    ticks: { color: '#6b7280', font: { size: 10 } } 
                } 
            } 
        }
    });
});
</script>
@endsection