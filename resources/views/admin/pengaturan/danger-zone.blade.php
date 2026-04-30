@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-zinc-900 to-zinc-950">
    <div class="p-6 pt-2">
        <!-- Header Kembali -->
        <a href="{{ route('admin.pengaturan.index') }}" class="inline-flex items-center gap-2 mb-8 p-3 bg-zinc-800 rounded-xl hover:bg-zinc-700 transition-all text-zinc-300 hover:text-white shadow-lg">
            <i class="fa-solid fa-arrow-left w-4"></i>
            <span>Kembali ke Pengaturan</span>
        </a>

        <div class="max-w-2xl">
            <div class="text-center mb-12">
                <div class="w-24 h-24 bg-red-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 border-4 border-red-500/30 shadow-2xl">
                    <i class="fa-solid fa-bomb text-red-400 text-5xl"></i>
                </div>
                <h1 class="text-4xl font-black bg-gradient-to-r from-red-400 to-orange-500 bg-clip-text text-transparent mb-4">⚠️ DANGER ZONE</h1>
                <p class="text-xl text-zinc-400 mb-2">Aksi ini akan menghapus data secara permanen</p>
                <p class="text-zinc-500">Pastikan Anda admin dengan akses penuh sebelum lanjut</p>
            </div>

            <!-- Reset Data (Soft Reset) -->
            <div class="bg-gradient-to-br from-orange-500/5 to-red-500/5 border border-orange-500/30 rounded-3xl p-8 mb-10 shadow-2xl backdrop-blur-sm">
                <h3 class="text-2xl font-bold text-orange-400 mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset Data Soft
                </h3>
                <div class="space-y-4 text-zinc-300 mb-8 p-6 bg-zinc-900/50 rounded-2xl border border-zinc-700">
                    <div><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Set semua stok bahan ke 0</div>
                    <div><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Hapus pengadaan PENDING</div>
                    <div><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Hapus bahan masuk unverified</div>
                    <div><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Clear cache & notifications</div>
                    <div><i class="fa-solid fa-times text-red-400 mr-2"></i> <strong>DATA UTAMA AMAN</strong></div>
                </div>
                
                <form action="{{ route('admin.pengaturan.danger-zone.reset') }}" method="POST" onsubmit="return confirm('Yakin reset soft data? Data pending akan hilang!')">
                    @csrf
                    <div class="flex items-center gap-4 mb-6">
                        <label class="flex items-center gap-3 p-4 bg-zinc-900 rounded-xl border-2 border-orange-500 cursor-pointer hover:border-orange-400 transition-all group">
                            <input type="checkbox" name="confirm" value="yes" required class="w-6 h-6 text-orange-500 rounded border-orange-500/50 bg-zinc-800 focus:ring-orange-500 focus:ring-2">
                            <span class="font-bold text-orange-400 group-hover:text-orange-300 text-lg">Saya mengerti risiko & konfirmasi</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white font-black py-5 rounded-2xl shadow-2xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all duration-300 text-lg border-2 border-orange-500">
                        <i class="fa-solid fa-rotate-left mr-3"></i>
                        RESET DATA SOFT
                    </button>
                </form>
            </div>

            <!-- Super Reset (Hard Reset) -->
            <div class="bg-gradient-to-br from-red-500/10 to-zinc-900/50 border-2 border-red-500/50 rounded-3xl p-8 shadow-2xl backdrop-blur-sm">
                <h3 class="text-2xl font-bold text-red-400 mb-6 flex items-center gap-3">
                    <i class="fa-solid fa-skull-crossbones"></i>
                    Super Reset (HARD)
                </h3>
                <div class="space-y-4 text-zinc-300 mb-8 p-6 bg-zinc-900/70 rounded-2xl border border-red-500/30">
                    <div><i class="fa-solid fa-times text-red-400 mr-2"></i> <strong>migrate:refresh --seed</strong> - HAPUS SEMUA DATA</div>
                    <div><i class="fa-solid fa-times text-red-400 mr-2"></i> Database kembali fresh + seed</div>
                    <div><i class="fa-solid fa-times text-red-400 mr-2"></i> <strong>Semua transaksi hilang permanen</strong></div>
                </div>
                
                <form action="{{ route('admin.pengaturan.danger-zone.super-reset') }}" method="POST" onsubmit="return confirm('⚠️ SUPER RESET akan HAPUS SEMUA DATA! Masukkan password: admin123')">
                    @csrf
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-red-300 mb-2">Konfirmasi Password Admin</label>
                        <input type="password" name="password" required placeholder="admin123" 
                               class="w-full bg-zinc-900 border-2 border-red-500 rounded-xl px-5 py-4 text-lg text-white focus:ring-2 focus:ring-red-400 focus:border-red-400 transition-all" autocomplete="off">
                        <label class="flex items-center gap-3 p-4 bg-zinc-900 rounded-xl border-2 border-red-500 cursor-pointer hover:border-red-400 transition-all group">
                            <input type="checkbox" name="confirm_super" value="destroy" required class="w-6 h-6 text-red-500 rounded border-red-500/50 bg-zinc-800 focus:ring-red-500 focus:ring-2">
                            <span class="font-black text-red-400 group-hover:text-red-300 text-xl">AKU MENGERTI - HAPUS SEMUA DATA</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full mt-8 bg-gradient-to-r from-red-600 to-red-700 text-white font-black py-6 rounded-3xl shadow-2xl hover:shadow-red-500/50 hover:scale-[1.02] transition-all duration-300 text-xl border-4 border-red-500">
                        <i class="fa-solid fa-skull-crossbones mr-3"></i>
                        SUPER RESET DATABASE
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Animasi danger zone */
@keyframes pulse-red {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
    50% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
}
.danger-pulse {
    animation: pulse-red 2s infinite;
}
</style>
@endsection

