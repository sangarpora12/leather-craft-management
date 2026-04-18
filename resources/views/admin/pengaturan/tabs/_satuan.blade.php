<!-- ==================== FORM SATUAN & KATEGORI ==================== -->
<h2 class="text-sm font-medium mb-2 flex items-center">
    <i class="fa-solid fa-ruler-combined mr-2 text-purple-400"></i>
    Master Satuan & Kategori
</h2>
<p class="text-xs text-zinc-400 mb-4">Kelola satuan ukuran dan kategori untuk bahan baku dan produk jadi</p>

<!-- AREA 1: SATUAN -->
<div class="mb-6">
    <h3 class="text-xs font-medium mb-3 text-purple-400 flex items-center">
        <i class="fa-solid fa-ruler mr-1"></i>
        Satuan
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Kolom Kiri: Form Tambah Satuan -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Tambah Satuan Baru</h4>
            <form id="form-satuan" action="{{ route('admin.satuan.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="nama_satuan" required placeholder="Contoh: Meter, Kg" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-purple-500">
                    </div>
                </div>
                @error('nama_satuan')
                    <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                @enderror

                <hr class="border-zinc-700 my-4">

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-medium py-1.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
                    <i class="fa-solid fa-plus mr-1"></i>
                    Simpan
                </button>
            </form>
        </div>
        
        <!-- Kolom Kanan: Tabel Data Satuan -->
        <div class="md:col-span-2 bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Daftar Satuan</h4>
            <div class="overflow-x-auto">
                <table id="table-satuan" class="w-full text-left text-xs">
                    <thead class="bg-zinc-800 text-zinc-400">
                        <tr>
                            <th class="px-3 py-2 rounded-tl-lg w-12">No</th>
                            <th class="px-3 py-2">Nama Satuan</th>
                            <th class="px-3 py-2 rounded-tr-lg w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-zinc-300 divide-y divide-zinc-800">
                        @forelse($satuans as $index => $satuan)
                        <tr class="hover:bg-zinc-800/50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $satuan->nama_satuan }}</td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.satuan.destroy', $satuan->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 p-1 rounded hover:bg-red-900/30 transition-colors text-xs" onclick="return confirm('Yakin hapus?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="3" class="px-3 py-6 text-center text-zinc-500 text-xs">
                                <i class="fa-solid fa-inbox text-xl mb-1 block"></i>
                                Belum ada data.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- AREA 2: KATEGORI -->
