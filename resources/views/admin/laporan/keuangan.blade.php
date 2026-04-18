@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">Laporan Keuangan</h2>
            <p class="text-gray-400 text-sm mt-1">Ringkasan transaksi masuk dan keluar PT. Berliano</p>
        </div>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg transition-all flex items-center gap-2">
            <i class="fas fa-file-export"></i> Cetak Laporan
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-2xl shadow-xl">
            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Total Estimasi Omzet</p>
            <h3 class="text-2xl font-black text-emerald-500">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-2xl shadow-xl">
            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Total Transaksi</p>
            <h3 class="text-2xl font-black text-blue-500">{{ $transaksi->total() }} <span class="text-sm font-normal text-gray-400">Pesanan</span></h3>
        </div>
        <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-2xl shadow-xl">
            <p class="text-gray-500 text-xs uppercase font-bold tracking-wider mb-1">Status Sistem</p>
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Terverifikasi
            </h3>
        </div>
    </div>

    <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl overflow-hidden shadow-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/5 text-gray-300 uppercase text-[10px] font-black tracking-widest">
                    <th class="p-4 border-b border-white/10">Tanggal</th>
                    <th class="p-4 border-b border-white/10">Produk Jadi</th>
                    <th class="p-4 border-b border-white/10 text-center">Qty</th>
                    <th class="p-4 border-b border-white/10">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-300 text-sm">
                @foreach($transaksi as $item)
                <tr class="hover:bg-white/5 transition-colors border-b border-white/5">
                    <td class="p-4 font-mono text-xs">{{ $item->created_at->format('d M Y') }}</td>
                    <td class="p-4 font-bold text-white">{{ $item->produk_jadi->nama_produk ?? 'Produk Dihapus' }}</td>
                    <td class="p-4 text-center font-bold text-emerald-400">{{ $item->jumlah_keluar }}</td>
                    <td class="p-4">
                        <span class="bg-emerald-500/10 text-emerald-500 text-[10px] px-2 py-1 rounded-full font-bold uppercase border border-emerald-500/20">
                            Selesai
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4 bg-white/2">
            {{ $transaksi->links() }}
        </div>
    </div>
</div>
@endsection