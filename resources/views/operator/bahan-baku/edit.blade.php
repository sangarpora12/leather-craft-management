@extends('layouts.operator_app')

@section('title', 'Edit Bahan Baku')

@push('styles')
<style>
.dd-wrap{position:relative;width:100%}
.dd-trigger{
    width:100%;min-height:48px;background:#1f2937;border:1px solid #4b5563;
    border-radius:12px;padding:6px 36px 6px 10px;display:flex;flex-wrap:wrap;
    gap:6px;align-items:center;cursor:pointer;color:#fff
}
.dd-placeholder{color:#9ca3af;font-size:12px}
.dd-chip{
    display:inline-flex;align-items:center;gap:4px;
    background:rgba(16,185,129,.2);border:1px solid #10b981;color:#fff;
    border-radius:6px;padding:2px 6px;font-size:11px
}
.dd-panel{
    display:none;position:absolute;top:100%;left:0;right:0;margin-top:6px;
    background:#111827;border:1px solid #4b5563;border-radius:12px;
    z-index:9999;overflow:hidden
}
.dd-panel.open{display:block}
.dd-search{
    width:100%;padding:8px;background:#1f2937;color:#fff;border:none;outline:none
}
.dd-list{max-height:180px;overflow-y:auto}
.dd-item{
    padding:10px;color:#e5e7eb;cursor:pointer
}
.dd-item:hover{background:#10b981;color:#fff}
.dd-item.sel{background:#065f46;color:#fff}
</style>
@endpush

@section('content')

@php
$selectedKategori = old('kategori', is_array($bahan->kategori)
    ? $bahan->kategori : json_decode($bahan->kategori, true) ?? []);

$selectedWarna = old('warna', is_array($bahan->warna)
    ? $bahan->warna : json_decode($bahan->warna, true) ?? []);
@endphp

<div class="w-full max-w-md mx-auto bg-gray-900 text-white min-h-screen">

<!-- HEADER -->
<header class="py-4 sticky top-0 z-40 flex items-center gap-4 bg-gray-900/95 border-b border-gray-700 px-5">
    <a href="{{ route('operator.bahan.index') }}" class="w-8 h-8 flex items-center justify-center bg-gray-800 rounded-lg">
        ←
    </a>
    <div>
        <h1 class="text-lg font-bold">Edit Bahan</h1>
        <p class="text-xs text-gray-400">{{ $bahan->kode_material }}</p>
    </div>
</header>

<main class="pb-40 pt-6 px-5">
<form id="editForm" action="{{ route('operator.bahan.update', $bahan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

<!-- NAMA -->
<div class="mb-4">
<label class="text-xs text-gray-400">Nama Material</label>
<input type="text" name="nama_material"
value="{{ old('nama_material',$bahan->nama_material) }}"
class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">
</div>

<!-- KATEGORI -->
<div class="mb-4">
<label class="text-xs text-gray-400">Kategori</label>

<select name="kategori"
class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">

@foreach($kategories as $k)
<option value="{{ $k->nama_kategori }}"
{{ old('kategori', $selectedKategori[0] ?? '') == $k->nama_kategori ? 'selected' : '' }}>
{{ $k->nama_kategori }}
</option>
@endforeach

</select>
</div>

<!-- WARNA -->
<div class="mb-4">
<label class="text-xs text-gray-400">Warna</label>

<select name="warna"
class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">

@foreach($warnas as $w)
<option value="{{ $w->nama_warna }}"
{{ old('warna', $selectedWarna[0] ?? '') == $w->nama_warna ? 'selected' : '' }}>
{{ $w->nama_warna }}
</option>
@endforeach

</select>
</div>

<!-- SATUAN -->
<div class="mb-4">
<label class="text-xs text-gray-400">Satuan</label>
<select name="satuan" class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">
@foreach($satuans as $s)
<option value="{{ $s->nama_satuan }}"
{{ old('satuan',$bahan->satuan)==$s->nama_satuan?'selected':'' }}>
{{ $s->nama_satuan }}
</option>
@endforeach
</select>
</div>

<!-- STOK + HARGA -->
<div class="grid grid-cols-2 gap-4 mb-4">
<input type="number" name="stok"
value="{{ old('stok',$bahan->stok) }}"
class="bg-gray-800 rounded-xl px-4 py-3 text-center">

<input type="text" id="harga_satuan" name="harga_satuan"
value="{{ old('harga_satuan',number_format($bahan->harga_satuan,0,',','.')) }}"
class="bg-gray-800 rounded-xl px-4 py-3 text-right">
</div>
<div class="mb-4">
    <label class="text-xs text-gray-400">Keterangan</label>
    <textarea name="keterangan" rows="3"
        placeholder="Contoh: bahan premium, tahan air, supplier lokal, dll..."
        class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white resize-none">{{ old('keterangan', $bahan->keterangan) }}</textarea>
</div>
<div class="mb-4">
    <label class="text-xs text-gray-400">Foto Material</label>

    {{-- Preview Foto Lama --}}
    @if($bahan->foto)
        <div class="mb-2">
            <img src="{{ asset('storage/'.$bahan->foto) }}"
                 class="w-full h-40 object-cover rounded-xl border border-gray-700">
        </div>
    @endif

    {{-- Input Upload --}}
    <input type="file" name="foto" id="foto_input" accept="image/*"
        class="w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4
               file:rounded-lg file:border-0 file:bg-gray-700 file:text-white
               hover:file:bg-gray-600 cursor-pointer">
</div>

<div class="mt-6">
    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl">
        Simpan
    </button>
</div>
</form>
</main>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.dd-wrap').forEach(wrap=>{

let selected = JSON.parse(wrap.dataset.selected||'[]');
let options  = JSON.parse(wrap.dataset.options||'[]');

let trigger = wrap.querySelector('.dd-trigger');
let panel   = wrap.querySelector('.dd-panel');
let list    = wrap.querySelector('.dd-list');
let search  = wrap.querySelector('.dd-search');
let hidden  = document.getElementById(wrap.dataset.hidden);

function renderList(filter=''){
list.innerHTML='';
options.filter(o=>o.toLowerCase().includes(filter.toLowerCase()))
.forEach(opt=>{
let div=document.createElement('div');
div.className='dd-item'+(selected.includes(opt)?' sel':'');
div.innerText=opt;

div.onclick=()=>{
if(selected.includes(opt)){
selected=selected.filter(x=>x!==opt);
}else{
selected.push(opt);
}
renderAll();
};

list.appendChild(div);
});
}

function renderTrigger(){
trigger.innerHTML='';
if(selected.length===0){
trigger.innerHTML='<span class="dd-placeholder">Pilih</span>';
}else{
selected.forEach(v=>{
let chip=document.createElement('span');
chip.className='dd-chip';
chip.innerText=v;
trigger.appendChild(chip);
});
}
}

function renderHidden(){
hidden.innerHTML='';
selected.forEach(v=>{
let i=document.createElement('input');
i.type='hidden';
i.name=wrap.dataset.name;
i.value=v;
hidden.appendChild(i);
});
}

function renderAll(){
renderTrigger();
renderList(search.value);
renderHidden();
}

trigger.onclick=()=>panel.classList.toggle('open');
search.oninput=()=>renderList(search.value);

renderAll();
});

// FORMAT HARGA
const harga=document.getElementById('harga_satuan');
harga.addEventListener('input',()=>{
let angka=harga.value.replace(/\D/g,'');
harga.value=angka.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
});

// FIX SUBMIT
document.getElementById('editForm').addEventListener('submit',()=>{
harga.value=harga.value.replace(/\./g,'');
});
const fotoInput = document.getElementById('foto_input');

if(fotoInput){
    fotoInput.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            const reader = new FileReader();

            reader.onload = function(e){
                let preview = document.querySelector('#preview-img');

                if(!preview){
                    preview = document.createElement('img');
                    preview.id = 'preview-img';
                    preview.className = "w-full h-40 object-cover rounded-xl mt-2";
                    fotoInput.parentNode.appendChild(preview);
                }

                preview.src = e.target.result;
            }

            reader.readAsDataURL(file);
        }
    });
}
</script>
@endpush