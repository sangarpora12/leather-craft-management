@extends('layouts.operator')
@section('title', 'Produk Jadi')

@section('content')
<div x-data="productApp()" class="space-y-6">
    
    {{-- SEARCH & FILTER --}}
    <div class="relative group">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
        <input type="text" x-model="search" placeholder="Cari produk jadi..." 
               class="w-full op-input pl-11 pr-4 py-3.5 text-sm focus:ring-2 focus:ring-orange-500/20 transition-all">
    </div>

    {{-- STATS RINGKAS --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="op-card p-4 flex flex-col gap-1">
            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Total Produk</span>
            <span class="text-xl font-black text-white" x-text="products.length"></span>
        </div>
        <div class="op-card p-4 flex flex-col gap-1 border-orange-500/20">
            <span class="text-[10px] text-orange-500 uppercase font-bold tracking-widest">Siap Jual</span>
            <span class="text-xl font-black text-white" x-text="totalStock"></span>
        </div>
    </div>

    {{-- GRID PRODUK JADI --}}
    <div class="grid grid-cols-1 gap-4">
        <template x-for="product in filteredProducts" :key="product.id">
            <div class="op-card overflow-hidden group hover:border-orange-500/30 transition-all duration-300">
                <div class="flex p-4 gap-4">
                    {{-- Thumbnail Produk --}}
                    <div class="w-20 h-20 rounded-2xl bg-[#0D1117] border border-white/5 flex items-center justify-center shrink-0 overflow-hidden">
                        <template x-if="product.foto">
                            <img :src="'/storage/' + product.foto" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!product.foto">
                            <i class="fas fa-box text-2xl text-gray-700"></i>
                        </template>
                    </div>

                    {{-- Info Produk --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                        <div class="flex justify-between items-start">
                            <h3 class="text-white font-bold text-sm truncate" x-text="product.nama_produk"></h3>
                            <span class="text-[10px] bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded font-bold" x-text="product.kategori"></span>
                        </div>
                        <p class="text-[10px] text-gray-500 font-mono mt-0.5" x-text="product.kode_produk"></p>
                        
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-baseline gap-1">
                                <span class="text-lg font-black text-white" x-text="product.stok"></span>
                                <span class="text-[10px] text-gray-500 uppercase" x-text="product.satuan"></span>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-gray-600 uppercase font-bold tracking-tighter">Harga Jual</p>
                                <p class="text-xs font-bold text-[#00BFA5]" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(product.harga)"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Action Button ala Figma --}}
                <div class="flex border-t border-white/5">
                    <button class="flex-1 py-3 text-[10px] font-bold text-gray-500 hover:text-white hover:bg-white/5 transition border-r border-white/5">
                        <i class="fas fa-eye mr-2"></i> DETAIL
                    </button>
                    <button class="flex-1 py-3 text-[10px] font-bold text-orange-500 hover:bg-orange-500 hover:text-white transition">
                        <i class="fas fa-plus mr-2"></i> UPDATE STOK
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Empty State --}}
    <div x-show="filteredProducts.length === 0" class="py-20 text-center">
        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-gray-700 text-xl"></i>
        </div>
        <p class="text-gray-500 text-sm italic">Produk tidak ditemukan...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productApp() {
    return {
        search: '',
        products: @json($produkJadi), // Pastikan variabel ini dikirim dari controller
        
        get filteredProducts() {
            if (!this.search) return this.products;
            return this.products.filter(p => 
                p.nama_produk.toLowerCase().includes(this.search.toLowerCase()) ||
                p.kode_produk.toLowerCase().includes(this.search.toLowerCase())
            );
        },

        get totalStock() {
            return this.products.reduce((sum, p) => sum + p.stok, 0);
        }
    }
}
</script>
@endpush