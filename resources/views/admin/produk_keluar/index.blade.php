@extends('layouts.admin')

@section('page_title', 'Riwayat Produk Keluar')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Riwayat Pengurangan Stok</h2>
        <a href="{{ route('admin.pakai-produk.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
            <i class="fa-solid fa-plus mr-2"></i>
            Kurangi Stok Baru
        </a>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-[#1A1C24] border border-[#343842] rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-400">
                <thead class="text-xs text-gray-300 uppercase bg-[#2D3139]">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Jumlah</th>
                        <th scope="col" class="px-6 py-3">Alasan</th>
                        <th scope="col" class="px-6 py-3">Dicatat oleh</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksis as $transaksi)
                        <tr class="bg-[#1A1C24] border-b border-[#343842] hover:bg-[#252830]">
                            <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                                {{ $transaksi->tanggal_keluar->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($transaksi->produk_jadi && $transaksi->produk_jadi->foto)
                                        <img src="{{ asset('storage/' . $transaksi->produk_jadi->foto) }}" 
                                             alt="{{ $transaksi->produk_jadi->nama_produk }}" 
                                             class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-[#2D3139] flex items-center justify-center">
                                            <i class="fa-solid fa-image text-gray-600 text-xs"></i>
                                        </div>
                                    @endif
                                    <span>{{ $transaksi->produk_jadi->nama_produk ?? 'Produk tidak ditemukan' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-red-400">
                                -{{ $transaksi->jumlah_keluar }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $transaksi->alasan }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $transaksi->user->name ?? 'Sistem' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.pakai-produk.destroy', $transaksi->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus riwayat ini? Stok produk akan dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <p class="text-gray-500">Belum ada riwayat pengurangan stok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($transaksis->hasPages())
        <div class="mt-8">
            {{ $transaksis->links() }}
        </div>
    @endif

</div>
@endsection