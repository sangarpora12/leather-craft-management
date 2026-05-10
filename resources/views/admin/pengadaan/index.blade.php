@extends('layouts.admin')
@section('page_title', 'Pengadaan Material')

@section('content')
<div x-data="procurementApp()" class="min-h-screen bg-[#0D1117] p-6 space-y-6 animate-fade-in font-sans text-gray-300">

    {{-- IMAGE PREVIEW MODAL --}}
    <div x-show="showImagePreview" @click="showImagePreview = false" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black/90 p-4 cursor-pointer" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <img :src="previewImageUrl" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl" @click.stop>
    </div>


    {{-- 1. HEADER & BAHAN KRITIS --}}
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

            <div class="flex gap-3 overflow-x-auto custom-scrollbar pb-2">
                @foreach($lowStockMaterials as $kode => $items)
                    @foreach($items as $item)
                    <div class="shrink-0 w-[180px] bg-[#0D1117] border border-white/5 rounded-lg p-3 flex flex-col justify-between hover:border-red-500/30 transition">
                        <div class="mb-2">
                            <div class="w-full h-20 relative group">
                                @php
                                    $criticalMasterRow = $bahanBakus->get($item->kode_material)?->first();
                                    $criticalPhotoRow = $bahanBakus->get($item->kode_material)?->firstWhere('foto', '!=', null);
                                    $criticalPhoto = $item->foto ?: ($criticalPhotoRow ? $criticalPhotoRow->foto : null);
                                @endphp
                                
                                {{-- Area untuk Klik Pratinjau --}}
                                <div @if($criticalPhoto) @click.stop="openImagePreview('{{ asset('storage/' . $criticalPhoto) }}')" @endif class="w-full h-full rounded cursor-pointer">
                                    @if($criticalPhoto)
                                        <img id="foto-kritis-{{$item->id}}" src="{{ asset('storage/' . $criticalPhoto) }}" class="w-full h-full object-cover rounded border border-white/10">
                                    @else
                                        <div class="w-full h-full bg-gray-800 rounded flex items-center justify-center border border-dashed border-gray-600">
                                            <i class="fa-solid fa-camera text-gray-700 text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Tombol Upload di Pojok --}}
                                {{-- <label for="photo-upload-kritis-{{$item->id}}" class="absolute top-1 right-1 w-6 h-6 bg-black/60 rounded flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition cursor-pointer hover:bg-black">
                                    <i class="fas fa-camera text-xs"></i>
                                </label> --}}
                            </div>
                            {{-- <input type="file" id="photo-upload-kritis-{{$item->id}}" class="hidden" onchange="uploadPhotoVarian({{$item->id}}, '{{$item->kode_material}}', 'foto-kritis-')"> --}}
                            <p class="text-white text-[11px] font-bold truncate mt-2">{{ $item->nama_material ?? 'N/A' }}</p>
                            <p class="text-[10px] text-orange-400 font-semibold">{{ $item->warna ?? 'Polos' }}</p>
                            <p class="text-[9px] text-red-500 font-black">Sisa: {{ rtrim(rtrim(number_format($item->stok, 2, '.', ''), '0'), '.') }} {{ $item->satuan ?? '' }}</p>
                        </div>
                        <button @click="addToCart({{ $item->id }}, '{{ $item->warna ?? 'Polos' }}')" 
                                class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-[9px] font-bold py-1 rounded transition uppercase">
                            + Tambah
                        </button>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- 2. MAIN LAYOUT --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        {{-- PANEL KIRI: KATALOG --}}
        <div class="flex-1 w-full">
            <div class="bg-[#1A1C24] border border-white/5 rounded-xl shadow-2xl overflow-hidden">
                <div class="p-4 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-3 text-white">
                    <h3 class="font-black text-[11px] uppercase tracking-widest">Katalog Material</h3>
                    <div class="relative w-full md:w-64">
                        <input type="text" x-model="search" placeholder="Cari Bahan..." 
                               class="w-full bg-[#0D1117] border border-white/10 rounded-lg pl-4 pr-3 py-2 text-[11px] outline-none">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-[9px] text-gray-500 font-black uppercase tracking-widest border-b border-white/5">
                            <tr>
                                <th class="px-4 py-3 w-20">Foto</th>
                                <th class="px-6 py-4">Material</th>
                                <th class="px-6 py-4 text-center">Stok</th>
                                <th class="px-6 py-4 text-right">Harga</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                                                @foreach($bahanBakus as $kode => $group)
                            <tbody x-data="{ expanded: false }" class="divide-y divide-white/5">
                                @php 
                                    $masterRow = $group->first();
                                    $photoRow = $group->firstWhere('foto', '!=', null);
                                    $masterPhoto = $photoRow ? $photoRow->foto : ($masterRow ? $masterRow->foto : null);
                                @endphp
                                <tr @click="expanded = !expanded" class="bg-white/[0.02] hover:bg-white/[0.03] transition cursor-pointer">
                                    <td class="px-4 py-3">
                                        <div class="w-12 h-12 relative group">
                                            {{-- Area untuk Klik Pratinjau --}}
                                            <div @if($masterPhoto) @click.stop="openImagePreview('{{ asset('storage/'.$masterPhoto) }}')" @endif class="w-full h-full rounded-xl cursor-pointer">
                                                @if($masterPhoto)
                                                    <img id="foto-{{$kode}}" src="{{ asset('storage/'.$masterPhoto) }}" class="w-full h-full rounded-xl object-cover">
                                                @else
                                                    <div class="w-full h-full rounded-xl bg-gray-800 border border-dashed border-gray-600 flex items-center justify-center">
                                                        <i class="fas fa-camera text-gray-600"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Tombol Upload di Pojok --}}
                                            {{-- <label for="photo-upload-{{$kode}}" class="absolute top-1 right-1 w-5 h-5 bg-black/60 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition cursor-pointer hover:bg-black">
                                                <i class="fas fa-camera text-[8px]"></i>
                                            </label> --}}
                                        </div>
                                        {{-- <input type="file" id="photo-upload-{{$kode}}" class="hidden" onchange="uploadPhoto('{{$kode}}')"> --}}
                                    </td>
                                    <td class="px-6 py-4" colspan="3">
                                        <p class="text-white text-sm font-black">{{ $masterRow->nama_material }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $kode }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <span x-show="!expanded" class="text-[9px] text-gray-500 font-bold">{{ $group->count() }} varian</span>
                                            <i class="fas fa-chevron-down transition-transform text-gray-500 w-4" :class="expanded && 'rotate-180'"></i>
                                        </div>
                                    </td>
                                </tr>
                                
                                @foreach($group as $v)
                                <tr x-show="expanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="hover:bg-white/[0.04] transition">
                                    <td class="px-4 py-3">
                                        <div class="w-10 h-10 relative group">
                                            @php
                                                $varianPhoto = $v->foto ?: $masterPhoto;
                                            @endphp
                                            {{-- Area untuk Klik Pratinjau --}}
                                            <div @if($varianPhoto) @click.stop="openImagePreview('{{ asset('storage/'.$varianPhoto) }}')" @endif class="w-full h-full rounded-lg cursor-pointer">
                                                @if($varianPhoto)
                                                    <img id="foto-varian-{{$v->id}}" src="{{ asset('storage/'.$varianPhoto) }}" class="w-full h-full rounded-lg object-cover">
                                                @else
                                                    <div class="w-full h-full rounded-lg bg-gray-800 border border-dashed border-gray-600 flex items-center justify-center">
                                                        <i class="fas fa-camera text-gray-600 text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Tombol Upload di Pojok --}}
                                            {{-- <label for="photo-upload-varian-{{$v->id}}" class="absolute top-0.5 right-0.5 w-5 h-5 bg-black/60 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition cursor-pointer hover:bg-black">
                                                <i class="fas fa-camera text-[8px]"></i>
                                            </label> --}}
                                        </div>
                                        {{-- <input type="file" id="photo-upload-varian-{{$v->id}}" class="hidden" onchange="uploadPhotoVarian({{$v->id}}, '{{$kode}}', 'foto-varian-')"> --}}
                                    </td>
                                    <td class="px-6 py-3 text-xs font-bold text-gray-300">{{ $v->warna ?? 'Polos' }}</td>
                                    <td class="px-6 py-3 text-center font-black text-[11px] {{ $v->stok <= 5 ? 'text-red-500' : 'text-emerald-500' }}">
                                        {{ rtrim(rtrim(number_format($v->stok, 2, '.', ''), '0'), '.') }} {{ $v->satuan }}
                                    </td>
                                    <td class="px-6 py-3 text-right text-[11px] font-mono">Rp{{ number_format($v->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <button @click.stop="addToCart({{ $v->id }}, '{{ $v->warna ?? 'Polos' }}')" class="bg-orange-600/10 text-orange-500 hover:bg-orange-600 hover:text-white px-4 py-1.5 rounded-lg border border-orange-600/20 text-[10px] font-black transition-all">
                                            + KERANJANG
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        {{-- PANEL KANAN: KERANJANG --}}
        <div class="w-full lg:w-[380px] shrink-0 sticky top-4 h-[calc(100vh-6rem)]">
            <div class="bg-[#1A1C24] border border-white/5 rounded-xl flex flex-col h-full overflow-hidden">
                <div class="p-4 border-b border-white/5 flex justify-between items-center bg-white/5">
                    <h3 class="text-orange-500 font-black text-[10px] uppercase">Keranjang Order</h3>
                    <span class="text-[10px]" x-text="cart.length + ' Item'"></span>
                </div>

                <div class="flex-1 p-4 space-y-3 overflow-y-auto custom-scrollbar">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="bg-[#0D1117] border border-white/5 p-3 rounded-lg flex flex-col gap-2">
                            <div class="flex gap-3">
                                <img :src="item.foto ? '/storage/' + item.foto : '/images/no-image.png'" class="w-10 h-10 rounded-lg object-cover">
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <p class="text-white text-[11px] font-bold" x-text="item.nama_material"></p>
                                        <button @click="removeFromCart(index)" class="text-gray-700 hover:text-red-500"><i class="fas fa-times"></i></button>
                                    </div>
                                    <p class="text-[9px] text-orange-400 font-black uppercase" x-text="'Warna: ' + item.warna"></p>
                                    <p class="text-[10px] text-gray-400 font-mono" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.qty * item.harga_satuan)"></p>
                                </div>
                            </div>
                            <input type="number" x-model="item.qty" class="w-full bg-black/40 text-white text-center text-[10px] py-1 rounded">
                        </div>
                    </template>
                </div>

                <div class="p-4 border-t border-white/5 bg-[#1A1C24]">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm font-black text-[#00BFA5]" x-text="'Total: Rp ' + new Intl.NumberFormat('id-ID').format(totalEstimasi)"></span>
                    </div>
                    <button @click="showSubmitModal = true" :disabled="cart.length === 0" class="w-full py-3 bg-orange-600 rounded-lg text-white text-[10px] font-black uppercase">
                        BUAT PENGADAAN
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI --}}
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4">
        <div class="w-full max-w-2xl bg-[#1A1C24] rounded-2xl overflow-hidden flex flex-col">
            <form action="{{ route('admin.pengadaan.store') }}" method="POST" x-ref="submitForm">
                @csrf
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <template x-for="(item, index) in cart" :key="index">
                            <tr class="border-b border-white/5">
                                <td class="py-2 align-top">
                                    <p class="font-bold text-white" x-text="item.nama_material"></p>
                                    <p class="text-[9px] text-orange-400 uppercase" x-text="item.warna"></p>
                                    <input type="hidden" :name="`items[${index}][bahan_baku_id]`" :value="item.id">
                                    <input type="hidden" :name="`items[${index}][jumlah]`" :value="item.qty">
                                    <input type="hidden" :name="`items[${index}][warna]`" :value="item.warna">
                                    <input type="hidden" :name="`items[${index}][catatan]`" x-bind:value="item.catatan">
                                </td>
                                <td class="py-2 align-top text-right font-black" x-text="item.qty + ' ' + item.satuan"></td>
                                <td class="py-2 pl-4 align-top w-1/3">
                                    <input type="text" x-model="item.catatan" class="bg-[#0D1117] border border-white/10 text-white p-2 rounded text-xs w-full" placeholder="Catatan...">
                                </td>
                            </tr>
                        </template>
                    </table>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <select name="operator_id" required class="bg-[#0D1117] text-white p-2 rounded text-xs">
                            <option value="">-- Pilih Operator --</option>
                            @foreach($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                        <select name="prioritas" class="bg-[#0D1117] text-white p-2 rounded text-xs">
                            <option value="BIASA">BIASA</option><option value="URGENT">URGENT</option>
                        </select>
                        <input type="date" name="tanggal_dibutuhkan" required class="bg-[#0D1117] text-white p-2 rounded text-xs col-span-2">
                    </div>
                </div>
                <div class="p-4 flex justify-end gap-3 bg-black/20">
                    <button type="button" @click="showSubmitModal = false" class="text-xs text-gray-500">BATAL</button>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-xs font-black rounded-lg uppercase">KIRIM</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function uploadPhoto(kodeMaterial) {
    const fileInput = document.getElementById(`photo-upload-${kodeMaterial}`);
    const file = fileInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`/admin/pengadaan/update-photo/${kodeMaterial}`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const imgElement = document.getElementById(`foto-${kodeMaterial}`);
            imgElement.src = data.new_photo_url + '?t=' + new Date().getTime(); // Cache-busting
            // Anda bisa menambahkan notifikasi sukses di sini, misalnya dengan Toastr atau SweetAlert
            alert(data.success);
        } else {
            // Tampilkan pesan error
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Terjadi kesalahan saat mengunggah foto.');
    });
}

