@php
    $page = 'bahan-baku'; // Penanda menu aktif
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Bahan Baku - Berliano Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white flex h-screen overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-8 md:ml-64 bg-black">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-white/10 pb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Data Bahan Baku</h2>
                <p class="text-gray-400 text-sm mt-1">Monitoring stok bahan yang tersedia di gudang.</p>
            </div>
            
            <button class="bg-[#1A1A1A] hover:bg-[#222] border border-white/10 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh Laporan
            </button>
        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black text-gray-500 text-xs uppercase font-bold border-b border-white/10">
                        <tr>
                            <th class="p-4">Info Material</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Stok Saat Ini</th>
                            <th class="p-4">Harga Beli</th>
                            <th class="p-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-gray-300">
                        
                        @forelse($bahan as $item)
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4">
                                <div class="font-bold text-white text-base">{{ $item->nama_material }}</div>
                                <div class="text-xs text-blue-400 mt-0.5 font-mono">{{ $item->kode_material }}</div>
                            </td>
                            <td class="p-4">
                                <span class="bg-[#222] px-2.5 py-1 rounded text-xs text-gray-400 border border-white/5">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-white text-base">
                                    {{ $item->stok }} <span class="text-xs text-gray-500 font-normal">{{ $item->satuan }}</span>
                                </div>
                            </td>
                            <td class="p-4 font-mono text-gray-400">
                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($item->stok <= 5)
                                    <span class="text-[10px] bg-red-500/10 text-red-500 px-2 py-1 rounded border border-red-500/20 font-bold">Menipis</span>
                                @else
                                    <span class="text-[10px] bg-green-500/10 text-green-500 px-2 py-1 rounded border border-green-500/20 font-bold">Aman</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Belum ada data bahan baku. Operator belum menginput data.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>