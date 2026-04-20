@extends('layouts.admin')
@section('page_title', 'Pengadaan Material')

@section('content')
{{-- CONTAINER UTAMA: Menggunakan p-6 agar ada jarak ke pinggir, min-h-screen agar lega --}}
<div x-data="procurementApp()" class="min-h-screen bg-[#0D1117] p-6 space-y-6 animate-fade-in font-sans text-gray-300">

    {{-- 1. HEADER & BAHAN KRITIS (TOP SECTION) --}}
    <div class="flex flex-col gap-4">
        <div>
            <p class="text-[10px] text-gray-500 uppercase tracking-[0.2em]">Inventory Control System</p>
        </div>

        @if(isset($lowStockMaterials) && $lowStockMaterials->count() > 0)
        <div class="bg-[#1A1C24] border border-red-500/20 rounded-xl p-4 shadow-lg overflow-hidden">
            <div class="flex items-center gap-2 mb-3">
                <span class="flex h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                <h3 class="text-white font-bold text-xs uppercase italic">Stok Kritis Terdeteksi</h3>
            </div>
            {{-- Horizontal Scroll Kecil --}}
            <div class="flex gap-3 overflow-x-auto custom-scrollbar pb-2">
                @foreach($lowStockMaterials as $mat)
                <div class="shrink-0 w-[180px] bg-[#0D1117] border border-white/5 rounded-lg p-3 flex flex-col justify-between hover:border-red-500/30 transition">
                    <div class="mb-2">
                        <p class="text-white text-[11px] font-bold truncate">{{ $mat->nama_material }}</p>
                        <p class="text-[9px] text-red-500 font-black">Sisa: {{ $mat->stok }} {{ $mat->satuan }}</p>
                    </div>
                    <button @click="addToCart({{ $mat->id }})" class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-[9px] font-bold py-1 rounded transition uppercase">
                        + Tambah
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- 2. MAIN LAYOUT (LEFT: CATALOG, RIGHT: CART FIXED) --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        {{-- PANEL KIRI: KATALOG MATERIAL (SCROLLABLE) --}}
        <div class="flex-1 w-full">
            <div class="bg-[#1A1C24] border border-white/5 rounded-xl shadow-2xl overflow-hidden">
                <div class="p-4 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <h3 class="text-white font-black text-[11px] uppercase tracking-widest">Katalog Material</h3>
                    <div class="relative w-full md:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-700 text-[10px]"></i>
                        <input type="text" x-model="search" placeholder="Cari Nama/Kode Bahan..." 
                               class="w-full bg-[#0D1117] border border-white/10 rounded-lg pl-9 pr-3 py-2 text-[11px] text-white focus:border-orange-500 outline-none transition">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-[9px] text-gray-500 font-black uppercase tracking-widest border-b border-white/5">
                            <tr>
                                <th class="px-4 py-3 w-20">Foto</th>
                                <th class="px-6 py-4">Informasi Material</th>
                                <th class="px-6 py-4 text-center">Stok</th>
                                <th class="px-6 py-4 text-right">Harga Satuan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <template x-for="mat in filteredMaterials" :key="mat.id">
                                <tr class="hover:bg-white/[0.02] transition">
                                    <td class="px-4 py-3">
                                        <template x-if="mat.foto">
                                            <img :src="'/storage/' + mat.foto" 
                                                 :alt="mat.nama_material"
                                                 class="w-16 h-16 rounded-lg object-cover border border-white/10">
                                        </template>
                                        <template x-if="!mat.foto">
                                            <div class="w-16 h-16 rounded-lg bg-[#2D3139] flex items-center justify-center border border-gray-700/50">
                                                <i class="fa-solid fa-image text-gray-500 text-lg"></i>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-white text-xs font-bold" x-text="mat.nama_material"></p>
                                        <p class="text-[9px] text-gray-600 font-mono mt-0.5" x-text="mat.kode_material"></p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center">
                                            <span :class="mat.stok <= 5 ? 'text-red-500 bg-red-500/10 border-red-500/20' : 'text-green-500 bg-green-500/10 border-green-500/20'" 
                                                   class="text-[9px] font-black px-3 py-1 rounded-l border border-r-0 font-mono" 
                                                   x-text="Math.round(mat.stok)"></span>
                                            <span class="text-[9px] font-bold px-2 py-1 rounded-r border text-gray-400 bg-white/5" x-text="mat.satuan"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-mono text-gray-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(mat.harga_satuan || 0)"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="addToCart(mat.id)" class="bg-orange-600/10 text-orange-500 hover:bg-orange-600 hover:text-white px-4 py-1.5 rounded-md border border-orange-600/20 text-[9px] font-black transition-all">
                                            TAMBAH
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PANEL KANAN: KERANJANG ORDER (FIXED STICKY) --}}
        <div class="w-full lg:w-[380px] shrink-0 sticky top-4 h-[calc(100vh-3rem)]">
            <div class="bg-[#1A1C24] border border-white/5 rounded-xl shadow-2xl flex flex-col h-full">
                <div class="p-4 border-b border-white/5 flex justify-between items-center bg-white/5">
                    <h3 class="text-orange-500 font-black text-[10px] uppercase tracking-widest text-center">Keranjang Order</h3>
                    <span class="text-[10px] text-gray-500" x-text="cart.length + ' Item'"></span>
                </div>

                {{-- Daftar Item Keranjang DENGAN SCROLL --}}
                <div class="p-4 space-y-3 flex-grow overflow-y-auto max-h-[350px] custom-scrollbar">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="bg-[#0D1117] border border-white/5 p-3 rounded-lg flex flex-col gap-2">
                            <div class="flex gap-3 items-start">
                                <!-- Foto Bahan di Keranjang -->
                                <template x-if="item.foto">
                                    <img :src="'/storage/' + item.foto" 
                                         :alt="item.nama_material"
                                         class="w-10 h-10 rounded-lg object-cover border border-white/10 shrink-0">
                                </template>
                                <template x-if="!item.foto">
                                    <div class="w-10 h-10 rounded-lg bg-[#2D3139] flex items-center justify-center border border-gray-700/50 shrink-0">
                                        <i class="fa-solid fa-image text-gray-500 text-sm"></i>
                                    </div>
                                </template>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <p class="text-white text-[11px] font-bold truncate" x-text="item.nama_material"></p>
                                        <button @click="removeFromCart(index)" class="text-gray-700 hover:text-red-500 ml-2"><i class="fas fa-times text-[10px]"></i></button>
                                    </div>
                                    
                                    <!-- Harga Total -->
                                    <p class="text-[10px] text-gray-400 font-mono mt-1" 
                                       x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(item.qty) * item.harga_satuan)"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex-1 flex items-center bg-black/40 rounded border border-white/5 p-1">
                                    <button @click="item.qty > 1 ? item.qty = Math.round(item.qty - 1) : null" class="w-6 h-6 text-gray-500">-</button>
                                    <input type="number" x-model.number="item.qty" class="w-full bg-transparent text-center text-[10px] text-white outline-none">
                                    <button @click="item.qty = Math.round(item.qty + 1)" class="w-6 h-6 text-gray-500">+</button>
                                </div>
                            </div>
                            
                            <!-- Input Catatan -->
                            <input type="text" 
                                   x-model="item.catatan" 
                                   placeholder="Catatan (opsional)..." 
                                   class="w-full mt-2 text-[10px] bg-gray-900 border border-gray-700 text-gray-300 rounded-md p-1.5 outline-none focus:border-orange-500">
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="py-10 text-center opacity-20 italic text-[10px]">Belum ada pilihan</div>
                </div>

                {{-- Footer Keranjang Tetap di Bawah --}}
                <div class="p-4 border-t border-white/5 bg-black/20 space-y-3 mt-auto">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-500 uppercase">Estimasi</span>
                        <span class="text-sm font-black text-[#00BFA5]" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalEstimasi)"></span>
                    </div>
                    <button @click="showSubmitModal = true" :disabled="cart.length === 0" 
                            class="w-full py-3 bg-orange-600 hover:bg-orange-500 disabled:bg-gray-800 disabled:text-gray-600 rounded-lg text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-900/20 transition-all">
                        BUAT PENGADAAN
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL (TETAP SAMA) --}}
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4">
        <div @click.outside="showSubmitModal = false" class="w-full max-w-md bg-[#1A1C24] border border-white/10 rounded-2xl shadow-2xl overflow-hidden animate-zoom-in">
            <div class="p-4 border-b border-white/5 bg-black/20 flex justify-between items-center">
                <h3 class="text-white font-black text-[10px] uppercase tracking-widest">Konfirmasi Pengadaan</h3>
                <button @click="showSubmitModal = false" class="text-gray-500 hover:text-white"><i class="fas fa-times text-xs"></i></button>
            </div>
            
            <form action="{{ route('admin.pengadaan.store') }}" method="POST" class="p-6 space-y-4" x-ref="submitForm">
                @csrf

                <!-- TABEL DAFTAR BAHAN YANG AKAN DIPESAN -->
                <div class="mb-4 bg-[#0D1117] rounded-lg overflow-hidden border border-white/5">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-black/30 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                <th class="py-3 px-4">Foto</th>
                                <th class="py-3 px-4">Nama Bahan</th>
                                <th class="py-3 px-4 text-center">Jumlah</th>
                                <th class="py-3 px-4 text-center">Satuan</th>
                                <th class="py-3 px-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="border-t border-white/5">
                                    <td class="py-3 px-4">
                                        <template x-if="item.foto">
                                        <img :src="'/storage/' + item.foto" 
                                             :alt="item.nama_material"
                                             class="w-16 h-16 rounded-lg object-cover border border-white/10" style="max-width: 64px; max-height: 64px;">
                                        </template>
                                        <template x-if="!item.foto">
                                            <div class="w-16 h-16 rounded-lg bg-[#2D3139] flex items-center justify-center border border-gray-700/50">
                                                <i class="fa-solid fa-image text-gray-500 text-lg"></i>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="text-white text-[11px] font-bold" x-text="item.nama_material"></p>
                                        <p class="text-[9px] text-gray-500 font-mono mt-0.5" x-text="item.kode_material"></p>
                                        <input type="hidden" :name="`items[${index}][bahan_baku_id]`" :value="item.id">
                                        <input type="hidden" :name="`items[${index}][jumlah]`" :value="item.qty">
                                        <input type="hidden" :name="`items[${index}][catatan]`" :value="item.catatan">
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-green-400 font-bold" x-text="Math.round(item.qty)"></td>
                                    <td class="py-3 px-4 text-center text-gray-400 text-[10px]" x-text="item.satuan"></td>
                                    <td class="py-3 px-4 text-gray-400 text-[10px]" style="max-width:180px; word-wrap:break-word;" x-text="item.catatan || '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Prioritas</label>
                        <select name="prioritas" class="w-full bg-[#0D1117] border border-white/10 rounded-lg text-white text-[11px] px-3 py-2 outline-none focus:border-orange-500">
                            <option value="BIASA">BIASA</option>
                            <option value="URGENT">URGENT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Target Tanggal</label>
                        <input type="date" name="tanggal_dibutuhkan" x-model="form.tanggal" class="w-full bg-[#0D1117] border border-white/10 rounded-lg text-white text-[11px] px-3 py-2 outline-none focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Operator</label>
                        <select name="operator_id" class="w-full bg-[#0D1117] border border-white/10 rounded-lg text-white text-[11px] px-3 py-2 outline-none focus:border-orange-500">
                            <option value="">-- Pilih --</option>
                            @foreach($operators as $op) <option value="{{ $op->id }}">{{ $op->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
                    <button type="button" @click="showSubmitModal = false" class="text-[10px] text-gray-500 font-bold hover:text-white transition">BATAL</button>
                    <button type="button" @click="submitPengadaan()" :disabled="!isFormValid" class="px-6 py-2 bg-orange-600 text-white text-[10px] font-black rounded-lg shadow-lg shadow-orange-900/40 uppercase tracking-widest hover:scale-105 active:scale-95 disabled:opacity-50">KIRIM ORDER</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- LOGIKA Alpine.js --}}
