@extends('layouts.admin')
@section('title_header', 'Ringkasan Operasional Pabrik')

@section('content')
<div class="space-y-6" x-data="{ period: 'weekly' }">
    
    {{-- 1. HEADER & CONTROL PANEL --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-3 bg-[#111] p-3 rounded-xl border border-white/5">
        <div>
            <h2 class="text-base font-bold text-white">Dashboard Analisa Produksi</h2>
            <p class="text-[11px] text-gray-500">Update Terakhir: {{ now()->format('d M Y H:i') }} WIB</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] text-gray-500 uppercase font-bold mr-2">Filter:</span>
            <div class="flex bg-black border border-white/10 rounded-lg p-1">
                <button @click="period = 'daily'" :class="period === 'daily' ? 'bg-white text-black' : 'text-gray-500 hover:text-white'" class="px-3 py-1 text-[11px] font-bold rounded transition">Harian</button>
                <button @click="period = 'weekly'" :class="period === 'weekly' ? 'bg-white text-black' : 'text-gray-500 hover:text-white'" class="px-3 py-1 text-[11px] font-bold rounded transition">Mingguan</button>
                <button @click="period = 'monthly'" :class="period === 'monthly' ? 'bg-white text-black' : 'text-gray-500 hover:text-white'" class="px-3 py-1 text-[11px] font-bold rounded transition">Bulanan</button>
            </div>
            <button class="px-3 py-2 bg-[#222] hover:bg-[#333] border border-white/10 text-white text-[11px] rounded-lg transition ml-2 flex items-center gap-2">
                <i class="fas fa-print"></i> Export PDF
            </button>
        </div>
    </div>

    {{-- 2. KARTU INDIKATOR KUNCI (KPI) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Card Total Output --}}
        <div class="bg-[#111] p-5 rounded-2xl border border-white/10 relative group overflow-hidden transition hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/5 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-emerald-500/10"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-[#1a1a1a] rounded-xl text-emerald-500 border border-white/5">
                    <i class="fas fa-cubes text-xl"></i>
                </div>
                <span class="flex items-center text-[10px] font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full">
                    <i class="fas fa-arrow-up mr-1"></i> +12.5%
                </span>
            </div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Output Produksi</p>
            <h3 class="text-2xl font-black text-white mt-1">1,450 <span class="text-xs font-medium text-gray-500">unit</span></h3>
            <p class="text-[10px] text-gray-600 mt-2">Target: 1,200 unit</p>
        </div>

        {{-- Card Efisiensi --}}
        <div class="bg-[#111] p-5 rounded-2xl border border-white/10 relative group overflow-hidden transition hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-blue-500/10"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-[#1a1a1a] rounded-xl text-blue-500 border border-white/5">
                    <i class="fas fa-stopwatch text-xl"></i>
                </div>
                <span class="flex items-center text-[10px] font-bold text-blue-500 bg-blue-500/10 px-2 py-1 rounded-full">
                    Optimal
                </span>
            </div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Efisiensi Waktu</p>
            <h3 class="text-2xl font-black text-white mt-1">94.2%</h3>
            <div class="w-full bg-gray-800 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-500 h-full rounded-full" style="width: 94.2%"></div>
            </div>
        </div>

        {{-- Card Defect Rate --}}
        <div class="bg-[#111] p-5 rounded-2xl border border-white/10 relative group overflow-hidden transition hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-500/5 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-red-500/10"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-[#1a1a1a] rounded-xl text-red-500 border border-white/5">
                    <i class="fas fa-recycle text-xl"></i>
                </div>
                <span class="flex items-center text-[10px] font-bold text-red-500 bg-red-500/10 px-2 py-1 rounded-full">
                    <i class="fas fa-arrow-down mr-1"></i> -0.5%
                </span>
            </div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Defect Rate (Cacat)</p>
            <h3 class="text-2xl font-black text-white mt-1">2.1%</h3>
            <p class="text-[10px] text-gray-600 mt-2">Total Reject: 30 unit</p>
        </div>

        {{-- Card Machine Utility --}}
        <div class="bg-[#111] p-5 rounded-2xl border border-white/10 relative group overflow-hidden transition hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/5 rounded-bl-full -mr-4 -mt-4 transition group-hover:bg-orange-500/10"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-[#1a1a1a] rounded-xl text-orange-500 border border-white/5">
                    <i class="fas fa-cogs text-xl"></i>
                </div>
                <span class="flex items-center text-[10px] font-bold text-orange-500 bg-orange-500/10 px-2 py-1 rounded-full">
                    Maintenance
                </span>
            </div>
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Utilitas Mesin</p>
            <h3 class="text-2xl font-black text-white mt-1">88%</h3>
            <p class="text-[10px] text-gray-600 mt-2">2 Mesin dalam perbaikan</p>
        </div>
    </div>

    {{-- 3. AREA CHART (GRAFIK UTAMA) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Grafik Garis --}}
        <div class="lg:col-span-2 bg-[#111] p-5 rounded-2xl border border-white/10">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Tren Output Produksi</h3>
                    <p class="text-xs text-gray-500">Perbandingan Target vs Realisasi</p>
                </div>
                <button class="text-gray-500 hover:text-white"><i class="fas fa-ellipsis-h"></i></button>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="productionTrendChart"></canvas>
            </div>
        </div>

        {{-- Grafik Donat & Log --}}
        <div class="flex flex-col gap-6">
            {{-- Grafik Kategori --}}
            <div class="bg-[#111] p-5 rounded-2xl border border-white/10 flex-1">
                <h3 class="text-sm font-bold text-white mb-4 uppercase tracking-wider">Komposisi Produk</h3>
                <div class="relative h-44 w-full flex justify-center">
                    <canvas id="productMixChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between text-xs text-gray-400">
                        <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tas Kulit</span>
                        <span>45%</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Dompet</span>
                        <span>30%</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Ikat Pinggang</span>
                        <span>25%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. TABEL DETAIL HARIAN (VERSI FULL) --}}
    <div class="bg-[#111] border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-white">Laporan Operasional Harian</h3>
                <p class="text-xs text-gray-500">Data detail per shift produksi</p>
            </div>
            <div class="flex gap-2">
                <input type="text" placeholder="Cari Kode SPK..." class="bg-black border border-white/10 text-xs text-white px-4 py-2 rounded-lg focus:outline-none focus:border-emerald-500">
                <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">Download Excel</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-[#1a1a1a] text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Shift</th>
                        <th class="px-6 py-4">Kode SPK</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4 text-center">Target</th>
                        <th class="px-6 py-4 text-center">Hasil</th>
                        <th class="px-6 py-4 text-center">Reject</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs text-gray-300">
                    {{-- Loop Data (Dummy 5 Baris) --}}
                    @for ($i = 1; $i <= 5; $i++)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4 text-gray-400">{{ now()->subDays($i)->format('d M Y') }}</td>
                        <td class="px-6 py-4"><span class="bg-[#222] px-2 py-1 rounded text-[10px] border border-white/10">Shift {{ rand(1,2) }}</span></td>
                        <td class="px-6 py-4 font-mono text-emerald-400">SPK-2601-00{{ $i }}</td>
                        <td class="px-6 py-4 font-bold text-white">
                            {{ ['Tas Kulit Premium', 'Dompet Pria V2', 'Sabuk Kulit Asli'][rand(0,2)] }}
                        </td>
                        <td class="px-6 py-4 text-center">100</td>
                        <td class="px-6 py-4 text-center font-bold text-white">{{ rand(90, 105) }}</td>
                        <td class="px-6 py-4 text-center text-red-400 font-bold">{{ rand(0, 5) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if(rand(0,1))
                                <span class="px-2 py-1 bg-green-500/10 text-green-500 rounded border border-green-500/20 font-bold text-[10px]">SELESAI</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-500/10 text-yellow-500 rounded border border-yellow-500/20 font-bold text-[10px]">PROSES</span>
                            @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-white/5 bg-[#0f0f0f] flex justify-end">
            <div class="flex gap-1">
                <button class="px-3 py-1 bg-[#222] rounded text-gray-500 text-xs hover:text-white">Prev</button>
                <button class="px-3 py-1 bg-emerald-600 rounded text-white text-xs font-bold">1</button>
                <button class="px-3 py-1 bg-[#222] rounded text-gray-500 text-xs hover:text-white">2</button>
                <button class="px-3 py-1 bg-[#222] rounded text-gray-500 text-xs hover:text-white">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. CONFIG CHART LINE (TREN PRODUKSI)
    const ctxLine = document.getElementById('productionTrendChart').getContext('2d');
    
    // Gradient Warna Hijau
    let gradient = ctxLine.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald-500 opacity
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [
                {
                    label: 'Realisasi Output',
                    data: [120, 135, 125, 150, 165, 140, 95],
                    borderColor: '#10b981', // Emerald 500
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4, // Melengkung halus
                    fill: true,
                    pointBackgroundColor: '#064e3b',
                    pointBorderColor: '#10b981',
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Target Harian',
                    data: [130, 130, 130, 130, 130, 130, 130],
                    borderColor: '#4b5563', // Gray 600
                    borderWidth: 2,
                    borderDash: [5, 5], // Garis putus-putus
                    tension: 0,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#9ca3af', font: { size: 11 } }
                },
                tooltip: {
                    backgroundColor: '#111',
                    titleColor: '#fff',
                    bodyColor: '#ccc',
                    borderColor: '#333',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#6b7280', font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b7280', font: { size: 10 } }
                }
            }
        }
    });

    // 2. CONFIG CHART DOUGHNUT (KOMPOSISI PRODUK)
    const ctxPie = document.getElementById('productMixChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Tas Kulit', 'Dompet', 'Sabuk'],
            datasets: [{
                data: [45, 30, 25],
                backgroundColor: ['#10b981', '#3b82f6', '#f97316'], // Emerald, Blue, Orange
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%', // Bolong tengah besar
            plugins: {
                legend: { display: false } // Legend Custom di HTML
            }
        }
    });
</script>
@endpush