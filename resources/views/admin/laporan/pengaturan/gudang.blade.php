@extends('layouts.admin')

@section('content')
<div class="text-zinc-100"> {{-- Hapus min-h-screen agar tidak nindih navbar --}}
    <div class="border-b border-zinc-800 bg-zinc-900 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Konfigurasi Gudang & Stok</h1>
                <p class="text-zinc-400 text-sm">Mega Configuration Center - Berliano Leather</p>
            </div>
            <a href="{{ route('admin.laporan.pengaturan.index') }}" class="bg-zinc-800 hover:bg-zinc-700 text-zinc-300 px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition-colors border border-zinc-700">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="p-6">
        {{-- Pesan Sukses HTML (Fix Kode Alien &#039;) --}}
        @if(session('success'))
        <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fa-solid fa-check-circle text-lg"></i>
            <span>{!! session('success') !!}</span>
        </div>
        @endif

        {{-- NOTE: route gudang belum terdaftar; pakai route pengaturan.store yang memang ada.
             Controller akan menangani field gudang termasuk stok_minimum_global. --}}
        <form action="{{ route('admin.laporan.pengaturan.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 hover:border-blue-500/50 transition-colors shadow-lg">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-bell text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Pusat Notifikasi</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg border border-zinc-700">
                            <div>
                                <p class="text-sm text-white font-medium">Peringatan Stok Menipis</p>
                                <p class="text-[10px] text-zinc-500">Kontrol kotak merah di Dashboard</p>
                            </div>
                            <select name="notif_stok_menipis" class="bg-zinc-900 border border-zinc-700 text-xs text-white rounded-md px-3 py-1.5 outline-none focus:border-blue-400">
                                <option value="1" {{ ($pengaturanCollection['notif_stok_menipis'] ?? '1') == '1' ? 'selected' : '' }}>AKTIF</option>
                                <option value="0" {{ ($pengaturanCollection['notif_stok_menipis'] ?? '') == '0' ? 'selected' : '' }}>OFF</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-zinc-800/50 rounded-lg border border-zinc-700">
                            <div>
                                <p class="text-sm text-white font-medium">Notifikasi Produksi</p>
                                <p class="text-[10px] text-zinc-500">Kontrol notifikasi ke Lonceng</p>
                            </div>
                            <select name="notif_produksi_selesai" class="bg-zinc-900 border border-zinc-700 text-xs text-white rounded-md px-3 py-1.5 outline-none focus:border-blue-400">
                                <option value="1" {{ ($pengaturanCollection['notif_produksi_selesai'] ?? '1') == '1' ? 'selected' : '' }}>AKTIF</option>
                                <option value="0" {{ ($pengaturanCollection['notif_produksi_selesai'] ?? '') == '0' ? 'selected' : '' }}>OFF</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 hover:border-purple-500/50 transition-colors">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-barcode text-purple-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Prefix Kode</h3>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1 uppercase">Prefix Bahan Baku</label>
                            <input type="text" name="prefix_bahan" value="{{ $pengaturanCollection['prefix_bahan'] ?? 'BHN-' }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white outline-none uppercase focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs text-zinc-400 mb-1 uppercase">Stok Minimum Global</label>
                            <input type="number" min="0" step="1" name="stok_minimum_global" value="{{ $pengaturanCollection['stok_minimum_global'] ?? ($pengaturanCollection['stok_minimum'] ?? '5') }}" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white outline-none focus:border-purple-500">
                            <p class="text-[10px] text-zinc-500 mt-2">Catatan: sistem memakai <b>stok_minimum_global</b> sebagai acuan kritis. (Key lama: <code>stok_minimum</code>)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg flex items-center gap-3 transition-all hover:scale-105">
                    <i class="fa-solid fa-save"></i> Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                html: "{!! session('success') !!}", {{-- Kuncinya: Pakai html: dan {!! !!} --}}
                icon: 'success',
                confirmButtonColor: '#16a34a',
                timer: 4000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection