@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-zinc-900">
    <div class="p-6 pt-2">
        <!-- Header Kembali -->
        <a href="{{ route('admin.pengaturan.index') }}" class="inline-flex items-center gap-2 mb-8 p-3 bg-zinc-800 rounded-xl hover:bg-zinc-700 transition-all text-zinc-300 hover:text-white">
            <i class="fa-solid fa-arrow-left w-4"></i>
            <span>Kembali ke Pengaturan</span>
        </a>

        <div class="max-w-2xl">
            <h1 class="text-3xl font-bold text-white mb-8">Pengaturan Notifikasi</h1>
            
            <div class="bg-zinc-800 rounded-2xl p-8 border border-zinc-700 shadow-xl">
                <form action="{{ route('admin.pengaturan.notifikasi.store') }}" method="POST">
                    @csrf
                    
                    <!-- Notifikasi Stok Kritis -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-yellow-500/5 to-orange-500/5 rounded-xl border border-yellow-500/20">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-exclamation-triangle text-yellow-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white mb-1">Stok Kritis Bahan Baku</h3>
                                <p class="text-zinc-400">Kirim notifikasi saat stok bahan mencapai batas minimum</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-3 p-3 bg-zinc-900 rounded-lg border border-zinc-600 cursor-pointer hover:border-zinc-500 transition-all group">
                                <input type="radio" name="notif_stok_kritis" value="1" {{ old('notif_stok_kritis', ($notifikasi['notif_stok_kritis'] ?? 1) == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-500 bg-zinc-700 border-zinc-600 rounded focus:ring-emerald-500 focus:ring-2">
                                <span class="text-white font-medium group-hover:text-emerald-400">Aktif</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-zinc-900 rounded-lg border border-zinc-600 cursor-pointer hover:border-zinc-500 transition-all group">
                                <input type="radio" name="notif_stok_kritis" value="0" {{ old('notif_stok_kritis', ($notifikasi['notif_stok_kritis'] ?? 1) == '0' ? 'checked' : '' }} class="w-5 h-5 text-gray-500 bg-zinc-700 border-zinc-600 rounded focus:ring-gray-500 focus:ring-2">
                                <span class="text-zinc-400 group-hover:text-gray-300">Nonaktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Notifikasi Bahan Masuk -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-emerald-500/5 to-teal-500/5 rounded-xl border border-emerald-500/20">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-box-open text-emerald-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white mb-1">Bahan Baru Masuk Gudang</h3>
                                <p class="text-zinc-400">Beritahu admin & operator saat bahan baru ditambahkan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-3 p-3 bg-zinc-900 rounded-lg border border-zinc-600 cursor-pointer hover:border-zinc-500 transition-all group">
                                <input type="radio" name="notif_bahan_masuk" value="1" {{ old('notif_bahan_masuk', ($notifikasi['notif_bahan_masuk'] ?? 1) == '1' ? 'checked' : '' }} class="w-5 h-5 text-emerald-500 bg-zinc-700 border-zinc-600 rounded focus:ring-emerald-500 focus:ring-2">
                                <span class="text-white font-medium group-hover:text-emerald-400">Aktif</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-zinc-900 rounded-lg border border-zinc-600 cursor-pointer hover:border-zinc-500 transition-all group">
                                <input type="radio" name="notif_bahan_masuk" value="0" {{ old('notif_bahan_masuk', ($notifikasi['notif_bahan_masuk'] ?? 1) == '0' ? 'checked' : '' }} class="w-5 h-5 text-gray-500 bg-zinc-700 border-zinc-600 rounded focus:ring-gray-500 focus:ring-2">
                                <span class="text-zinc-400 group-hover:text-gray-300">Nonaktif</span>
                            </label>
                        </div>
                    </div>

                    <!-- Telegram Bot -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-white mb-2">Telegram Bot Token</label>
                            <input type="text" name="telegram_bot_token" value="{{ old('telegram_bot_token', $notifikasi['telegram_bot_token']['nilai'] ?? '') }}" 
                                   class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all" 
                                   placeholder="BotFather token: 123456789:ABC...">
                            <p class="text-xs text-zinc-500 mt-1">Dapatkan dari @BotFather di Telegram</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-white mb-2">Channel ID Telegram</label>
                                <input type="text" name="telegram_channel_id" value="{{ old('telegram_channel_id', $notifikasi['telegram_channel_id']['nilai'] ?? '') }}" 
                                       class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all" 
                                       placeholder="@berliano_channel atau -1001234567890">
                                <p class="text-xs text-zinc-500 mt-1">Gunakan @userinfobot untuk dapatkan ID</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-white mb-2">Admin IDs (pisah koma)</label>
                                <input type="text" name="telegram_admins" value="{{ old('telegram_admins', $notifikasi['telegram_admins']['nilai'] ?? '') }}" 
                                       class="w-full bg-zinc-800 border border-zinc-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all" 
                                       placeholder="123456789, 987654321">
                                <p class="text-xs text-zinc-500 mt-1">ID Telegram admin yang terima notifikasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-8">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-pink-600 to-purple-600 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-pink-500/30 hover:scale-[1.02] transition-all duration-200">
                            <i class="fa-solid fa-save mr-2"></i>
                            Simpan Pengaturan
                        </button>
                        <a href="{{ route('admin.pengaturan.index') }}" class="flex-1 bg-zinc-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:bg-zinc-600 hover:scale-[1.02] transition-all duration-200 text-center flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

