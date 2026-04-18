@extends('layouts.admin')

@php
    // 1. Fungsi format Rupiah
    $formatRupiahShort = function (float $value): string {
        if ($value >= 1000000000) return 'Rp ' . number_format($value / 1000000000, 1, ',', '.') . ' M';
        if ($value >= 1000000)    return 'Rp ' . number_format($value / 1000000, 1, ',', '.') . ' Jt';
        return 'Rp ' . number_format($value, 0, ',', '.');
    };
    $kpiTotalAset = $formatRupiahShort((float) ($totalAsetBahan ?? 0));

    // 2. Ambil status saklar notifikasi dari database
    // NOTE: "aktif" di sini seharusnya hanya mematikan PERINGATAN, bukan mematikan ringkasan.
    // Normalisasi agar aman kalau nilai di DB berupa: 1/0, '1'/'0', true/false, 'true'/'false'.
    $rawNotif = \App\Models\Pengaturan::where('nama_pengaturan', 'notif_stok_menipis')->value('nilai');
    if (is_null($rawNotif)) {
        $isNotifAktif = true; // default ON bila setting belum ada
    } else {
        $isNotifAktif = filter_var($rawNotif, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($isNotifAktif)) {
            $isNotifAktif = ((string) $rawNotif === '1');
        }
    }
@endphp

