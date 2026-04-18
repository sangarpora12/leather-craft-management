@extends('layouts.admin')
@section('title_header', 'Pengadaan Material')

@section('content')
<div x-data="procurementApp()" class="space-y-6 h-[calc(100vh-140px)] flex flex-col">

    {{-- =========================
         ALERT STOK KRITIS (DIPULIHKAN)
    ========================== --}}
    @if(isset($lowStockMaterials) && $lowStockMaterials->count() > 0)
    <div class="w-full bg-gradient-to-r from-red-900/40 to-[#111]
                border border-red-500/20 rounded-xl p-4
                flex items-center justify-between gap-6 shrink-0">

        {{-- INFO --}}
        <div class="flex items-center gap-4 shrink-0">
            <div class="w-10 h-10 rounded-full bg-red-500/20
                        flex items-center justify-center text-red-500 animate-pulse">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h4 class="text-white font-bold text-sm">Perhatian: Stok Kritis</h4>
                <p class="text-gray-400 text-xs">
                    {{ $lowStockMaterials->count() }} bahan di bawah stok minimum
                </p>
            </div>
        </div>

        {{-- LIST BAHAN KRITIS (HORIZONTAL) --}}
        <div class="flex gap-2 overflow-x-auto max-w-[65%] pb-1 custom-scrollbar">
            @foreach($lowStockMaterials as $mat)
            <div class="shrink-0 min-w-[200px]
                        bg-black/50 border border-red-500/30
                        rounded-lg px-3 py-2 flex items-center gap-3">

                <div class="flex-1">
                    <p class="text-white text-xs font-bold truncate">
                        {{ $mat->nama_material }}
                    </p>
                    <p class="text-[10px] text-red-400">
                        Sisa: {{ $mat->stok }} {{ $mat->satuan }}
                    </p>
                </div>

                <button @click="addToCart({{ $mat->id }})"
                        class="bg-red-600 hover:bg-red-500
                               text-white text-[10px]
                               px-2 py-1 rounded transition">
                    + Order
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- =========================
         AREA UTAMA (TIDAK DIUBAH)
    ========================== --}}
    <div class="flex flex-col lg:flex-row gap-6 flex-1 min-h-0">

        {{-- KIRI: DRAF --}}
        <div class="w-full lg:w-1/3 flex flex-col bg-[#111] border border-white/10 rounded-xl overflow-hidden shadow-2xl">
            <div class="h-14 bg-[#151515] border-b border-white/5 flex items-center justify-between px-5">
                <span class="text-white font-bold text-sm">Draf Pesanan</span>
                <span class="px-2 py-0.5 bg-white/10 rounded text-[10px] text-white"
                      x-text="cart.length + ' Item'"></span>
            </div>

            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-[#0a0a0a]">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-[#151515] border border-white/5 rounded-lg p-2
                                grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-5">
                            <p class="text-white text-xs font-bold truncate"
                               x-text="item.nama_material"></p>
                            <p class="text-gray-500 text-[10px]"
                               x-text="item.kode_material || '-'"></p>
                        </div>
                        <div class="col-span-3">
                            <input type="number" x-model="item.qty" min="1"
                                   class="w-full bg-black border border-white/10
                                          rounded text-center text-white text-xs py-1">
                        </div>
                        <div class="col-span-2 text-center text-gray-400 text-[10px]"
                             x-text="item.satuan"></div>
                        <div class="col-span-2 text-center">
                            <button @click="removeFromCart(index)"
                                    class="text-gray-500 hover:text-red-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0"
                     class="text-gray-600 text-xs text-center py-10">
                    Keranjang kosong
                </div>
            </div>

            <div class="p-4 bg-[#151515] border-t border-white/5">
                <button type="button"
                        @click="showSubmitModal = true"
                        :disabled="cart.length === 0"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700
                               disabled:opacity-50 disabled:cursor-not-allowed
                               rounded-lg text-white text-xs font-bold">
                    Terbitkan PO
                </button>
            </div>
        </div>

        {{-- KANAN: KATALOG --}}
        <div class="w-full lg:w-2/3 flex flex-col bg-[#111] border border-white/10 rounded-xl overflow-hidden shadow-2xl">
            <div class="h-16 bg-[#151515] border-b border-white/5
                        flex items-center justify-between px-5">
                <h3 class="text-white font-bold text-sm">Katalog Bahan Baku</h3>
                <input type="text" x-model="search" placeholder="Cari..."
                       class="bg-black border border-white/10
                              rounded px-3 py-2 text-xs text-white">
            </div>

            <div class="flex-1 overflow-y-auto bg-[#0a0a0a]">
                <template x-for="mat in filteredMaterials" :key="mat.id">
                    <div class="px-5 py-3 border-b border-white/5
                                flex justify-between items-center">
                        <div>
                            <p class="text-white text-sm font-bold"
                               x-text="mat.nama_material"></p>
                            <p class="text-[10px] text-gray-500"
                               x-text="mat.kode_material"></p>
                        </div>
                        <button @click="addToCart(mat.id)"
                                class="px-3 py-1.5 bg-[#222]
                                       hover:bg-emerald-600
                                       text-xs text-white rounded">
                            + Pesan
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div x-show="showSubmitModal"
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm">

        <div @click.outside="showSubmitModal = false"
             class="w-full max-w-lg bg-[#151515] border border-white/10 rounded-2xl shadow-2xl overflow-hidden">

            <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
                <div>
                    <h3 class="text-white font-bold text-sm">Konfirmasi Pengadaan</h3>
                    <p class="text-[10px] text-gray-500">Lengkapi data sebelum mengirim</p>
                </div>
                <button @click="showSubmitModal = false" class="text-gray-500 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.pengadaan.store') }}"
                  method="POST"
                  class="p-6 space-y-5"
                  x-ref="submitForm">
                @csrf

                {{-- hidden item --}}
                <template x-for="(item, index) in cart" :key="index">
                    <div>
                        <input type="hidden" :name="`items[${index}][bahan_baku_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${index}][jumlah]`" :value="item.qty">
                    </div>
                </template>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.prioritas"
                            name="prioritas"
                            class="w-full bg-black border rounded-lg text-white text-xs px-3 py-2.5"
                            :class="formErrors.prioritas ? 'border-red-500' : 'border-white/10 focus:border-emerald-500'">
                        <option value="">-- Pilih --</option>
                        <option value="BIASA">Biasa</option>
                        <option value="URGENT">Urgent</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">
                        Tanggal Dibutuhkan <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           x-model="form.tanggal"
                           name="tanggal_dibutuhkan"
                           @focus="$el.showPicker && $el.showPicker()"
                           class="w-full bg-black border rounded-lg text-white text-xs px-3 py-2.5 cursor-pointer"
                           :class="formErrors.tanggal ? 'border-red-500' : 'border-white/10 focus:border-emerald-500'">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">
                        Operator (Opsional)
                    </label>
                    <select name="operator_id"
                            class="w-full bg-black border border-white/10 rounded-lg text-white text-xs px-3 py-2.5">
                        <option value="">-- Pilih Operator --</option>
                        @foreach($operators as $op)
                            <option value="{{ $op->id }}">{{ $op->username }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">
                        Catatan
                    </label>
                    <textarea name="catatan" rows="3"
                              class="w-full bg-black border border-white/10 rounded-lg text-white text-xs px-3 py-2.5"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button"
                            @click="showSubmitModal = false"
                            class="px-4 py-2 text-xs text-gray-400">
                        Batal
                    </button>
                    <button type="button"
                            @click="submitPengadaan()"
                            :disabled="!isFormValid"
                            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded
                                   disabled:opacity-40 disabled:cursor-not-allowed">
                        Kirim Order
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function procurementApp() {
    return {
        search: '',
        cart: [],
        materials: @json($bahanBakus),
        showSubmitModal: false,

        form: { prioritas: '', tanggal: '' },
        formErrors: { prioritas: false, tanggal: false },

        get filteredMaterials() {
            if (!this.search) return this.materials;
            return this.materials.filter(m =>
                m.nama_material.toLowerCase().includes(this.search.toLowerCase()) ||
                (m.kode_material && m.kode_material.toLowerCase().includes(this.search.toLowerCase()))
            );
        },

        get isFormValid() {
            return this.form.prioritas !== '' && this.form.tanggal !== '';
        },

        submitPengadaan() {
            this.formErrors.prioritas = this.form.prioritas === '';
            this.formErrors.tanggal = this.form.tanggal === '';
            if (this.isFormValid) this.$refs.submitForm.submit();
        },

        addToCart(id) {
            const mat = this.materials.find(m => m.id === id);
            if (!mat) return;
            const existing = this.cart.find(i => i.id === id);
            existing ? existing.qty++ : this.cart.push({
                id: mat.id,
                nama_material: mat.nama_material,
                kode_material: mat.kode_material,
                satuan: mat.satuan,
                qty: 1
            });
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        }
    }
}
</script>
@endpush
