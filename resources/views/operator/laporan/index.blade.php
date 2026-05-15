@extends('layouts.operator_app')

@section('content')
<div class="p-6 pb-24 safe-top">
    {{-- BAGIAN FILTER --}}
    <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-4 mb-6">
        <form action="{{ route('operator.laporan.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-2 gap-2">
                <input type="date" name="from" value="{{ $fromDate }}" class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-xs text-white">
                <input type="date" name="to" value="{{ $toDate }}" class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div class="flex gap-2">
                <select name="kategori" class="flex-1 bg-black/20 border border-white/10 rounded-xl px-3 py-2 text-xs text-white">
                    <option value="semua" {{ $kategori == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="produk" {{ $kategori == 'produk' ? 'selected' : '' }}>Hanya Produk</option>
                    <option value="bahan" {{ $kategori == 'bahan' ? 'selected' : '' }}>Hanya Bahan</option>
                </select>
                <button type="submit" class="bg-blue-600 px-4 py-2 rounded-xl text-xs font-bold text-white">Filter</button>
            </div>
        </form>
        
        <a href="{{ route('operator.laporan.cetak', ['from' => $fromDate, 'to' => $toDate, 'kategori' => $kategori]) }}" 
   class="bg-emerald-600 ...">
   Cetak Laporan
</a>
    </div>

    {{-- ALERT STOK KRITIS --}}
    @if($stokKritis->count() > 0)
    <div class="mb-6 bg-red-500/10 border border-red-500/20 p-4 rounded-2xl">
        <h3 class="text-red-500 text-sm font-bold flex items-center gap-2 mb-3">
            <i class="fas fa-exclamation-triangle"></i> {{ $stokKritis->count() }} Bahan Perlu Re-stock!
        </h3>
        <div class="flex gap-3 overflow-x-auto no-scrollbar">
            @foreach($stokKritis as $bahan)
            <div class="bg-[#1C222D] p-3 rounded-xl border border-red-500/30 min-w-[150px]">
                <p class="text-[10px] text-gray-400 truncate">{{ $bahan->nama_material }}</p>
                <p class="text-xs font-bold text-white">Sisa: <span class="text-red-400">{{ (int)$bahan->stok }}</span></p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- LIST RIWAYAT --}}
    <div class="space-y-4">
        @forelse($riwayat as $item)
        <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-4 flex gap-4 items-center">
            {{-- FOTO ASLI ITEM --}}
            <div class="w-16 h-16 rounded-xl overflow-hidden bg-black/40 border border-white/10 flex-shrink-0">
                @if($item->foto_item)
                    <img src="{{ asset('storage/' . $item->foto_item) }}" class="w-full h-full object-cover" alt="foto">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-700">
                        <i class="fas {{ $item->jenis == 'produk' ? 'fa-box' : 'fa-scroll' }} text-2xl"></i>
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start">
                    <h4 class="text-sm font-bold text-white truncate mr-2">{{ $item->nama }}</h4>
                    <span class="text-[9px] text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_terakhir)->diffForHumans() }}</span>
                </div>
                
                <div class="flex flex-wrap gap-2 mt-1">
                    <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase {{ $item->jenis == 'produk' ? 'bg-blue-500/20 text-blue-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                        {{ $item->jenis }}
                    </span>
                    @if($item->warna)
                    <span class="text-[10px] text-gray-400 flex items-center gap-1">
                        <i class="fas fa-palette text-[8px]"></i> {{ $item->warna }}
                    </span>
                    @endif
                </div>

                <p class="text-xs text-gray-500 mt-2">
                    Total: <span class="text-white font-bold">{{ number_format($item->total_qty) }}</span> {{ $item->satuan_item ?? 'unit' }}
                </p>
                <p class="text-[9px] text-gray-600 italic mt-1 font-medium">
                    <i class="fas fa-user-edit mr-1"></i> Admin: {{ $item->nama_operator ?? 'Sistem' }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center py-10 opacity-30">
            <i class="fas fa-folder-open text-5xl mb-3"></i>
            <p class="text-sm">Data tidak ditemukan</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $riwayat->links() }}
    </div>
</div>
@endsection