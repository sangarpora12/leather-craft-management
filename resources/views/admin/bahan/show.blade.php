@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-[#1E2129] p-6 sm:p-8 rounded-2xl shadow-lg">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-700">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Bahan Baku</h1>
                <p class="text-sm text-gray-400 mt-1">Informasi lengkap mengenai material.</p>
            </div>
            <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                <a href="{{ route('admin.bahan.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('admin.bahan.edit', $bahan->id) }}" class="bg-orange-500 hover:bg-orange-400 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
                    <i class="fa-solid fa-pen-to-square mr-2"></i>Edit
                </a>
                <button onclick="confirmDelete({{ $bahan->id }})" class="bg-red-500/20 hover:bg-red-500/40 text-red-400 font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
                    <i class="fa-solid fa-trash-alt mr-2"></i>Hapus
                </button>
            </div>
        </div>

        <form id="delete-form-{{ $bahan->id }}" action="{{ route('admin.bahan.destroy', $bahan->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column: Photo and Main Info -->
            <div class="lg:col-span-1">
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
                    @if($bahan->foto)
                        <img src="{{ asset('storage/' . $bahan->foto) }}" alt="{{ $bahan->nama_material }}" class="w-full h-auto object-cover rounded-lg mb-4">
                    @else
                        <div class="w-full h-48 bg-gray-700 flex items-center justify-center rounded-lg mb-4">
                            <i class="fa-solid fa-image text-gray-500 text-4xl"></i>
                        </div>
                    @endif
                    <h2 class="text-xl font-semibold text-white mb-2">{{ $bahan->nama_material }}</h2>
                    <p class="text-sm text-gray-400">{{ $bahan->kode_material }}</p>
                </div>
            </div>

            <!-- Right Column: Details and FIFO Table -->
            <div class="lg:col-span-2">
                <!-- Details Card -->
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50 mb-8">
                    <h3 class="text-lg font-semibold text-white mb-4 border-b border-gray-600 pb-2">Informasi Detail</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400">Kategori</p>
                            <p class="text-white font-medium">{{ $bahan->kategori ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Warna</p>
                            <p class="text-white font-medium">{{ $bahan->warna ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Stok Saat Ini</p>
                            <p class="text-white font-bold text-lg">{{ number_format($bahan->stok, 2, ',', '.') }} <span class="text-xs text-gray-400">{{ $bahan->satuan }}</span></p>
                        </div>
                        <div>
                            <p class="text-gray-400">Satuan</p>
                            <p class="text-white font-medium">{{ $bahan->satuan }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-400">Detail Tambahan</p>
                            <p class="text-white font-medium">{{ $bahan->detail ?? 'Tidak ada detail.' }}</p>
                        </div>
                        @if($bahan->deskripsi_bahan)
                        <div class="md:col-span-2">
                            <p class="text-gray-400">Deskripsi Bahan</p>
                            <p class="text-white font-medium">{{ $bahan->deskripsi_bahan }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- FIFO Stock Layers Card -->
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
                    <h3 class="text-lg font-semibold text-white mb-4"><i class="fa-solid fa-layer-group text-orange-400 mr-2"></i>Riwayat Stok & Harga (FIFO)</h3>
                    <div class="overflow-x-auto custom-scrollbar">
                        @if($bahan->stockLayers && $bahan->stockLayers->count() > 0)
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-800 text-xs text-gray-400 uppercase">
                                <tr>
                                    <th class="px-4 py-2">Tgl. Masuk</th>
                                    <th class="px-4 py-2 text-right">Jml. Awal</th>
                                    <th class="px-4 py-2 text-right">Sisa Stok</th>
                                    <th class="px-4 py-2 text-right">Harga Beli / Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($bahan->stockLayers->sortByDesc('created_at') as $layer)
                                <tr class="hover:bg-gray-700/50">
                                    <td class="px-4 py-2 text-gray-300">{{ \Carbon\Carbon::parse($layer->tanggal_masuk)->isoFormat('D MMM YYYY') }}</td>
                                    <td class="px-4 py-2 text-right text-gray-300">{{ number_format($layer->qty_masuk, 2, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right font-bold {{ $layer->qty_sisa > 0 ? 'text-orange-400' : 'text-gray-500' }}">{{ number_format($layer->qty_sisa, 2, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right text-gray-300">Rp {{ number_format($layer->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="text-center text-gray-400 py-8">
                            <i class="fa-solid fa-box-open text-3xl mb-2"></i>
                            <p>Tidak ada riwayat stok untuk item ini.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Anda Yakin?',
            text: "Tindakan ini akan menghapus material beserta seluruh riwayat stoknya. Data tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF5252',
            cancelButtonColor: '#4A5568',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#1A1C24',
            color: '#FFFFFF'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush