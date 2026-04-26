@extends('layouts.admin')

@section('page_title', 'Kinerja Operator - ' . $operator->name)

@section('content')
<div class="min-h-screen bg-[#0B0E14] px-6 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('admin.operator.index') }}" class="text-gray-400 hover:text-white text-sm mb-3 inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Operator
        </a>
        <h1 class="text-2xl font-bold text-white">Detail Kinerja Operator</h1>
        <p class="text-gray-500 mt-1">Statistik dan riwayat aktifitas operator</p>
    </div>

    {{-- FILTER TANGGAL --}}
    <div class="mb-6 flex gap-3 flex-wrap">
        <a href="{{ route('admin.pekerja-master.kinerja', [$operator->id, 'filter' => 'all']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter == 'all' ? 'bg-blue-600 text-white' : 'bg-[#1C222D] text-gray-400 hover:text-white' }}">
            Semua Waktu
        </a>
        <a href="{{ route('admin.pekerja-master.kinerja', [$operator->id, 'filter' => 'today']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter == 'today' ? 'bg-blue-600 text-white' : 'bg-[#1C222D] text-gray-400 hover:text-white' }}">
            Hari Ini
        </a>
        <a href="{{ route('admin.pekerja-master.kinerja', [$operator->id, 'filter' => 'week']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter == 'week' ? 'bg-blue-600 text-white' : 'bg-[#1C222D] text-gray-400 hover:text-white' }}">
            Minggu Ini
        </a>
        <a href="{{ route('admin.pekerja-master.kinerja', [$operator->id, 'filter' => 'month']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filter == 'month' ? 'bg-blue-600 text-white' : 'bg-[#1C222D] text-gray-400 hover:text-white' }}">
            Bulan Ini
        </a>
    </div>

    {{-- KARTU PROFIL --}}
    <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-gray-700 shrink-0">
                @if($operator->foto_profil || $operator->foto)
                    <img src="{{ asset('storage/' . ($operator->foto_profil ?? $operator->foto)) }}" alt="{{ $operator->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                        {{ substr($operator->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $operator->name }}</h2>
                <p class="text-purple-400 font-medium">{{ $operator->jabatan ?? 'Staff Produksi' }}</p>
                <p class="text-gray-500 text-sm">{{ $operator->email }}</p>
            </div>
        </div>
    </div>

    {{-- STATISTIK RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- TOTAL INPUT BAHAN --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-box text-blue-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 uppercase tracking-wider">Total Input Bahan</p>
                    <p class="text-3xl font-bold text-white">{{ $totalInputBahan }}</p>
                </div>
            </div>
        </div>

        {{-- TOTAL PRODUK SELESAI --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-green-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-check text-green-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-[11px] text-gray-500 uppercase tracking-wider">Total Produk Selesai</p>
                    <p class="text-3xl font-bold text-white">{{ $totalProdukSelesai }} Pcs</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT AKTIFITAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- RIWAYAT INPUT BAHAN --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-4 border-b border-white/5">
                <h3 class="font-bold text-white">Riwayat Input Bahan Terakhir</h3>
            </div>
            <div class="max-h-[400px] overflow-y-auto">
                @if($riwayatBahan->count() > 0)
                <div class="divide-y divide-white/5">
                    @foreach($riwayatBahan as $bahan)
                    <div class="p-4">
                        <p class="text-white font-medium text-sm">{{ $bahan->nama_material }}</p>
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($bahan->created_at)->translatedFormat('d M Y H:i') }}</span>
                            <span class="text-blue-400">{{ (int) ($bahan->jumlah_bahan ?? $bahan->jumlah ?? $bahan->stok ?? 0) }} {{ $bahan->satuan ?? 'Unit' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center">
                    <i class="fa-solid fa-box-open text-gray-600 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada riwayat input bahan</p>
                </div>
                @endif
            </div>
        </div>

        {{-- RIWAYAT PRODUKSI --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 overflow-hidden">
            <div class="p-4 border-b border-white/5">
                <h3 class="font-bold text-white">Riwayat Produksi Terakhir</h3>
            </div>
            <div class="max-h-[400px] overflow-y-auto">
                @if($riwayatProduksi->count() > 0)
                <div class="divide-y divide-white/5">
                    @foreach($riwayatProduksi as $produksi)
                    <div class="p-4">
                        <p class="text-white font-medium text-sm">{{ $produksi->nama_produk ?? 'Produksi' }}</p>
                        @if($produksi->kode_produk)
                        <p class="text-gray-400 text-xs">{{ $produksi->kode_produk }}</p>
                        @endif
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-gray-500">{{ \Carbon\Carbon::parse($produksi->updated_at ?? $produksi->created_at)->translatedFormat('d M Y H:i') }}</span>
                            <span class="text-green-400">{{ (int) ($produksi->jumlah_produksi ?? 0) }} Pcs</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-8 text-center">
                    <i class="fa-solid fa-hammer text-gray-600 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada riwayat produksi</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection