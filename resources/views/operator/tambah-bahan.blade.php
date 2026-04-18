@extends('layouts.operator_app')

@section('title', 'Tambah Bahan')

@section('content')
<div class="w-full max-w-md mx-auto bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-800 min-h-screen">

    @include('components.operator-header', ['title' => 'TAMBAH BAHAN'])

    <div class="px-6 py-6">

    <div class="bg-[#1A1A1A] rounded-3xl shadow-xl border border-white/10 p-6">

        @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-4 text-sm font-semibold">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('operator.bahan.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-700/50 shadow-xl">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Kode Material</label>
                    <input type="text" name="kode_material" placeholder="Otomatis terisi saat pilih bahan..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-blue-500 outline-none" readonly>
                </div>

                <div class="relative mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Nama Bahan</label>
                    <input type="text" id="nama_material" name="nama_material" placeholder="Ketik nama bahan..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none" required autocomplete="off">
                    <div id="suggestions" class="absolute top-full left-0 right-0 bg-gray-800 border border-gray-600 rounded-xl shadow-2xl mt-1 max-h-40 overflow-y-auto z-10 hidden"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Supplier</label>
                        <input type="text" name="supplier" placeholder="Contoh: CV. Kulit Jaya" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Ref / Nota</label>
                        <input type="text" name="ref_no" placeholder="No Nota / Barcode" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                        <select name="kategori" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option class="bg-gray-800">Bahan Utama</option>
                            <option class="bg-gray-800">Aksesoris</option>
                            <option class="bg-gray-800">Kimia</option>
                            <option class="bg-gray-800">Packaging</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Satuan</label>
                        <select name="satuan" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option class="bg-gray-800">Pcs</option>
                            <option class="bg-gray-800">Lembar</option>
                            <option class="bg-gray-800">Rol</option>
                            <option class="bg-gray-800">Kg</option>
                            <option class="bg-gray-800">Kaleng</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_inventory" value="1" class="w-5 h-5 rounded border-gray-600 bg-gray-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900" checked>
                        <span class="text-sm text-gray-300">
                            <strong>Hitung Stok & Harga</strong> - Bahan ini akan dihitung stok dan harganya dalam sistem
                        </span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1 ml-8">
                        Uncheck jika bahan ini adalah aksesoris/non-stok (seperti jaring, DLL) yang tidak perlu dihitung stok/harganya
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jumlah Masuk</label>
                        <input type="number" name="stok" placeholder="0" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga Beli (Rp)</label>
                        <input type="number" name="harga_satuan" placeholder="Rp" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-blue-500/30 transition-all duration-200 mt-6">
                Simpan
            
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
