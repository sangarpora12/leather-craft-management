@php
    $page = 'produksi';

    // Status mapping untuk tombol
    $isDraft = $produksi->status === 'DRAFT';
    $isProses = $produksi->status === 'PROSES';
    $isSelesai = $produksi->status === 'SELESAI';
    
    // Memastikan ada tanggal selesai untuk kalkulasi durasi
    $tanggal_selesai = $produksi->tanggal_selesai ? \Carbon\Carbon::parse($produksi->tanggal_selesai) : null;
    $tanggal_mulai = \Carbon\Carbon::parse($produksi->tanggal_mulai);

    $durasi_hari = 0;
    if ($tanggal_selesai) {
        $durasi_hari = $tanggal_mulai->diffInDays($tanggal_selesai) + 1; 
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail SPK: {{ $produksi->kode_spk }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> 
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }
        /* Dark Theme Status Classes (sesuai yang didefinisikan di ProduksiController) */
        .status-DRAFT { background-color: #92400e20; color: #fcd34d; }
        .status-PROSES { background-color: #3b82f620; color: #60a5fa; }
        .status-SELESAI { background-color: #05966920; color: #34d399; }
        .status-DIBATALKAN { background-color: #ef444420; color: #f87171; }
    </style>
</head>
<body class="bg-black text-white h-screen overflow-hidden flex">

    @include('components.sidebar')

    <main class="flex-1 flex flex-col relative overflow-hidden bg-black md:ml-64">
        
        <header class="h-20 bg-[#111111] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.produksi.index') }}" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-white">Detail SPK: {{ $produksi->kode_spk }}</h2>
                    <p class="text-gray-400 text-xs mt-0.5">Surat Perintah Kerja untuk produk {{ $produksi->product->nama_produk ?? 'Tidak Dikenal' }}.</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                @if ($isDraft)
                    <form method="POST" action="{{ route('admin.produksi.update_status', $produksi->id) }}" onsubmit="return confirm('Yakin ingin membatalkan SPK ini? Aksi ini tidak dapat dibatalkan.');">
                        @csrf
                        <input type="hidden" name="status" value="DIBATALKAN">
                        <button type="submit" class="bg-gray-600/20 hover:bg-red-600/50 text-red-400 px-4 py-2.5 rounded-lg text-sm font-bold transition border border-red-500/30">
                            Batalkan SPK
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.produksi.update_status', $produksi->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="PROSES">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-lg shadow-blue-900/30">
                            Mulai Produksi (PROSES)
                        </button>
                    </form>
                @elseif ($isProses)
                    <form method="POST" action="{{ route('admin.produksi.update_status', $produksi->id) }}" onsubmit="return confirm('Yakin produksi ini sudah selesai? Stok produk jadi akan bertambah.');">
                        @csrf
                        <input type="hidden" name="status" value="SELESAI">
                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-lg shadow-green-900/30">
                            Selesaikan Produksi (TAMBAH STOK)
                        </button>
                    </form>
                @elseif ($isSelesai || $produksi->status === 'DIBATALKAN')
                    <span class="text-sm text-gray-500 italic">SPK ini sudah ditutup.</span>
                @endif
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            
            @include('components.flash-notification')

            <div class="grid grid-cols-3 gap-8">
                
                <div class="col-span-1 bg-[#1A1A1A] rounded-xl border border-white/10 p-6 flex flex-col space-y-5">
                    
                    <h3 class="text-lg font-bold text-gray-300 border-b border-white/10 pb-2">Ringkasan SPK</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between border-b border-white/5 pb-1.5">
                            <span class="text-gray-400">Status:</span>
                            @php
                                $statusClass = 'status-' . strtoupper($produksi->status);
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase {{ $statusClass }}">
                                {{ $produksi->status }}
                            </span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-1.5">
                            <span class="text-gray-400">Produk:</span>
                            <span class="font-medium text-white">{{ $produksi->product->nama_produk ?? '-' }} ({{ $produksi->product->kode_produk ?? '-' }})</span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-1.5">
                            <span class="text-gray-400">Total Unit:</span>
                            <span class="font-bold text-orange-400 text-xl">{{ number_format($produksi->jumlah_produksi) }} Unit</span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-1.5">
                            <span class="text-gray-400">Tgl Mulai:</span>
                            <span class="font-medium text-white">{{ \Carbon\Carbon::parse($produksi->tanggal_mulai)->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-1.5">
                            <span class="text-gray-400">Tgl Selesai:</span>
                            <span class="font-medium text-white">
                                {{ $produksi->tanggal_selesai ? \Carbon\Carbon::parse($produksi->tanggal_selesai)->format('d M Y') : 'Belum Selesai' }}
                            </span>
                        </div>
                        <div class="flex justify-between pb-1.5 pt-2 border-t border-white/10">
                            <span class="text-gray-400">Durasi:</span>
                            <span class="font-bold text-white">{{ $durasi_hari }} Hari</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-orange-600/50">
                        <p class="text-sm font-medium text-gray-400">TOTAL BIAYA HPP (Estimasi)</p>
                        <p class="text-3xl font-extrabold text-orange-400 mt-1">
                            Rp {{ number_format($produksi->total_biaya_hpp, 0, ',', '.') }}
                        </p>
                    </div>

                </div>
                
                <div class="col-span-2 bg-[#1A1A1A] rounded-xl border border-white/10 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-300 border-b border-white/10 pb-2 mb-4">Total Kebutuhan Material (x{{ number_format($produksi->jumlah_produksi) }} Unit)</h3>
                    
                    <div class="overflow-y-auto flex-1">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Bahan</th>
                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Stok Saat Ini</th>
                                    <th class="px-3 py-2 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Kebutuhan Total</th>
                                    <th class="px-3 py-2 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Total Biaya Est.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse ($kebutuhan_material as $kebutuhan)
                                @php
                                    $stok_cukup = $kebutuhan['current_stock'] >= $kebutuhan['quantity_required'];
                                @endphp
                                <tr class="text-sm {{ !$stok_cukup ? 'bg-red-900/10' : '' }}">
                                    <td class="px-3 py-3 font-medium text-gray-200">
                                        {{ $kebutuhan['material_name'] }}
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="{{ $stok_cukup ? 'text-green-400' : 'text-yellow-400' }} font-bold">
                                            {{ number_format($kebutuhan['current_stock'], 2) }} {{ $kebutuhan['unit'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center text-orange-400 font-bold">
                                        {{ number_format($kebutuhan['quantity_required'], 2) }} {{ $kebutuhan['unit'] }}
                                        @if (!$stok_cukup)
                                            <span class="text-xs text-red-500 block"> (Kurang: {{ number_format($kebutuhan['quantity_required'] - $kebutuhan['current_stock'], 2) }})</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-right font-bold text-gray-300">
                                        Rp {{ number_format($kebutuhan['total_cost'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        Resep BOM untuk produk ini kosong.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>