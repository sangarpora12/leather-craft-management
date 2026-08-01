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
        
        {{-- Kartu 1: Total Bahan --}}
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl hover:border-[#00BFA5]/50 transition-colors">
            <p class="text-[10px] font-bold text-[#00BFA5] uppercase tracking-widest">Total Bahan</p>
            <div class="mt-2 text-xl font-extrabold text-[#00BFA5]">{{ number_format($totalStokBahan, 0, ',', '.') }} Item</div>
            <p class="mt-1 text-[11px] text-gray-200">Total Bahan</p>
        </div>

        {{-- Kartu 2: Stok Produk Jadi --}}
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl hover:border-[#B388FF]/50 transition-colors">
            <p class="text-[10px] font-bold text-[#B388FF] uppercase tracking-widest">Total Produk Jadi</p>
            <div class="mt-2 text-xl font-extrabold text-[#B388FF]">{{ number_format($totalProduk ?? 0, 0, ',', '.') }} Model</div>
            <p class="mt-1 text-[11px] text-gray-200">Total Poduk</p>
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
                {{ $kritisCount }} Bahan
            </div>

            <p class="mt-2 text-[11px] font-medium">
                @if($showAlert)
                    <span class="text-red-100">⚠️ Segera restock!</span>
                @elseif($isNotifAktif === false)
                    <span class="text-gray-500">Peringatan dimatikan (Monitor OFF)</span>
                @else
                    <span class="text-gray-500">Status Aman</span>
                @endif
            </p>
        </div>

        {{-- Kartu 4: Produk Jadi Kritis --}}
        @php
            $stokMinProduk = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_produk')->value('nilai') ?? 2);
            $produkKritisCount = (int) $jumlahProdukKritis ?? 0;
            $isProdukKritis = $produkKritisCount > 0;
        @endphp
        <div class="bg-[#1A1C24] {{ $isProdukKritis ? 'border-red-500/50 border-l-4 border-l-red-500' : 'border-[#2D3139]' }} rounded-2xl p-6 shadow-xl transition-all">
            <p class="text-[10px] font-bold {{ $isProdukKritis ? 'text-red-500' : 'text-[#FFD740]' }} uppercase tracking-widest">
                Produk Jadi Kritis
            </p>
            <div class="mt-2 text-xl font-extrabold {{ $isProdukKritis ? 'text-red-500' : 'text-[#FFD740]' }}">
                {{ $produkKritisCount }} Model
            </div>
            <p class="mt-2 text-[11px] font-medium">
                @if($isProdukKritis)
                    <span class="text-red-500">⚠️ Stok ≤ {{ $stokMinProduk }} pcs</span>
                @else
                    <span class="text-gray-500"></span>
                @endif
            </p>
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
            <h3 class="text-white font-bold mb-4 flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-[#00BFA5] rounded-full animate-pulse"></span> Produksi Berjalan
                </div>
                <div class="flex gap-1">
                    <span class="text-[10px] px-2 py-1 rounded bg-[#00BFA5]/20 text-[#00BFA5] border border-[#00BFA5]/30">
                        {{ ($produksiAktif ?? collect())->sum('jumlah_spk') }} SPK
                    </span>
                    <span class="text-[10px] px-2 py-1 rounded bg-orange-500/20 text-orange-400 border border-orange-500/30">
                        {{ number_format(($produksiAktif ?? collect())->sum('total_qty')) }} Pcs
                    </span>
                </div>
            </h3>
            <div class="space-y-3">
                @forelse(($produksiAktif ?? []) as $prod)
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <div class="text-sm">
                            @if(isset($prod->product->kode_produk))
                            <span class="text-xs text-[#FF6B00] font-mono mr-2">[{{ $prod->product->kode_produk }}]</span>
                            @endif
                            <span class="font-bold text-white">{{ $prod->product->nama_produk ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[10px] text-gray-400">{{ number_format($prod->total_qty) }} Pcs</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-[#00BFA5]/20 text-[#00BFA5] font-bold border border-[#00BFA5]/30">{{ $prod->jumlah_spk }} SPK</span>
                        </div>
                        <div class="mt-2 text-[10px] text-gray-500">Status: {{ strtoupper($prod->statuses) }}</div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500 text-xs italic">Belum ada aktivitas produksi.</div>
                @endforelse
                
                <a href="{{ route('produksi.create') }}" class="w-full flex items-center justify-center gap-2 bg-[#FD730D] hover:bg-[#E6670B] text-white font-bold text-xs py-4 rounded-xl transition mt-4 active:scale-95 shadow-lg shadow-orange-950/20">
                    <i class="fa-solid fa-plus text-xs"></i> BUAT SPK BARU
                </a>
            </div>
        </div>
    </div>

    {{-- E-COMMERCE: PESANAN BARU MASUK --}}
    @php
        $pendingOrders = \App\Models\Order::where('status', 'pending')->latest()->limit(5)->get();
        $pendingCount = \App\Models\Order::where('status', 'pending')->count();
        $totalRevenue = \App\Models\Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalOrders = \App\Models\Order::count();
    @endphp
    <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
        <h3 class="text-white font-bold mb-1 flex items-center gap-2 text-sm">
            <span class="w-2 h-2 bg-[#FF6B00] rounded-full animate-pulse"></span> E-Commerce: Pesanan Baru Masuk
        </h3>
        <p class="text-xs text-gray-400 mb-4">Pesanan dari konsumen yang menunggu konfirmasi admin.</p>
        
        {{-- Summary Cards --}}
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-orange-500/10 border border-orange-500/20 rounded-lg p-3 text-center">
                <p class="text-[10px] text-orange-400 font-bold uppercase">Pending</p>
                <p class="text-lg font-extrabold text-orange-400">{{ $pendingCount }}</p>
            </div>
            <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3 text-center">
                <p class="text-[10px] text-green-400 font-bold uppercase">Total Order</p>
                <p class="text-lg font-extrabold text-green-400">{{ $totalOrders }}</p>
            </div>
            <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3 text-center">
                <p class="text-[10px] text-blue-400 font-bold uppercase">Pendapatan</p>
                <p class="text-sm font-extrabold text-blue-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[10px] text-gray-500 uppercase">
                    <tr>
                        <th class="text-left py-2 px-3">No. Pesanan</th>
                        <th class="text-left py-2 px-3">Pelanggan</th>
                        <th class="text-right py-2 px-3">Total</th>
                        <th class="text-center py-2 px-3">Status</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139]">
                    @forelse($pendingOrders as $order)
                    <tr>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <span class="text-white font-mono text-xs">{{ $order->order_number }}</span>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <div class="text-white font-medium">{{ $order->shipping_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->shipping_phone ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-3 text-right whitespace-nowrap">
                            <span class="text-green-400 font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">PENDING</span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <a href="{{ route('admin.penjualan.show', $order->id) }}" class="text-blue-400 hover:text-blue-300 text-xs font-bold">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-check-circle text-3xl text-emerald-500 mb-2"></i>
                                <p class="font-semibold text-gray-300">Tidak ada pesanan pending.</p>
                                <p class="text-xs text-gray-500 mt-1">Semua pesanan sudah diproses.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pendingCount > 0)
        <a href="{{ route('admin.penjualan.index', ['status' => 'pending']) }}" class="mt-4 w-full flex items-center justify-center gap-2 bg-[#FF6B00] hover:bg-[#E55B00] text-white font-bold text-xs py-3 rounded-xl transition shadow-lg">
            <i class="fa-solid fa-store text-xs"></i> LIHAT SEMUA PESANAN PENDING ({{ $pendingCount }})
        </a>
        @endif
    </div>

    {{-- PENGADAAN BAHAN YANG PENDING --}}
    <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
        <h3 class="text-white font-bold mb-1 flex items-center gap-2 text-sm">
            <span class="w-2 h-2 bg-[#FFD740] rounded-full animate-pulse"></span> Daftar Pengadaan Belum Dikerjakan
        </h3>
        <p class="text-xs text-gray-400 mb-4">Tugas pembelian bahan yang masih menunggu untuk diproses oleh operator.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[10px] text-gray-500 uppercase">
                    <tr>
                        <th class="text-left py-2 px-3">Bahan</th>
                        <th class="text-left py-2 px-3">Jumlah</th>
                        <th class="text-left py-2 px-3">Operator</th>
                        <th class="text-left py-2 px-3">Tgl. Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139]">
                    @forelse($pengadaanPendingIndividual as $item)
                    <tr>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <img class="h-10 w-10 rounded-lg object-cover flex-shrink-0" src="{{ $item->bahan && $item->bahan->foto ? asset('storage/' . $item->bahan->foto) : asset('images/no-image.png') }}" alt="">
                                <div>
                                    <div class="font-medium text-white">{{ $item->bahan ? $item->bahan->nama_material : 'Bahan tidak tersedia' }}</div>
                                    {{-- Ganti bagian warna dengan kode aman ini --}}
                                    <p class="text-xs text-gray-500">
    @php
        $warna = $item->warna ?? ($item->bahan->warna ?? null);
        $isSampah = in_array(strtolower(trim($warna)), ['polos', 'n/a', 'null', '-', '']);
    @endphp

    @if($warna && !$isSampah && strtolower(trim($warna)) !== 'tidak ada warna')
        {{ $warna }}
    @else
        <span class="opacity-50">Tidak Ada Warna</span>
    @endif
</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <div class="text-white font-bold">{{ rtrim(rtrim(number_format($item->jumlah, 2, '.', ''), '0'), '.') }}</div>
                            <div class="text-xs text-gray-400">{{ $item->bahan ? $item->bahan->satuan : 'N/A' }}</div>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap text-gray-300">
                            {{ $item->operator->name ?? 'Belum Ditugaskan' }}
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap text-gray-400 text-xs">
                            {{ $item->created_at->isoFormat('D MMM YYYY') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-check-circle text-3xl text-emerald-500 mb-2"></i>
                                <p class="font-semibold text-gray-300">Tidak ada pengadaan yang pending.</p>
                                <p class="text-xs text-gray-500 mt-1">Semua tugas pembelian sudah dikerjakan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
                             type: 'linear',
                             beginAtZero: true,
                             grace: '35%',  // Add 35% padding at top for high values
                        ticks: { 
                            color: '#888888', 
                            font: { size: 10 },
                            precision: 0
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
            refreshChartData();
        } finally {
            document.getElementById('chartLoader').classList.add('hidden');
        }
    }

    function refreshChartData() {
        const type = document.getElementById('dataType').value;
        const labels = serverData.labels ?? [];
        const dataForType = serverData[type] ?? { masuk: [], keluar: [] };

        // Fallback: if data is empty, create an array of zeros with the same length as labels
        const masukData = (dataForType.masuk && dataForType.masuk.length > 0) ? dataForType.masuk : new Array(labels.length).fill(0);
        const keluarData = (dataForType.keluar && dataForType.keluar.length > 0) ? dataForType.keluar : new Array(labels.length).fill(0);

        renderChart(labels, masukData, keluarData);
    }

    document.addEventListener('DOMContentLoaded', () => changePeriod('daily', document.querySelector('.period-btn')));
</script>
@endpush
@endsection