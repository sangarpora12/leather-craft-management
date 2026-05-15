@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-zinc-900 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-zinc-900 p-6 border-b border-zinc-800">
            <form method="GET" action="{{ route('admin.laporan.lengkap') }}" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-gray-500 text-xs mb-2">Tipe Laporan</label>
                    <select name="tipe" class="w-full bg-neutral-900 border border-zinc-800 rounded-lg px-3 py-2 text-white text-sm">
                        <option value="harian" {{ $tipe == 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="bulanan" {{ $tipe == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                @if($tipe == 'harian')
                <div class="flex-1">
                    <label class="block text-gray-500 text-xs mb-2">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full bg-neutral-900 border border-zinc-800 rounded-lg px-3 py-2 text-white text-sm">
                </div>
                @else
                <div class="flex-1">
                    <label class="block text-gray-500 text-xs mb-2">Bulan</label>
                    <input type="month" name="bulan" value="{{ $bulan }}" class="w-full bg-neutral-900 border border-zinc-800 rounded-lg px-3 py-2 text-white text-sm">
                </div>
                @endif
                <div class="flex-1">
                    <label class="block text-gray-500 text-xs mb-2">Mode Laporan</label>
                    <select name="mode" class="w-full bg-neutral-900 border border-zinc-800 rounded-lg px-3 py-2 text-white text-sm">
                        <option value="lengkap" {{ $mode == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                        <option value="produk" {{ $mode == 'produk' ? 'selected' : '' }}>Produk</option>
                        <option value="bahan" {{ $mode == 'bahan' ? 'selected' : '' }}>Bahan</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Terapkan</button>
                </div>

            </form>
        </div>

        <div class="p-6">
            <div class="bg-zinc-900/50 border-b border-zinc-800 p-4 rounded-lg mb-6">
                <h2 class="text-xl font-bold text-white">Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</h2>
            </div>

            <!-- Mode LENGKAP (Default) -->
            @if($mode == 'lengkap')
            <!-- Output Produksi -->
            <div class="mb-8 Print-section" data-mode="produksi">
                <h3 class="text-white text-lg font-bold mb-4">Output Produksi</h3>
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tanggal Selesai</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Produk Jadi</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Jumlah</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Biaya/Unit</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($productions as $prod)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($prod->tanggal)->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-white">{{ $prod->nama_produk ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300">{{ $prod->operators ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300 text-right">{{ $prod->total_selesai ?? 0 }} Pcs</td>
                                <td class="px-4 py-2 text-sm text-gray-300 text-right">Rp {{ number_format($prod->harga_jual ?? 0, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-white text-right">Rp {{ number_format(($prod->total_selesai ?? 0) * ($prod->harga_jual ?? 0), 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data produksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Penjualan -->
            <div class="mb-8 Print-section" data-mode="penjualan">
                <h3 class="text-white text-lg font-bold mb-4">Riwayat Penjualan / Barang Keluar</h3>
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tanggal Keluar</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Nama Produk</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Total Terjual</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Harga Jual</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($salesHistory as $sale)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($sale->tanggal)->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-white">{{ $sale->nama_produk }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300">-</td>
                                <td class="px-4 py-2 text-sm text-gray-300 text-right">{{ number_format($sale->total_terjual) }} Pcs</td>
                                <td class="px-4 py-2 text-sm text-gray-300 text-right">Rp {{ number_format($sale->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-green-400 text-right">Rp {{ number_format($sale->total_pendapatan, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data penjualan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Penggunaan Bahan Baku -->
            <div class="mb-8 Print-section" data-mode="bahan">
                <h3 class="text-white text-lg font-bold mb-4">Penggunaan Bahan Baku</h3>
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Nama Material</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Jumlah Terpakai</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500">Satuan</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($materialUsage as $supplier => $materials)
                                @foreach($materials as $mat)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($mat->tanggal)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-white">{{ $mat->nama_material }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-300">-</td>
                                    <td class="px-4 py-2 text-sm text-gray-300 text-right">{{ floatval($mat->total_quantity ?? 0) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-300 text-center">{{ $mat->satuan }}</td>
                                    <td class="px-4 py-2 text-sm font-bold text-orange-400 text-right">Rp {{ number_format($mat->total_cost ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data penggunaan bahan baku</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pembelian -->
            <div class="mb-8 Print-section" data-mode="pembelian">
                <h3 class="text-white text-lg font-bold mb-4">Pembelian / Bahan Baku Masuk</h3>
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tanggal Masuk</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Nama Material</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Jumlah Masuk</th>
                                <th class="px-4 py-2 text-center text-xs font-bold text-gray-500">Satuan</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($materialPurchases as $purchase)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($purchase->tanggal)->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm text-white">{{ $purchase->nama_material }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300">{{ $purchase->user_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300 text-right">{{ floatval($purchase->jumlah_masuk) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300 text-center">{{ $purchase->satuan }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-purple-400 text-right">Rp {{ number_format($purchase->total_biaya ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data pembelian</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ringkasan Keuangan -->
            <div class="mb-8 Print-section" data-mode="ringkasan">
                <h3 class="text-white text-lg font-bold mb-4">Ringkasan Keuangan</h3>
                <div class="flex flex-wrap gap-4">
                    <div class="bg-zinc-900/50 rounded-lg px-4 py-3 border border-zinc-800 flex-1 min-w-[200px]">
                        <p class="text-gray-400 text-xs">Total Pemasukan (Penjualan)</p>
                        <p class="text-xl font-bold text-white">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-zinc-900/50 rounded-lg px-4 py-3 border border-zinc-800 flex-1 min-w-[200px]">
                        <p class="text-gray-400 text-xs">Total Pengeluaran (Bahan Baku)</p>
                        <p class="text-xl font-bold text-white">Rp {{ number_format($totalPurchaseCost ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-zinc-900/50 rounded-lg px-4 py-3 border border-zinc-800 flex-1 min-w-[200px]">
                        <p class="text-gray-400 text-xs">Keuntungan Bersih</p>
                        <p class="text-xl font-bold {{ (($totalIncome ?? 0) - ($totalPurchaseCost ?? 0)) >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            Rp {{ number_format(($totalIncome ?? 0) - ($totalPurchaseCost ?? 0), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Mode PRODUK -->
            @if($mode == 'produk')
            <div class="mb-8 Print-section" data-mode="produk">
                <h3 class="text-white text-lg font-bold mb-4">Riwayat Produk</h3>
                <p class="text-gray-400 text-sm mb-4">Mutasi produk baru & produk terjual</p>
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Produk</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Jenis Mutasi</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Jumlah</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Nilai (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($productions as $prod)
                            <!-- Produksi/Input -->
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($prod->tanggal)->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm font-bold text-white">{{ $prod->nama_produk ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300">{{ $prod->operators ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded text-xs font-medium">Tambah</span>
                                </td>
                                <td class="px-4 py-2 text-sm text-white font-bold text-right">+{{ $prod->total_selesai ?? 0 }}</td>
                                <td class="px-4 py-2 text-sm text-emerald-400 text-right">Rp {{ number_format(($prod->total_selesai ?? 0) * ($prod->harga_jual ?? 0), 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data produk</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Mode BAHAN -->
            @if($mode == 'bahan')
            <div class="mb-8 Print-section" data-mode="bahan">
                <h3 class="text-white text-lg font-bold mb-4">Riwayat Stok Bahan</h3>
                <p class="text-gray-400 text-sm mb-4">Bahan masuk & pemakaian untuk produksi (SPK)</p>
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <table class="w-full">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Bahan</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">Jenis Mutasi</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-500">No. SPK</th>
                                <th class="px-4 py-2 text-right text-xs font-bold text-gray-500">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($materialPurchases as $purchase)
                            <!-- Bahan Masuk/Restock -->
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($purchase->tanggal)->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm text-white">{{ $purchase->nama_material }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300">{{ $purchase->user_name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="bg-green-500/20 text-green-400 px-2 py-0.5 rounded text-xs font-medium">Bahan Baru</span>
                                </td>
                                <td class="px-4 py-2 text-sm text-white font-bold text-right">+{{ floatval($purchase->jumlah_masuk) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-300">-</td>
                                <td class="px-4 py-2 text-sm text-emerald-400 text-right">Rp {{ number_format($purchase->total_biaya ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            @endforelse
                            
                            @forelse($materialUsage as $supplier => $materials)
                                @foreach($materials as $mat)
                                <!-- Pemakaian untuk Produksi/SPK -->
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-400">{{ \Carbon\Carbon::parse($mat->tanggal)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-white">{{ $mat->nama_material }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-300">-</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="bg-orange-500/20 text-orange-400 px-2 py-0.5 rounded text-xs font-medium">SPK</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-red-400 font-bold text-right">-{{ floatval($mat->total_quantity ?? 0) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-300">-</td>
                                    <td class="px-4 py-2 text-sm text-orange-400 text-right">Rp {{ number_format($mat->total_cost ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">Belum ada data bahan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection