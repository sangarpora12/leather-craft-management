@extends('layouts.admin')

@section('page_title', 'Data Supplier')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="space-y-6 animate-fade-in text-gray-300 font-sans bg-zinc-900 min-h-screen p-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Klik supplier untuk melihat daftar bahan yang disediakan</p>
        </div>
    </div>

    {{-- 2. DAFTAR SUPPLIER (CARD GRID) --}}
    @if($suppliers->count() > 0)
    <div class="bg-[#111111] rounded-2xl border border-white/5 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-store text-blue-500"></i>
            Daftar Supplier
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($suppliers as $namaSupplier => $bahanBakus)
            <div class="bg-[#0f1115] rounded-xl border border-white/5 p-4 hover:border-blue-500/50 transition cursor-pointer group"
                 onclick="toggleSupplier('supplier-{{ md5($namaSupplier) }}')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500/20 transition">
                            <i class="fa-solid fa-shop text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">{{ $namaSupplier }}</h3>
                            <p class="text-xs text-gray-500">{{ $bahanBakus->count() }} material tersedia</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-600 group-hover:text-blue-400 transition-transform duration-300"
                       id="arrow-{{ md5($namaSupplier) }}"></i>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- DETAIL SUPPLIER (MODAL) --}}
    @foreach($suppliers as $namaSupplier => $bahanBakus)
    <div id="supplier-{{ md5($namaSupplier) }}" class="hidden bg-[#111111] rounded-2xl border border-blue-500/30 overflow-hidden mb-4">
        <div class="px-4 py-3 bg-blue-500/10 border-b border-blue-500/20 flex justify-between items-center">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <i class="fa-solid fa-shop text-blue-400"></i>
                {{ $namaSupplier }}
            </h3>
            <button onclick="toggleSupplier('supplier-{{ md5($namaSupplier) }}')" class="text-gray-400 hover:text-white">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama Material</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-center">Satuan</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($bahanBakus as $bahan)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-gray-400">{{ $bahan->kode_material }}</td>
                        <td class="px-4 py-3 text-white font-medium">{{ $bahan->nama_material }}</td>
                        <td class="px-4 py-3 text-gray-400">{{ $bahan->kategori }}</td>
                        <td class="px-4 py-3 text-right text-green-400 font-bold">Rp {{ number_format($bahan->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center text-gray-400">{{ $bahan->satuan }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="@if($bahan->stok <= $bahan->stok_minimum) text-red-400 @else text-gray-400 @endif">
                                {{ $bahan->stok }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
    @else
    <div class="bg-[#111111] rounded-2xl border border-white/5 p-12 text-center">
        <i class="fa-solid fa-store-slash text-4xl text-gray-600 mb-4"></i>
        <p class="text-gray-400">Belum ada data supplier</p>
        <p class="text-sm text-gray-500 mt-2">Data akan muncul otomatis setelah input Bahan Baku dengan nama supplier</p>
    </div>
    @endif

</div>

<style>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
function toggleSupplier(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
</script>
@endsection