@push('scripts')
<script>
function procurementApp() {
    return {
        search: '',
        cart: [],
        materials: @json($bahanBakus),
        showSubmitModal: false,
        form: { prioritas: 'BIASA', tanggal: '' },

        get filteredMaterials() {
            if (!this.search) return this.materials;
            return this.materials.filter(m =>
                m.nama_material.toLowerCase().includes(this.search.toLowerCase()) ||
                (m.kode_material && m.kode_material.toLowerCase().includes(this.search.toLowerCase()))
            );
        },

        get totalEstimasi() {
            return this.cart.reduce((sum, item) => sum + (item.qty * (item.harga_satuan || 0)), 0);
        },

        get isFormValid() {
            return this.form.tanggal !== '' && this.cart.length > 0;
        },

                            addToCart(id) {
                                const mat = this.materials.find(m => m.id === id);
                                if (!mat) return;
                                const existing = this.cart.find(i => i.id === id);
                                if (existing) {
                                    existing.qty = Math.round(existing.qty + 1);
                                } else {
                                    this.cart.push({
                                        id: mat.id,
                                        nama_material: mat.nama_material,
                                        kode_material: mat.kode_material,
                                        foto: mat.foto,
                                        satuan: mat.satuan,
                                        harga_satuan: mat.harga_satuan || 0,
                                        qty: 1,
                                        catatan: ''
                                    });
                                }
                            },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        submitPengadaan() {
            if (this.isFormValid) this.$refs.submitForm.submit();
        }
    }
}
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #2D3139; border-radius: 10px; }
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
</style>
@endpush
@endsection