<div class="mb-6">
    <h3 class="text-xs font-medium mb-3 text-blue-400 flex items-center">
        <i class="fa-solid fa-tags mr-1"></i>
        Kategori
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Kolom Kiri: Form Tambah Kategori -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Tambah Kategori Baru</h4>
            <form id="form-kategori" action="{{ route('admin.kategori.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Tipe Kategori <span class="text-red-400">*</span></label>
                    <select name="tipe" required class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="bahan">Bahan</option>
                        <option value="produk">Produk</option>
                    </select>
                    @error('tipe')
                        <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="nama_kategori" required placeholder="Contoh: Kulit Sapi, Dompet" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-purple-500">
                    </div>
                </div>
                @error('nama_kategori')
                    <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                @enderror

                <hr class="border-zinc-700 my-4">

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-medium py-1.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
                    <i class="fa-solid fa-plus mr-1"></i>
                    Simpan
                </button>
            </form>
        </div>
        
        <!-- Kolom Kanan: Tabel Data Kategori -->
        <div class="md:col-span-2 bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Daftar Kategori</h4>
            <div class="overflow-x-auto">
                <table id="table-kategori" class="w-full text-left text-xs">
                    <thead class="bg-zinc-800 text-zinc-400">
                        <tr>
                            <th class="px-3 py-2 rounded-tl-lg w-12">No</th>
                            <th class="px-3 py-2">Nama Kategori</th>
                            <th class="px-3 py-2">Tipe</th>
                            <th class="px-3 py-2 rounded-tr-lg w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-zinc-300 divide-y divide-zinc-800">
                        @forelse($kategories as $index => $kategori)
                        <tr class="hover:bg-zinc-800/50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $kategori->nama_kategori }}</td>
                            <td class="px-3 py-2 text-zinc-400">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $kategori->tipe == 'bahan' ? 'bg-blue-900/50 text-blue-300' : 'bg-green-900/50 text-green-300' }}">
                                    {{ ucfirst($kategori->tipe) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 p-1 rounded hover:bg-red-900/30 transition-colors text-xs" onclick="return confirm('Yakin hapus?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="4" class="px-3 py-6 text-center text-zinc-500 text-xs">
                                <i class="fa-solid fa-inbox text-xl mb-1 block"></i>
                                Belum ada data.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- AREA 3: WARNA -->
<div class="mb-6">
    <h3 class="text-xs font-medium mb-3 text-green-400 flex items-center">
        <i class="fa-solid fa-palette mr-1"></i>
        Warna
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Kolom Kiri: Form Tambah Warna -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Tambah Warna Baru</h4>
            <form id="form-warna" action="{{ route('admin.warna.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="nama_warna" required placeholder="Contoh: Merah, Biru" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-green-500">
                    </div>
                </div>
                @error('nama_warna')
                    <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                @enderror

                <hr class="border-zinc-700 my-4">

                <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-medium py-1.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
                    <i class="fa-solid fa-plus mr-1"></i>
                    Simpan
                </button>
            </form>
        </div>
        
        <!-- Kolom Kanan: Tabel Data Warna -->
        <div class="md:col-span-2 bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Daftar Warna</h4>
            <div class="overflow-x-auto">
                <table id="table-warna" class="w-full text-left text-xs">
                    <thead class="bg-zinc-800 text-zinc-400">
                        <tr>
                            <th class="px-3 py-2 rounded-tl-lg w-12">No</th>
                            <th class="px-3 py-2">Nama Warna</th>
                            <th class="px-3 py-2 rounded-tr-lg w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-zinc-300 divide-y divide-zinc-800">
                        @forelse($warnas as $index => $warna)
                        <tr class="hover:bg-zinc-800/50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $warna->nama_warna }}</td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.warna.destroy', $warna->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 p-1 rounded hover:bg-red-900/30 transition-colors text-xs" onclick="return confirm('Yakin hapus?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="3" class="px-3 py-6 text-center text-zinc-500 text-xs">
                                <i class="fa-solid fa-inbox text-xl mb-1 block"></i>
                                Belum ada data.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- AREA 4: MOTIF -->
<div>
    <h3 class="text-xs font-medium mb-3 text-yellow-400 flex items-center">
        <i class="fa-solid fa-star-of-life mr-1"></i>
        Motif
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Kolom Kiri: Form Tambah Motif -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Tambah Motif Baru</h4>
            <form id="form-motif" action="{{ route('admin.motif.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="nama_motif" required placeholder="Contoh: Polos, Croco" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-yellow-500">
                    </div>
                </div>
                @error('nama_motif')
                    <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
                @enderror

                <hr class="border-zinc-700 my-4">

                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-500 text-white font-medium py-1.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
                    <i class="fa-solid fa-plus mr-1"></i>
                    Simpan
                </button>
            </form>
        </div>
        
        <!-- Kolom Kanan: Tabel Data Motif -->
        <div class="md:col-span-2 bg-neutral-900 border border-zinc-800 rounded-lg p-4">
            <h4 class="text-xs font-medium mb-3 text-white">Daftar Motif</h4>
            <div class="overflow-x-auto">
                <table id="table-motif" class="w-full text-left text-xs">
                    <thead class="bg-zinc-800 text-zinc-400">
                        <tr>
                            <th class="px-3 py-2 rounded-tl-lg w-12">No</th>
                            <th class="px-3 py-2">Nama Motif</th>
                            <th class="px-3 py-2 rounded-tr-lg w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-zinc-300 divide-y divide-zinc-800">
                        @forelse($motifs as $index => $motif)
                        <tr class="hover:bg-zinc-800/50">
                            <td class="px-3 py-2">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $motif->nama_motif }}</td>
                            <td class="px-3 py-2">
                                <form action="{{ route('admin.motif.destroy', $motif->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 p-1 rounded hover:bg-red-900/30 transition-colors text-xs" onclick="return confirm('Yakin hapus?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="3" class="px-3 py-6 text-center text-zinc-500 text-xs">
                                <i class="fa-solid fa-inbox text-xl mb-1 block"></i>
                                Belum ada data.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Script disederhanakan untuk sementara waktu
</script>
@endpush