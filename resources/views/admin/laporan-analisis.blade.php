@extends('layouts.admin')
@section('title_header', 'Analisis Efisiensi')

@section('content')
<div class="space-y-6">

    {{-- HEADER & FILTER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-[#111] border border-white/10 rounded-xl p-4 gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Laporan & Analisis</h2>
            <p class="text-gray-400 text-xs mt-1">Pantau performa produksi & efisiensi tim secara real-time.</p>
        </div>
        
        <form action="{{ route('admin.laporan.analisis') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <select name="bulan" class="bg-black border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ Carbon\Carbon::createFromDate(null, $i, 1)->isoFormat('MMMM') }}</option>
                @endfor
            </select>
            <select name="tahun" class="bg-black border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none">
                @for($y = date('Y') - 2; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-white text-black px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                Terapkan Filter
            </button>
        </form>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Produksi --}}
        <div class="bg-[#111] p-5 rounded-xl border border-white/10 relative overflow-hidden group hover:border-blue-500/30 transition">
            <div class="absolute right-0 top-0 p-6 bg-blue-500/5 rounded-bl-full transition group-hover:bg-blue-500/10"></div>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Total Produksi</p>
            <h3 class="text-3xl font-black text-white">{{ number_format($totalProduksi) }} <span class="text-xs font-normal text-gray-500">pcs</span></h3>
            @if($produksiChange != 0)
            <span class="text-[10px] {{ $produksiChange > 0 ? 'text-emerald-400' : 'text-red-400' }} mt-2 block font-bold">
                {{ $produksiChange > 0 ? '↑' : '↓' }} {{ abs($produksiChange) }}% dari bulan lalu
            </span>
            @else
            <span class="text-[10px] text-gray-500 mt-2 block font-bold">-</span>
            @endif
        </div>

        {{-- Efisiensi Operator --}}
        <div class="bg-[#111] p-5 rounded-xl border border-white/10 relative overflow-hidden group hover:border-emerald-500/30 transition">
            <div class="absolute right-0 top-0 p-6 bg-emerald-500/5 rounded-bl-full transition group-hover:bg-emerald-500/10"></div>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Efisiensi Operator</p>
            <h3 class="text-3xl font-black text-white">{{ $avgEfficiency }}%</h3>
            <div class="w-full bg-gray-900 h-1.5 rounded-full mt-2">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $avgEfficiency }}%"></div>
            </div>
        </div>

        {{-- Biaya Overhead --}}
        <div class="bg-[#111] p-5 rounded-xl border border-white/10 relative overflow-hidden group hover:border-orange-500/30 transition">
            <div class="absolute right-0 top-0 p-6 bg-orange-500/5 rounded-bl-full transition group-hover:bg-orange-500/10"></div>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Biaya Overhead</p>
            <h3 class="text-3xl font-black text-white">Rp {{ number_format($totalBiayaOverhead / 1000000, 1) }}jt</h3>
            @if($overheadChange != 0)
            <span class="text-[10px] {{ $overheadChange < 0 ? 'text-emerald-400' : 'text-red-400' }} mt-2 block font-bold">
                {{ $overheadChange > 0 ? '↑' : '↓' }} {{ abs($overheadChange) }}% {{ $overheadChange < 0 ? '(Hemat)' : '' }}
            </span>
            @else
            <span class="text-[10px] text-gray-500 mt-2 block font-bold">-</span>
            @endif
        </div>

        {{-- Material Kritis --}}
        <div class="bg-[#111] p-5 rounded-xl border border-red-500/30 relative overflow-hidden group hover:border-red-500 transition">
            <div class="absolute right-0 top-0 p-6 bg-red-500/5 rounded-bl-full transition group-hover:bg-red-500/10"></div>
            <p class="text-[10px] text-red-400 uppercase font-bold tracking-wider mb-1">Material Kritis</p>
            <h3 class="text-3xl font-black text-white">{{ $jumlahMaterialKritis }} <span class="text-xs font-normal text-gray-500">item</span></h3>
            <a href="{{ route('admin.pengadaan.index') }}" class="text-[10px] text-white underline mt-2 block font-bold hover:text-red-400">Cek Stok</a>
        </div>
    </div>

    {{-- KONTEN UTAMA (Grafik & Ranking) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- GRAFIK PRODUKSI HARIAN --}}
        <div class="lg:col-span-2 bg-[#111] rounded-xl border border-white/10 p-6">
            <h3 class="font-bold text-white mb-6 text-sm uppercase tracking-wider">Grafik Produksi Harian (7 Hari Terakhir)</h3>
            
            {{-- Visualisasi Bar Chart CSS Murni --}}
            <div class="h-64 flex items-end justify-between gap-2 px-2 pb-2 border-b border-white/10">
                @foreach($chartData as $index => $h)
                @php
                    $heightPercent = $chartMax > 0 ? ($h / $chartMax) * 100 : 0;
                    if ($heightPercent < 5) $heightPercent = 5;
                @endphp
                <div class="w-full bg-blue-600/20 hover:bg-blue-500 rounded-t transition-all relative group" style="height: {{ $heightPercent }}%">
                    <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] opacity-0 group-hover:opacity-100 transition text-white font-bold">{{ $h }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-[10px] text-gray-500 font-mono">
                @foreach($chartLabels as $label)
                <span>{{ $label }}</span>
                @endforeach
            </div>

            {{-- Progress Status SPK --}}
            <div class="mt-8 space-y-4">
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Selesai</span><span class="font-bold text-emerald-400">{{ $percentSelesai }}% ({{ $spkSelesai }} SPK)</span></div>
                    <div class="w-full bg-black h-2 rounded-full overflow-hidden"><div class="bg-emerald-500 h-full" style="width: {{ $percentSelesai }}%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Proses</span><span class="font-bold text-blue-400">{{ $percentProses }}% ({{ $spkProses }} SPK)</span></div>
                    <div class="w-full bg-black h-2 rounded-full overflow-hidden"><div class="bg-blue-500 h-full" style="width: {{ $percentProses }}%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs mb-1"><span class="text-gray-400">Pending</span><span class="font-bold text-yellow-400">{{ $percentPending }}% ({{ $spkPending }} SPK)</span></div>
                    <div class="w-full bg-black h-2 rounded-full overflow-hidden"><div class="bg-yellow-500 h-full" style="width: {{ $percentPending }}%"></div></div>
                </div>
            </div>
        </div>

        {{-- RANKING OPERATOR --}}
        <div class="bg-[#111] rounded-xl border border-white/10 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-white text-sm uppercase tracking-wider">Top Performa ({{ Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM YYYY') }})</h3>
                <a href="{{ route('admin.operator.index') }}" class="text-[10px] font-bold text-orange-500 hover:text-orange-400 transition">Lihat Semua</a>
            </div>

            <div class="space-y-4">
                @forelse($operatorRanking as $index => $operator)
                @php
                    $rankClass = $index == 0 ? 'from-yellow-500/10 to-transparent border-yellow-500/20' : 'from-transparent to-transparent border-white/5';
                    $rankNumberClass = $index == 0 ? 'text-yellow-500' : ($index == 1 ? 'text-gray-400' : 'text-gray-600');
                    $initials = strtoupper(substr($operator->name, 0, 1));
                    $effClass = $operator->efficiency >= 90 ? 'text-yellow-500' : ($operator->efficiency >= 80 ? 'text-blue-400' : 'text-emerald-400');
                @endphp
                <div class="flex items-center gap-4 p-3 bg-gradient-to-r {{ $rankClass }} border rounded-xl relative">
                    <div class="absolute top-2 left-2 text-[10px] font-black {{ $rankNumberClass }}">#{{ $index + 1 }}</div>
                    <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center font-bold text-white border {{ $index == 0 ? 'border-yellow-500' : '' }}">{{ $initials }}</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-white text-xs">{{ $operator->name }}</h4>
                        <p class="text-[10px] text-gray-400">{{ number_format($operator->total_selesai) }} Pcs Selesai</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black {{ $effClass }}">{{ $operator->efficiency }}%</p>
                        <p class="text-[9px] text-gray-500">Efisiensi</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 text-xs">
                    <p>Belum ada data produksi bulan ini</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
