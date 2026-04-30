<!-- ==================== FORM PRODUK JADI ==================== -->


<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Form Tambah Produk -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-lg p-4">
        <form id="form-produk" action="{{ route('admin.pengaturan.store') }}" method="POST">
<input type="hidden" name="tipe_master" value="produk">

            @csrf
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <input type="text" name="nama_produk_jadi" required placeholder="Contoh: Dompet Kulit Premium" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            @error('nama_produk_jadi')
                <div class="text-red-400 text-xs mt-1">{{ $message }}</div>
            @enderror

            <hr class="border-zinc-700 my-4">

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-1.5 px-3 rounded-lg transition-colors flex items-center justify-center text-xs">
                <i class="fa-solid fa-plus mr-1"></i>
                Simpan
            </button>
        </form>
    </div>
    
    <!-- Tabel Data Produk -->
    <div class="md:col-span-2 bg-neutral-900 border border-zinc-800 rounded-lg p-4">
        <h4 class="text-xs font-medium mb-3 text-white">Daftar Produk Jadi</h4>
        <div class="overflow-x-auto">
            <table id="table-produk" class="w-full text-left text-xs">
                <thead class="bg-zinc-800 text-zinc-400">
                    <tr>
                        <th class="px-3 py-2 rounded-tl-lg w-12">No</th>
                        <th class="px-3 py-2">Nama Produk</th>
                        <th class="px-3 py-2 rounded-tr-lg w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-zinc-300 divide-y divide-zinc-800">
                    @forelse($produkJadis as $index => $produk)
                    <tr class="hover:bg-zinc-800/50">
                        <td class="px-3 py-2">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 font-medium">{{ $produk->nama_produk }}</td>
                        <td class="px-3 py-2">
                            <form action="{{ route('admin.pengaturan.store') }}" method="POST" class="inline">
<input type="hidden" name="tipe_master" value="produk">
<input type="hidden" name="hapus_produk_id" value="{{ $produk->id }}">

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
