<!-- ==================== FORM SHIFT & OPERASIONAL ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center">
    <i class="fa-solid fa-clock mr-2 text-indigo-400"></i>
    Jam Operasional & Shift Pabrik
</h2>
<p class="text-xs text-zinc-400 mb-4">Atur jadwal kerja dan sistem shift untuk operator produksi</p>

@if(session('success'))
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i>{{ session('success') }}
</div>
@endif

<form action="{{ route('admin.pengaturan.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Kiri: Jam Kerja Utama -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-xs font-medium mb-4 flex items-center text-indigo-400">
                <i class="fa-solid fa-business-time mr-2"></i>
                Jam Kerja Utama
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Jam Buka Pabrik</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                        <input type="time" name="jam_buka" value="{{ $pengaturanCollection['jam_buka'] ?? '08:00' }}" class="w-full bg-zinc-800 border border-zinc-700 rounded pl-10 pr-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Jam Tutup Pabrik</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                            <i class="fa-solid fa-clock"></i>
                        </span>
                        <input type="time" name="jam_tutup" value="{{ $pengaturanCollection['jam_tutup'] ?? '17:00' }}" class="w-full bg-zinc-800 border border-zinc-700 rounded pl-10 pr-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Sistem Shift -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-xs font-medium mb-4 flex items-center text-indigo-400">
                <i class="fa-solid fa-layer-group mr-2"></i>
                Sistem Shift
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Terapkan Sistem Shift?</label>
                    <select name="sistem_shift" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <option value="0" {{ ($pengaturanCollection['sistem_shift'] ?? '0') == '0' ? 'selected' : '' }}>Tidak Aktif (Jam Kerja Normal)</option>
                        <option value="1" {{ ($pengaturanCollection['sistem_shift'] ?? '0') == '1' ? 'selected' : '' }}>Aktif (Gunakan Shift Pagi/Malam)</option>
                    </select>
                    <p class="text-xs text-zinc-500 mt-1">Jika aktif, laporan produksi akan mencatat pada shift mana barang tersebut diselesaikan.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-6 rounded-lg transition-colors flex items-center text-sm shadow-lg shadow-blue-900/20">
            <i class="fa-solid fa-save mr-2"></i>
            Simpan Jadwal
        </button>
    </div>
</form>