@section('content')
<div class="space-y-6">

    {{-- 
        NOTE: Banner Peringatan Besar di atas sudah dihapus sesuai permintaan Zul 
    --}}

    {{-- KPI CARDS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        {{-- Kartu 1: Total Aset --}}
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl hover:border-[#00BFA5]/50 transition-colors">
            <p class="text-[10px] font-bold text-white uppercase tracking-widest">Total Aset Bahan</p>
            <div class="mt-2 text-xl font-extrabold text-[#00BFA5]">{{ $kpiTotalAset }}</div>
            <p class="mt-1 text-[11px] text-gray-400">Valuasi stok gudang</p>
        </div>

        {{-- Kartu 2: Stok Produk Jadi --}}
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl hover:border-[#B388FF]/50 transition-colors">
            <p class="text-[10px] font-bold text-white uppercase tracking-widest">Stok Produk Jadi</p>
            <div class="mt-2 text-xl font-extrabold text-[#B388FF]">{{ number_format($stokProduk ?? 0, 0, ',', '.') }} Pcs</div>
            <p class="mt-1 text-[11px] text-gray-200">Siap didistribusikan</p>
        </div>

        {{-- Kartu 3: BAHAN BAKU KRITIS --}}
        @php
            $kritisCount = (int) ($jumlahKritis ?? 0);
            $showAlert = ($isNotifAktif === true) && ($kritisCount > 0);
        @endphp
        <div class="bg-[#1A1C24] {{ $showAlert ? 'border-red-500/50' : 'border-[#2D3139]' }} rounded-2xl p-6 shadow-xl border-l-4 {{ $showAlert ? 'border-l-red-500' : 'border-l-zinc-700' }} transition-all">
            <p class="text-[10px] font-bold {{ $showAlert ? 'text-red-500' : 'text-gray-400' }} uppercase tracking-widest">Bahan Baku Kritis</p>
            
            {{-- PERBAIKAN: ringkasan stok bahan tetap tampil (angka asli), saklar hanya mematikan peringatan. --}}
            <div class="mt-2 text-xl font-extrabold {{ $showAlert ? 'text-red-500' : 'text-white' }}">
                {{ $kritisCount }} Item
            </div>

            <p class="mt-2 text-[11px] font-medium">
                @if($showAlert)
                    <span class="text-red-500">⚠️ Segera restock!</span>
                @elseif($isNotifAktif === false)
                    <span class="text-gray-500">Peringatan dimatikan (Monitor OFF)</span>
                @else
                    <span class="text-gray-500">Status Aman</span>
                @endif
            </p>
        </div>

        {{-- Kartu 4: Produk Jadi Kritis --}}
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
            <p class="text-[10px] font-bold text-[#FFD740] uppercase tracking-widest">Produk Jadi Kritis</p>
            <div class="mt-2 text-xl font-extrabold text-[#FFD740]">{{ $jumlahProdukKritis ?? 0 }} Model</div>
            <p class="mt-1 text-[11px] text-gray-400">Stok menipis</p>
        </div>
    </div>

    {{-- BAR CHART & PRODUKSI BERJALAN --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Chart Statistik --}}
        <div class="xl:col-span-2 bg-[#1A1C24] border border-[#2D3139] rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-[#2D3139] flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-white font-bold tracking-wide text-sm">Statistik Arus Barang</h3>
                    <select id="dataType" onchange="refreshChartData()" class="bg-[#0D1117] border border-[#2D3139] text-white text-[10px] font-bold rounded-lg px-3 py-2 outline-none">
                        <option value="bahan">BAHAN BAKU</option>
                        <option value="produk">PRODUK JADI</option>
                    </select>
                </div>
                <div class="flex bg-black/30 p-1 rounded-lg border border-[#2D3139]">
                    <button onclick="changePeriod('daily', this)" class="period-btn px-4 py-1.5 text-[10px] font-bold text-white active-btn rounded-md">HARIAN</button>
                    <button onclick="changePeriod('weekly', this)" class="period-btn px-4 py-1.5 text-[10px] font-bold text-gray-500">MINGGUAN</button>
                    <button onclick="changePeriod('monthly', this)" class="period-btn px-4 py-1.5 text-[10px] font-bold text-gray-500">BULANAN</button>
                </div>
            </div>
            <div class="p-6 h-[380px] relative">
                <canvas id="chartUtama"></canvas>
                <div id="chartLoader" class="absolute inset-0 bg-[#1A1C24]/80 flex items-center justify-center hidden">
                    <div class="w-8 h-8 border-2 border-white border-t-[#00BFA5] rounded-full animate-spin"></div>
                </div>
            </div>
        </div>

        {{-- List Produksi Berjalan --}}
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2 text-sm">
                <span class="w-2 h-2 bg-[#00BFA5] rounded-full animate-pulse"></span> Produksi Berjalan
            </h3>
            <div class="space-y-3">
                @forelse(($produksiAktif ?? []) as $prod)
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <div class="text-sm font-bold text-white">{{ $prod->product->nama_produk ?? 'Unknown' }}</div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[10px] text-gray-400">{{ number_format($prod->total_qty) }} Pcs</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-[#00BFA5]/20 text-[#00BFA5] font-bold border border-[#00BFA5]/30">{{ $prod->jumlah_spk }} SPK</span>
                        </div>
                        <div class="mt-2 text-[10px] text-gray-500">Status: {{ strtoupper($prod->statuses) }}</div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500 text-xs italic">Belum ada aktivitas produksi.</div>
                @endforelse
                
                <a href="{{ route('admin.produksi.create') }}" class="w-full flex items-center justify-center gap-2 bg-[#FD730D] hover:bg-[#E6670B] text-white font-bold text-xs py-4 rounded-xl transition mt-4 active:scale-95 shadow-lg shadow-orange-950/20">
                    <i class="fa-solid fa-plus text-xs"></i> BUAT SPK BARU
                </a>
            </div>
        </div>
    </div>

    {{-- PENGADAAN BAHAN YANG PENDING --}}
    @if(isset($pengadaanBahan) && $pengadaanBahan->count() > 0)
    <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
        <h3 class="text-white font-bold mb-4 flex items-center gap-2 text-sm">
            <span class="w-2 h-2 bg-[#FFD740] rounded-full animate-pulse"></span> Pengadaan Bahan Pending
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[10px] text-gray-500 uppercase">
                    <tr>
                        <th class="text-left py-2">Bahan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Supplier</th>
                        <th class="text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139]">
                    @foreach($pengadaanBahan as $pengadaan)
                    <tr>
                        <td class="py-3 text-white font-medium">{{ $pengadaan->nama_material }}</td>
                        <td class="py-3 text-center text-[#00BFA5] font-bold">{{ number_format($pengadaan->jumlah) }} {{ $pengadaan->satuan }}</td>
                        <td class="py-3 text-center text-gray-400 text-xs">{{ $pengadaan->supplier ?? '-' }}</td>
                        <td class="py-3 text-center text-gray-500 text-xs">{{ \Carbon\Carbon::parse($pengadaan->created_at)->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<style>
    .active-btn { background: #2D3139; color: white !important; }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    // Register the plugin globally
    Chart.register(ChartDataLabels);

    let myChart;
    let serverData = {};
    const ctx = document.getElementById('chartUtama').getContext('2d');

    function renderChart(labels, masuk, keluar) {
        if (myChart) myChart.destroy();
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { 
                        label: 'Masuk', 
                        data: masuk, 
                        backgroundColor: '#00BFA5', 
                        borderRadius: 4,
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 10 },
                            formatter: function(value) {
                                return value > 0 ? value : '';
                            }
                        }
                    },
                    { 
                        label: 'Keluar', 
                        data: keluar, 
                        backgroundColor: '#FF5252', 
                        borderRadius: 4,
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 10 },
                            formatter: function(value) {
                                return value > 0 ? value : '';
                            }
                        }
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#ffffff', font: { weight: 'bold', size: 11 } } },
                    datalabels: {
                        display: false
                    }
                },
                scales: {
                    y: { 
                        type: 'logarithmic',
                        beginAtZero: true,
                        grace: '15%',  // Add 15% padding at top so bars don't touch the edge
                        ticks: { 
                            color: '#888888', 
                            font: { size: 10 },
                            callback: function(value, index, values) {
                                // Only show major tick marks (1, 10, 100, 1000, etc.)
                                if (value === 0) return '0';
                                if (value === 1) return '1';
                                // Show powers of 10 and their multiples
                                const logValue = Math.log10(value);
                                if (logValue === Math.floor(logValue)) {
                                    return value;
                                }
                                return null;
                            }
                        }, 
                        grid: { display: false } 
                    },
                    x: { 
                        ticks: { 
                            color: '#888888', 
                            font: { size: 9 }, 
                            maxRotation: 45, 
                            minRotation: 0,
                            autoSkip: false
                        }, 
                        grid: { display: false } 
                    }
                }
            }
        });
    }

    async function changePeriod(period, btn) {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active-btn', 'text-white'));
        if(btn) btn.classList.add('active-btn', 'text-white');
        
        document.getElementById('chartLoader').classList.remove('hidden');
        try {
            const res = await fetch(`{{ route('admin.dashboard.chart-data') }}?period=${period}`);
            serverData = await res.json();
            const type = document.getElementById('dataType').value;
            renderChart(serverData.labels, serverData[type].masuk, serverData[type].keluar);
        } finally {
            document.getElementById('chartLoader').classList.add('hidden');
        }
    }

    function refreshChartData() {
        const type = document.getElementById('dataType').value;
        if (serverData[type]) renderChart(serverData.labels, serverData[type].masuk, serverData[type].keluar);
    }

    document.addEventListener('DOMContentLoaded', () => changePeriod('daily', document.querySelector('.period-btn')));
</script>
@endpush
@endsection