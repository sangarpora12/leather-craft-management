@extends('layouts.admin')

@section('page_title', 'Kinerja Operator')

@section('content')
<div class="min-h-screen bg-[#0B0E14] px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Halaman -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <img class="h-16 w-16 rounded-full object-cover" src="{{ $operator->profile_photo_url }}" alt="{{ $operator->name }}">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $operator->name }}</h1>
                    <p class="text-sm text-gray-400">Kinerja & Riwayat Aktivitas</p>
                </div>
            </div>
            <a href="{{ route('admin.pekerja-master.index') }}" class="inline-flex items-center gap-x-2 rounded-md bg-gray-700 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-500 transition-colors">
                <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Grid Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <!-- STAT 1: Total Produksi Bulan Ini -->
        <div class="bg-[#1C222D] rounded-xl border border-white/10 p-6 shadow-lg">
            <h5 class="text-sm text-gray-400 font-medium uppercase tracking-wider">Produk Bulan Ini</h5>
            <div class="mt-3 flex items-end justify-between">
                <span class="text-4xl font-bold text-purple-400 leading-none">{{ number_format($totalProdukBulanIni, 0, ',', '.') }}</span>
                <span class="text-base text-gray-500">Pcs</span>
            </div>
        </div>
        <!-- STAT 2: Total Bahan Diinput (Global) -->
        <div class="bg-[#1C222D] rounded-xl border border-white/10 p-6 shadow-lg">
            <h5 class="text-sm text-gray-400 font-medium uppercase tracking-wider">Total Bahan Diinput</h5>
            <div class="mt-3 flex items-end justify-between">
                <span class="text-4xl font-bold text-cyan-400 leading-none">{{ number_format($totalBahanDiinput, 0, ',', '.') }}</span>
                <span class="text-base text-gray-500">Item</span>
            </div>
        </div>
    </div>

    <!-- Area Konten Utama (Tab & Tabel) -->
    <div class="bg-[#1C222D] rounded-xl border border-white/10 shadow-lg p-6" x-data="{ tab: 'produk' }">
        
        <!-- Header Konten: Judul, Filter -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-white">Riwayat Aktivitas</h2>
                <p class="text-sm text-gray-400 mt-1">Tinjau riwayat produk selesai dan input bahan.</p>
            </div>
            <form action="{{ route('admin.pekerja-master.kinerja', $operator->id) }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="bg-[#2A313C] border border-white/10 text-white rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                <button type="submit" class="inline-flex items-center justify-center h-9 px-4 bg-purple-600 hover:bg-purple-700 rounded-lg text-white text-sm font-semibold transition-colors">Filter</button>
                <a href="{{ route('admin.pekerja-master.kinerja', $operator->id) }}" class="inline-flex items-center justify-center h-9 px-4 bg-gray-600 hover:bg-gray-700 rounded-lg text-white text-sm font-semibold transition-colors">Reset</a>
            </form>
        </div>

        <!-- Tombol Tab -->
        <div class="border-b border-white/10 mb-6">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button @click="tab = 'produk'" 
                        :class="{ 'border-purple-400 text-white': tab === 'produk', 'border-transparent text-gray-400 hover:text-white hover:border-gray-500': tab !== 'produk' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    📦 Riwayat Produk Selesai
                </button>
                <button @click="tab = 'bahan'" 
                        :class="{ 'border-purple-400 text-white': tab === 'bahan', 'border-transparent text-gray-400 hover:text-white hover:border-gray-500': tab !== 'bahan' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors">
                    📥 Riwayat Input Bahan
                </button>
            </nav>
        </div>

        <!-- Konten Tab -->
        <div>
            <!-- TABEL PRODUK SELESAI -->
            <div x-show="tab === 'produk'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Kode Produk</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Nama Produk</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Jumlah</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Tanggal Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($produkSelesaiTerbaru as $produk)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-mono text-gray-300 sm:pl-6">{{ $produk->kode_produk }}</td>
                                    <td class="whitespace-normal break-words px-3 py-4 text-sm font-medium text-white">{{ $produk->nama_produk }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ $produk->jumlah_selesai }} Pcs</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ $produk->created_at ? \Carbon\Carbon::parse($produk->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="mt-2 text-sm">Tidak ada data produk selesai untuk ditampilkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL BAHAN MASUK -->
            <div x-show="tab === 'bahan'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Kode Bahan</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Nama Bahan</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Jumlah</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($bahanTerbaru as $bahan)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-mono text-gray-300 sm:pl-6">{{ $bahan->kode_bahan }}</td>
                                    <td class="whitespace-normal break-words px-3 py-4 text-sm font-medium text-white">{{ $bahan->nama_material }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ number_format($bahan->jumlah, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ $bahan->created_at ? \Carbon\Carbon::parse($bahan->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="mt-2 text-sm">Tidak ada data bahan masuk untuk ditampilkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection