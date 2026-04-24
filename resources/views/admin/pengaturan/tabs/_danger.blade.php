<!-- ==================== FORM DANGER ZONE ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center text-red-500">
    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
    Danger Zone & Pemeliharaan
</h2>

@if(session('success'))
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i>{{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-900/30 border border-red-800 text-red-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-x-circle mr-1"></i>{{ session('error') }}
</div>
@endif

<p class="text-red-400 text-xs mb-6">
    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
    Hati-hati! Tindakan di area ini dapat mengubah atau menghapus data secara permanen.
</p>

<!-- Card: Reset Database Pilihan -->
<div class="bg-zinc-800/50 border border-red-700/50 rounded-lg p-4 mb-4">
    <h3 class="text-white font-medium text-sm mb-3">
        <i class="fa-solid fa-database mr-2 text-red-400"></i>
        Reset Database Pilihan
    </h3>
    
    <form id="resetDatabaseForm" action="{{ route('admin.pengaturan.resetData') }}" method="POST">
        @csrf
        
        <div class="space-y-3 mb-4">
            <!-- Checkbox: Transaksi & SPK -->
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" name="reset_transaksi" value="1" class="mt-1 w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-red-500 focus:ring-red-500 focus:ring-offset-zinc-800">
                <div class="flex-1">
                    <span class="text-zinc-300 text-xs font-medium group-hover:text-white">Hapus Semua Transaksi & SPK</span>
                    <p class="text-zinc-500 text-xs mt-0.5">Layer FIFO, Riwayat Produksi, Pengadaan, Produk Keluar, Notifications, Activity Log, OCR Results</p>
                </div>
            </label>
            
            <!-- Checkbox: Master Bahan Baku -->
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" name="reset_bahan" value="1" class="mt-1 w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-red-500 focus:ring-red-500 focus:ring-offset-zinc-800">
                <div class="flex-1">
                    <span class="text-zinc-300 text-xs font-medium group-hover:text-white">Hapus Semua Master Bahan Baku</span>
                    <p class="text-zinc-500 text-xs mt-0.5">Master Bahan Baku dan Stok Layer (FIFO)</p>
                </div>
            </label>
            
            <!-- Checkbox: Master Produk & BOM -->
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" name="reset_produk" value="1" class="mt-1 w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-red-500 focus:ring-red-500 focus:ring-offset-zinc-800">
                <div class="flex-1">
                    <span class="text-zinc-300 text-xs font-medium group-hover:text-white">Hapus Semua Master Produk & BOM</span>
                    <p class="text-zinc-500 text-xs mt-0.5">Master Produk Jadi dan Bill of Materials (BOM)</p>
                </div>
            </label>
            
            <!-- Checkbox: Master Supplier -->
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" name="reset_supplier" value="1" class="mt-1 w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-red-500 focus:ring-red-500 focus:ring-offset-zinc-800">
                <div class="flex-1">
                    <span class="text-zinc-300 text-xs font-medium group-hover:text-white">Hapus Semua Master Supplier</span>
                    <p class="text-zinc-500 text-xs mt-0.5">Master Supplier dan Data Harga Katalog</p>
                </div>
            </label>
        </div>
        
        <!-- Submit Button -->
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg text-xs transition-colors flex items-center justify-center gap-2">
            <i class="fa-solid fa-bolt"></i>
            Eksekusi Reset
        </button>
    </form>
</div>

<!-- Info Card -->
<div class="bg-zinc-800/50 border border-zinc-700 rounded-lg p-4">
    <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-info text-red-400 text-sm"></i>
        </div>
        <div>
            <h4 class="text-white font-medium">Informasi Pemeliharaan</h4>
            <p class="text-zinc-400 text-xs mt-1">
                Backup database secara berkala untuk menjaga keamanan data. 
                Reset data transaksi hanya boleh dilakukan oleh Administrator utama.
                Pastikan Anda memilih data yang ingin direset dengan mencentang checkbox yang diperlukan.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('resetDatabaseForm').addEventListener('submit', function(e) {
        // Check if at least one checkbox is selected
        const checkboxes = this.querySelectorAll('input[type="checkbox"]:checked');
        
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Silakan pilih setidaknya satu opsi reset data!');
            return false;
        }
        
        // Show confirmation dialog
        const confirmMessage = '⚠️ PERINGATAN PENTING! ⚠️\n\n' +
            'Aksi ini TIDAK BISA DIBATALKAN!\n\n' +
            'Data yang dipilih akan dihapus secara PERMANEN dari database.\n\n' +
            'Apakah Anda yakin ingin melanjutkan?';
        
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
        
        // Additional confirmation
        const doubleConfirm = confirm('Ketik "YA" untuk mengkonfirmasi reset data.');
        if (!doubleConfirm) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
</script>
@endpush