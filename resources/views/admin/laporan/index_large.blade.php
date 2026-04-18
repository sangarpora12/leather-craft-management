@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-zinc-900 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white">Laporan Operasional</h1>
                    <p class="text-gray-400 text-sm mt-1">Ringkasan aktivitas produksi dan gudang.</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M10.5 4.5V1.81055C10.5 1.61156 10.421 1.28016L9.21961 0.219609C9.07898 0.0585938 8.8882 0 8.68922 0H2.25C1.83586 0 1.5 0.335859 1.5 0.75V4.5C0.671484 4.5 0 5.17148 0 6V8.625C0 8.83219 0.167812 9 0.375 9H1.5V11.25C1.5 11.6641 1.83586 12 2.25 12H9.75C10.1641 12 10.5 11.6641 10.5 11.25V9H11.625C11.8322 9 12 8.83219 12 8.625V6C12 5.17148 11.3285 4.5 10.5 4.5ZM9 10.5H3V8.25H9V10.5ZM9 5.25H3V1.5H7.5V2.625C7.5 2.83219 7.66781 3 7.875 3H9V5.25ZM10.125 6.9375C9.81445 6.9375 9.5625 6.68555 9.5625 6.375C9.5625 6.06422 9.81445 5.8125 10.125 5.8125C10.4355 5.8125 10.6875 6.06422 10.6875 6.375C10.6875 6.68555 10.4355 6.9375 10.125 6.9375Z" fill="currentColor"/>
                        </svg>
                        Cetak Laporan
                    </button>
                    <a href="{{ route('admin.laporan.lengkap') }}" class="flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-gray-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.4369 0H0.563154C0.0635131 0 -0.188604 0.606234 0.165419 0.960258L4.5 5.29549V10.125C4.5 10.3085 4.58955 10.4805 4.73993 10.5858L6.61493 11.8979C6.98484 12.1568 7.5 11.8944 7.5 11.437V5.29549L11.8347 0.960258C12.188 0.606938 11.9376 0 11.4369 0Z" fill="currentColor"/>
                        </svg>
                        Laporan Lengkap
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-zinc-900 p-6 border-b border-zinc-800">
            <div class="flex items-center gap-2 mb-4">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M15.5 12H5V11.5C5 11.225 4.775 11 4.5 11H3.5C3.225 11 3 11.225 3 11.5V12H0.5C0.225 12 0 12.225 0 12.5V13.5C0 13.775 0.225 14 0.5 14H3V14.5C3 14.775 3.225 15 3.5 15H4.5C4.775 15 5 14.775 5 14.5V14H15.5C15.775 14 16 13.775 16 13.5V12.5C16 12.225 15.775 12 15.5 12Z" fill="#F97316"/>
                </svg>
                <h3 class="text-white text-xl font-bold">Filter Laporan</h3>
            </div>
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col lg:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-gray-500 text-xl font-semibold mb-4">Dari Tanggal & Jam</label>
                    <input type="datetime-local" name="from" value="{{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d\TH:i') }}" class="w-full bg-neutral-900 border-2 border-zinc-700 rounded-xl px-6 py-6 text-white text-xl font-medium h-20">
                </div>
                <div class="flex-1">
                    <label class="block text-gray-500 text-xl font-semibold mb-4">Sampai Tanggal & Jam</label>
                    <input type="datetime-local" name="to" value="{{ $to ? \Carbon\Carbon::parse($to)->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d\TH:i') }}" class="w-full bg-neutral-900 border-2 border-zinc-700 rounded-xl px-6 py-6 text-white text-xl font-medium h-20">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="flex items-center gap-3 bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-xl text-lg font-bold transition-colors h-20">
                        <svg width="16" height="16" viewBox="0 0 12 12" fill="none">
                            <path d="M11.4369 0H0.563154C0.0635131 0 -0.188604 0.606234 0.165419 0.960258L4.5 5.29549V10.125C4.5 10.3085 4.58955 10.4805 4.73993 10.5858L6.61493 11.8979C6.98484 12.1568 7.5 11.8944 7.5 11.437V5.29549L11.8347 0.960258C12.188 0.606938 11.9376 0 11.4369 0Z" fill="white"/>
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Total Output</p>
                            <p class="text-white text-2xl font-bold">{{ number_format($totalOutput) }}</p>
                            <p class="text-gray-500 text-xs">Unit Produksi</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Produksi Pending</p>
                            <p class="text-white text-2xl font-bold">{{ number_format($produksiPending) }}</p>
                            <p class="text-gray-500 text-xs">Unit Menunggu</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Barang Keluar</p>
                            <p class="text-white text-2xl font-bold">{{ number_format($barangKeluar) }}</p>
                            <p class="text-gray-500 text-xs">Unit Terjual</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Stok Kritis</p>
                            <p class="text-white text-2xl font-bold">{{ $stokKritisCount }}</p>
                            <p class="text-gray-500 text-xs">Bahan Baku</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-400 text-sm">Total Pengeluaran</p>
                            <p class="text-white text-2xl font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                            <p class="text-gray-500 text-xs">Biaya Bahan Baku</p>
                        </div>
                        <div class="w-12 h-12 bg-red-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
            <p class="text-gray-400 text-sm">Pemasukan</p>
            <p class="text-white text-2xl font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-gray-500 text-xs">Total Pemasukan</p>
                        </div>
                        <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800 mb-8">
                <h3 class="text-white text-lg font-bold mb-4">Grafik Produksi 7 Hari Terakhir</h3>
                <div class="h-64">
                    <canvas id="productionChart"></canvas>
                </div>
            </div>

            <!-- Recent Productions -->
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-8">
                <div class="px-6 py-4 border-b border-zinc-800">
                    <h3 class="text-white text-lg font-bold">Produksi Terbaru</h3>
                    <p class="text-gray-400 text-sm">10 produksi terakhir yang telah selesai</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kode SPK</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Operator</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($productions as $prod)
                                <tr class="hover:bg-zinc-900/30">
                                    <td class="px-6 py-4 text-sm text-gray-400 font-mono">{{ $prod->kode_spk }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-white">{{ $prod->produk_jadi->nama_produk ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-300">{{ $prod->operator->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-white">{{ $prod->total_qty }} Pcs</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            SELESAI
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-400">{{ $prod->last_update->format('d/m H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data produksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Material Usage Summary -->
            <div class="bg-zinc-900/50 rounded-lg p-6 border border-zinc-800">
                <h3 class="text-white text-lg font-bold mb-4">Penggunaan Bahan Baku</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @forelse($materialUsage as $supplier => $materials)
                        <div class="bg-neutral-900/50 rounded-lg p-4 border border-zinc-800">
                            <h4 class="text-gray-300 text-sm font-bold mb-3">{{ $supplier }}</h4>
                            <div class="space-y-2">
                                @foreach($materials as $mat)
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-400">{{ $mat->nama_material }}</span>
                                        <span class="text-white font-medium">{{ number_format($mat->total_quantity, 2) }} {{ $mat->satuan }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500 italic">
                            Belum ada data penggunaan bahan baku
                        </div>
                    @endforelse
                </div>
                @if($totalMaterialCost > 0)
                    <div class="mt-6 pt-6 border-t border-zinc-800">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Total Biaya Bahan Baku</span>
                            <span class="text-orange-500 text-xl font-bold">Rp {{ number_format($totalMaterialCost, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('productionChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Produksi Harian',
                data: @json($chartData),
                borderColor: '#F97316',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#9CA3AF'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#9CA3AF'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#FFFFFF'
                    }
                }
            }
        }
    });
</script>
@endsection
