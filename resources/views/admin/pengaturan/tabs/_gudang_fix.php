<!-- ==================== FORM KONFIGURASI GUDANG & STOK ==================== -->
<div class="flex items-center gap-3 mb-6">
    <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
        <i class="fa-solid fa-warehouse text-xl"></i>
    </div>
    <div>
        <h3 class="text-white font-bold text-lg">Konfigurasi Gudang & Stok</h3>
        <p class="text-zinc-400 text-sm">Atur parameter otomatisasi inventaris pabrik</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i>{{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-900/30 border border-red-800 text-red-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ session('error') }}
</div>
@endif

<form action="{{ route('admin.laporan.pengaturan.store') }}" method="POST" class="space-y-6" id="form-konfigurasi">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-zinc-400 text-sm mb-2">Batas Stok Minimum Global</label>
            <input type="number" name="stok_minimum" value="{{ $config->stok_minimum ?? 5 }}" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg focus:ring-green-500 border">
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">Metode Valuasi Aset</label>
            <select name="metode_stok" class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg border">
                <option value="fifo" {{ ($config->metode_stok ?? 'fifo') == 'fifo' ? 'selected' : '' }}>FIFO (First In First Out)</option>
                <option value="average" {{ ($config->metode_stok ?? '') == 'average' ? 'selected' : '' }}>Harga Rata-rata (Average)</option>
            </select>
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">Awalan Kode Bahan (Prefix)</label>
            <input type="text" name="prefix_bahan" value="{{ $config->prefix_bahan ?? 'BHN-' }}" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg uppercase border">
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">Awalan Kode Produk (Prefix)</label>
            <input type="text" name="prefix_produk" value="{{ $config->prefix_produk ?? 'PR-' }}" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg uppercase border">
        </div>
    </div>

    <div class="pt-4 border-t border-zinc-800 flex gap-3">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition flex items-center gap-2" id="btn-simpan">
            <i class="fa-solid fa-save"></i> Simpan Konfigurasi
        </button>
    </div>
</form>

<!-- Tombol Sinkronkan Data Lama -->
<div class="mt-4 pt-4 border-t border-zinc-800">
    <form action="{{ route('admin.laporan.pengaturan.updateAllCodes') }}" method="POST" id="form-sinkronkan">
        @csrf
        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2" id="btn-sinkronkan">
            <i class="fa-solid fa-sync"></i> Sinkronkan Data Lama
        </button>
    </form>
    <p class="text-zinc-500 text-xs mt-2">Tombol ini akan memperbarui semua kode produk dan bahan baku sesuai dengan prefix yang disetting.</p>
</div>

<!-- SweetAlert2 Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Notifikasi untuk tombol Simpan Konfigurasi
    document.getElementById('form-konfigurasi').addEventListener('submit', function(e) {
        // Biarkan form submit normally, SweetAlert akan muncul setelah redirect dengan session
    });

    // Tampilkan SweetAlert berdasarkan session flash message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        });
    @endif

    // Konfirmasi sebelum sinkronisasi
    document.getElementById('form-sinkronkan').addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Sinkronkan Kode?',
            text: "Apakah Anda yakin ingin menyinkronkan semua kode produk dan bahan? Ini akan mengubah kode lama sesuai prefix baru.",
            icon: 'warning',
            background: '#18181b',
            color: '#fff',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Sinkronkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form jika user mengkonfirmasi
                this.submit();
            }
        });
    });
</script>
