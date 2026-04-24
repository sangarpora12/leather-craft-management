<!-- ==================== FORM KEUANGAN ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center">
    <i class="fa-solid fa-coins mr-2 text-yellow-400"></i>
    Pengaturan Keuangan
</h2>
<p class="text-xs text-zinc-400 mb-4">Kelola PPN, margin keuntungan, dan hitung harga jual</p>

@if(session('success'))
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i>{{ session('success') }}
</div>
@endif

<form action="{{ route('admin.pengaturan.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Kiri: PPN & Margin -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-xs font-medium mb-4 flex items-center text-yellow-400">
                <i class="fa-solid fa-percent mr-2"></i>
                PPN & Margin
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">PPN (%)</label>
                    <input type="number" name="ppn" value="{{ $pengaturanCollection['ppn'] ?? '11' }}" step="0.1" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                    <p class="text-xs text-zinc-500 mt-1">Persentase PPN untuk harga jual.</p>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Margin Keuntungan Default (%)</label>
                    <input type="number" name="margin_default" value="{{ $pengaturanCollection['margin_default'] ?? '30' }}" step="0.1" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                    <p class="text-xs text-zinc-500 mt-1">Margin default untuk perhitungan harga jual.</p>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Harga Jual -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-xs font-medium mb-4 flex items-center text-yellow-400">
                <i class="fa-solid fa-tags mr-2"></i>
                Pengaturan Harga Jual
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Metode Hitung Harga</label>
                    <select name="metode_harga" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                        <option value="manual" {{ ($pengaturanCollection['metode_harga'] ?? '') == 'manual' ? 'selected' : '' }}>Manual (Input Harga Langsung)</option>
                        <option value="auto_markup" {{ ($pengaturanCollection['metode_harga'] ?? '') == 'auto_markup' ? 'selected' : '' }}>Otomatis (Markup dari Harga Pokok)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Pembulatan Harga</label>
                    <select name="pembulatan_harga" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-yellow-500">
                        <option value="100" {{ ($pengaturanCollection['pembulatan_harga'] ?? '') == '100' ? 'selected' : '' }}>Ratusan</option>
                        <option value="500" {{ ($pengaturanCollection['pembulatan_harga'] ?? '') == '500' ? 'selected' : '' }}>Lima Ratusan</option>
                        <option value="1000" {{ ($pengaturanCollection['pembulatan_harga'] ?? '') == '1000' ? 'selected' : '' }}>Ribuan</option>
                        <option value="none" {{ ($pengaturanCollection['pembulatan_harga'] ?? '') == 'none' ? 'selected' : '' }}>Tidak Ada</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-6 rounded-lg transition-colors flex items-center text-sm shadow-lg shadow-blue-900/20">
            <i class="fa-solid fa-save mr-2"></i>
            Simpan Pengaturan
        </button>
    </div>
</form>