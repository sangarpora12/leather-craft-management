<?php
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
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    

    
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl hover:border-[#00BFA5]/50 transition-colors">
            <p class="text-[10px] font-bold text-white uppercase tracking-widest">Total Aset Bahan</p>
            <div class="mt-2 text-xl font-extrabold text-[#00BFA5]"><?php echo e($kpiTotalAset); ?></div>
            <p class="mt-1 text-[11px] text-gray-400">Valuasi stok gudang</p>
        </div>

        
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl hover:border-[#B388FF]/50 transition-colors">
            <p class="text-[10px] font-bold text-white uppercase tracking-widest">Stok Produk Jadi</p>
            <div class="mt-2 text-xl font-extrabold text-[#B388FF]"><?php echo e(number_format($stokProduk ?? 0, 0, ',', '.')); ?> Pcs</div>
            <p class="mt-1 text-[11px] text-gray-200">Siap didistribusikan</p>
        </div>

        
        <?php
            $kritisCount = (int) ($jumlahKritis ?? 0);
            $showAlert = ($isNotifAktif === true) && ($kritisCount > 0);
        ?>
        <div class="bg-[#1A1C24] <?php echo e($showAlert ? 'border-red-500/50' : 'border-[#2D3139]'); ?> rounded-2xl p-6 shadow-xl border-l-4 <?php echo e($showAlert ? 'border-l-red-500' : 'border-l-zinc-700'); ?> transition-all">
            <p class="text-[10px] font-bold <?php echo e($showAlert ? 'text-red-500' : 'text-gray-400'); ?> uppercase tracking-widest">Bahan Baku Kritis</p>
            
            
            <div class="mt-2 text-xl font-extrabold <?php echo e($showAlert ? 'text-red-500' : 'text-white'); ?>">
                <?php echo e($kritisCount); ?> Item
            </div>

            <p class="mt-2 text-[11px] font-medium">
                <?php if($showAlert): ?>
                    <span class="text-red-500">⚠️ Segera restock!</span>
                <?php elseif($isNotifAktif === false): ?>
                    <span class="text-gray-500">Peringatan dimatikan (Monitor OFF)</span>
                <?php else: ?>
                    <span class="text-gray-500">Status Aman</span>
                <?php endif; ?>
            </p>
        </div>

        
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
            <p class="text-[10px] font-bold text-[#FFD740] uppercase tracking-widest">Produk Jadi Kritis</p>
            <div class="mt-2 text-xl font-extrabold text-[#FFD740]"><?php echo e($jumlahProdukKritis ?? 0); ?> Model</div>
            <p class="mt-1 text-[11px] text-gray-400">Stok menipis</p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
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

        
        <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2 text-sm">
                <span class="w-2 h-2 bg-[#00BFA5] rounded-full animate-pulse"></span> Produksi Berjalan
            </h3>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = ($produksiAktif ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <div class="text-sm font-bold text-white"><?php echo e($prod->product->nama_produk ?? 'Unknown'); ?></div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-[10px] text-gray-400"><?php echo e(number_format($prod->total_qty)); ?> Pcs</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-[#00BFA5]/20 text-[#00BFA5] font-bold border border-[#00BFA5]/30"><?php echo e($prod->jumlah_spk); ?> SPK</span>
                        </div>
                        <div class="mt-2 text-[10px] text-gray-500">Status: <?php echo e(strtoupper($prod->statuses)); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-10 text-gray-500 text-xs italic">Belum ada aktivitas produksi.</div>
                <?php endif; ?>
                
                <a href="<?php echo e(route('admin.produksi.create')); ?>" class="w-full flex items-center justify-center gap-2 bg-[#FD730D] hover:bg-[#E6670B] text-white font-bold text-xs py-4 rounded-xl transition mt-4 active:scale-95 shadow-lg shadow-orange-950/20">
                    <i class="fa-solid fa-plus text-xs"></i> BUAT SPK BARU
                </a>
            </div>
        </div>
    </div>

    
    <?php if(isset($pengadaanBahan) && $pengadaanBahan->count() > 0): ?>
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
                    <?php $__currentLoopData = $pengadaanBahan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengadaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="py-3 text-white font-medium"><?php echo e($pengadaan->nama_material); ?></td>
                        <td class="py-3 text-center text-[#00BFA5] font-bold"><?php echo e(number_format($pengadaan->jumlah)); ?> <?php echo e($pengadaan->satuan); ?></td>
                        <td class="py-3 text-center text-gray-400 text-xs"><?php echo e($pengadaan->supplier ?? '-'); ?></td>
                        <td class="py-3 text-center text-gray-500 text-xs"><?php echo e(\Carbon\Carbon::parse($pengadaan->created_at)->format('d M Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    .active-btn { background: #2D3139; color: white !important; }
</style>

<?php $__env->startPush('scripts'); ?>
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
            const res = await fetch(`<?php echo e(route('admin.dashboard.chart-data')); ?>?period=${period}`);
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>