function uploadPhotoVarian(bahanId, kodeMaterial, prefix = 'foto-varian-') {
    const inputId = prefix === 'foto-varian-' ? `photo-upload-varian-${bahanId}` : `photo-upload-kritis-${bahanId}`;
    const fileInput = document.getElementById(inputId);
    const file = fileInput.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch(`/admin/pengadaan/update-photo-id/${bahanId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const imgElement = document.getElementById(`${prefix}${bahanId}`);
            if (imgElement) {
                imgElement.src = data.new_photo_url + '?t=' + new Date().getTime(); // Cache-busting
            }
            // Also update in Alpine data if exists
            const alpineComponent = document.querySelector('[x-data]').__x;
            if(alpineComponent) {
                const materialInCart = alpineComponent.cart.find(item => item.id === bahanId);
                if(materialInCart) materialInCart.foto = data.new_photo_path;

                const materialInCatalog = alpineComponent.materials.find(item => item.id === bahanId);
                if(materialInCatalog) materialInCatalog.foto = data.new_photo_path;
            }
            alert(data.success);
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Terjadi kesalahan saat mengunggah foto.');
    });
}
function procurementApp() {
    return {
        search: '', 
        cart: [],
        // Ambil data bahan baku mentah dari PHP
        materials: @json($allBahanBakus->values()),
        showSubmitModal: false,
        showImagePreview: false,
        previewImageUrl: '',
        
        get totalEstimasi() { 
            return this.cart.reduce((sum, item) => sum + (parseFloat(item.qty) * item.harga_satuan), 0); 
        },
        
        openImagePreview(url) {
            this.previewImageUrl = url;
            this.showImagePreview = true;
        },

        addToCart(id, warna) {
            // 1. Cari material berdasarkan ID yang diklik
            const mat = this.materials.find(m => m.id === id);
            if (!mat) return;

            // 2. LOGIKA FIX: Cek foto varian. Kalau kosong, cari foto dari master grupnya.
            let fotoFinal = mat.foto;
            if (!fotoFinal) {
                // Cari item lain di grup yang sama (kode_material sama) yang PUNYA foto
                const master = this.materials.find(m => m.kode_material === mat.kode_material && m.foto);
                fotoFinal = master ? master.foto : null;
            }

            // 3. Masukkan ke keranjang dengan foto hasil pengecekan tadi
            const existing = this.cart.find(i => i.id === id);
            if (existing) { 
                existing.qty = parseFloat(existing.qty) + 1; 
            } else { 
                this.cart.push({ 
                    id: mat.id, 
                    nama_material: mat.nama_material, 
                    foto: fotoFinal, // <-- SEKARANG PAKAI FOTO INI, ZUL!
                    satuan: mat.satuan, 
                    harga_satuan: mat.harga_satuan || 0, 
                    qty: 1, 
                    warna: warna, 
                    catatan: '' 
                }); 
            }
        },
        removeFromCart(index) { this.cart.splice(index, 1); }
    }
}
</script>
@endpush
@endsection