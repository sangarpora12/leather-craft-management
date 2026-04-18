@extends('layouts.operator_app')

@section('title', 'Input Bahan')

@section('content')
<div class="px-5 pt-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-black uppercase italic tracking-tight">Input Stok Masuk</h2>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Catat bahan baku baru dari supplier</p>
        </div>
        <div class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center font-bold text-xs border border-white/10">OP</div>
    </div>

    <div class="bg-[#1A1A1A] rounded-3xl shadow-xl border border-white/10 p-6">

        @if(session('success'))
        <div class="bg-green-600/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-xl relative mb-4 text-xs font-bold">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('operator.bahan.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Kode Material</label>
                <input type="text" name="kode_material" placeholder="Otomatis terisi saat pilih bahan..." class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 outline-none" readonly>
            </div>

            <div class="relative">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Bahan</label>
                <input type="text" id="nama_material" name="nama_material" placeholder="Ketik nama bahan..." class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 outline-none" required autocomplete="off">
                <div id="suggestions" class="absolute top-full left-0 right-0 bg-gray-800 border border-gray-600 rounded-b-xl max-h-40 overflow-y-auto z-10 hidden"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Supplier</label>
                    <input type="text" name="supplier" placeholder="Contoh: CV. Kulit Jaya" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Ref / Nota</label>
                    <input type="text" name="ref_no" placeholder="No Nota / Barcode" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Kategori</label>
                    <select name="kategori" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-orange-500">
                        <option>Bahan Utama</option>
                        <option>Aksesoris</option>
                        <option>Kimia</option>
                        <option>Packaging</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Satuan</label>
                    <select name="satuan" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-orange-500">
                        <option>Pcs</option>
                        <option>Lembar</option>
                        <option>Rol</option>
                        <option>Kg</option>
                        <option>Kaleng</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Jumlah Masuk</label>
                    <input type="number" name="stok" placeholder="0" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm font-bold text-center focus:ring-2 focus:ring-orange-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Harga Beli (Rp)</label>
                    <input type="number" name="harga_satuan" placeholder="Rp" class="w-full bg-gray-800 border border-white/10 rounded-xl px-4 py-3 text-sm font-bold text-center focus:ring-2 focus:ring-orange-500 outline-none" required>
                </div>
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-orange-900/20 hover:bg-orange-500 transition transform active:scale-95 mt-8">
                Simpan Data
            </button>

        </form>
    </div>
</div>

<script>
console.log('Script loaded');
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    var input = document.getElementById('nama_material');
    var suggestions = document.getElementById('suggestions');
    var timeout;

    input.addEventListener('input', function() {
        var query = this.value.trim();
        if (query.length < 1) {
            suggestions.classList.add('hidden');
            suggestions.innerHTML = '';
            return;
        }

        clearTimeout(timeout);
        timeout = setTimeout(function() {
            console.log('Searching for:', query);
            fetch('{{ route("api.bahan.search") }}?q=' + encodeURIComponent(query), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Data received:', data);
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(function(item) {
                        var itemDiv = document.createElement('div');
                        itemDiv.className = 'px-4 py-2 hover:bg-gray-700 cursor-pointer text-sm text-white';
                        itemDiv.innerHTML = item.nama_material + ' (' + item.kode_material + ') - Stok: ' + item.stok + ' ' + item.satuan;
                        itemDiv.dataset.nama = item.nama_material;
                        itemDiv.dataset.kode = item.kode_material;
                        itemDiv.dataset.kategori = item.kategori || '';
                        itemDiv.dataset.satuan = item.satuan || '';
                        itemDiv.dataset.supplier = item.supplier || '';
                        itemDiv.dataset.refNo = item.ref_no || '';
                        itemDiv.addEventListener('click', function() {
                            input.value = this.dataset.nama;
                            document.querySelector('input[name="kode_material"]').value = this.dataset.kode;
                            document.querySelector('select[name="kategori"]').value = this.dataset.kategori;
                            document.querySelector('select[name="satuan"]').value = this.dataset.satuan;
                            document.querySelector('input[name="supplier"]').value = this.dataset.supplier;
                            document.querySelector('input[name="ref_no"]').value = this.dataset.refNo;
                            suggestions.classList.add('hidden');
                            suggestions.innerHTML = '';
                        });
                        suggestions.appendChild(itemDiv);
                    });
                    suggestions.classList.remove('hidden');
                    console.log('Suggestions shown');
                } else {
                    suggestions.classList.add('hidden');
                }
            })
            .catch(error => {
                console.log('Fetch error:', error);
            });
        }, 250);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.classList.add('hidden');
        }
    });
});
</script>
@endsection
