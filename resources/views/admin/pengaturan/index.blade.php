@extends('layouts.admin')

@section('content')
<div class="bg-zinc-950 min-h-screen text-zinc-100">
    <div class="border-b border-zinc-800 bg-zinc-900 px-6 py-4">
        <h1 class="text-2xl font-bold text-white">Pengaturan Sistem</h1>
        <p class="text-zinc-400 text-sm">Kelola pengaturan aplikasi dan profil Anda</p>
    </div>

    <div class="p-6">
        <div id="menu-grid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @php
                $menus = [
                    ['id' => 'satuan', 'icon' => 'fa-ruler-combined', 'color' => 'purple', 'title' => 'Satuan & Kategori', 'desc' => 'Kelola satuan dan kategori'],
                    ['id' => 'gudang', 'icon' => 'fa-warehouse', 'color' => 'green', 'title' => 'Konfigurasi Gudang', 'desc' => 'Batas stok & auto-potong'],
                    ['id' => 'laporan', 'icon' => 'fa-file-pdf', 'color' => 'red', 'title' => 'Laporan & PDF', 'desc' => 'Format & template laporan'],
                    ['id' => 'keuangan', 'icon' => 'fa-coins', 'color' => 'yellow', 'title' => 'Keuangan', 'desc' => 'PPN & margin keuntungan'],
                    ['id' => 'notifikasi', 'icon' => 'fa-bell', 'color' => 'pink', 'title' => 'Notifikasi', 'desc' => 'Bot Telegram & stok kritis'],
                    ['id' => 'shift', 'icon' => 'fa-clock', 'color' => 'indigo', 'title' => 'Shift & Operasional', 'desc' => 'Jadwal buka/tutup pabrik'],
                    ['id' => 'hakakses', 'icon' => 'fa-shield-alt', 'color' => 'cyan', 'title' => 'Hak Akses', 'desc' => 'Manajemen role & izin'],
                    ['id' => 'log', 'icon' => 'fa-history', 'color' => 'teal', 'title' => 'Log Aktivitas', 'desc' => 'Riwayat aktivitas sistem'],
                    ['id' => 'danger', 'icon' => 'fa-exclamation-triangle', 'color' => 'red', 'title' => 'Danger Zone', 'desc' => 'Reset & hapus data'],
                ];
            @endphp

            @foreach($menus as $menu)
            <div onclick="bukaMenu('{{ $menu['id'] }}')" 
                 class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 cursor-pointer hover:scale-105 hover:bg-zinc-800 hover:border-{{ $menu['color'] }}-500 transition-all duration-300 group shadow-sm">
                <div class="w-14 h-14 bg-{{ $menu['color'] }}-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-{{ $menu['color'] }}-500/30 transition-colors">
                    <i class="fa-solid {{ $menu['icon'] }} text-2xl text-{{ $menu['color'] }}-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">{{ $menu['title'] }}</h3>
                <p class="text-sm text-zinc-400">{{ $menu['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <div id="area-form" class="hidden">
            <button onclick="kembaliKeMenu()" class="mb-4 flex items-center gap-2 text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Menu Pengaturan</span>
            </button>

            <div class="form-section hidden" id="form-satuan">@include('admin.pengaturan.tabs._satuan')</div>
            <div class="form-section hidden" id="form-gudang">@include('admin.pengaturan.tabs._gudang')</div>
            <div class="form-section hidden" id="form-laporan">@include('admin.pengaturan.tabs._laporan')</div>
            <div class="form-section hidden" id="form-keuangan">@include('admin.pengaturan.tabs._keuangan')</div>
            <div class="form-section hidden" id="form-notifikasi">@include('admin.pengaturan.tabs._notifikasi')</div>
            <div class="form-section hidden" id="form-shift">@include('admin.pengaturan.tabs._shift')</div>
            <div class="form-section hidden" id="form-hakakses">@include('admin.pengaturan.tabs._hakakses')</div>
            <div class="form-section hidden" id="form-log">@include('admin.pengaturan.tabs._log')</div>
            <div class="form-section hidden" id="form-danger">@include('admin.pengaturan.tabs._danger')</div>
        </div>
    </div>
</div>

<script>
    function bukaMenu(id_menu) {
        document.getElementById('menu-grid').classList.add('hidden');
        document.getElementById('area-form').classList.remove('hidden');
        document.querySelectorAll('.form-section').forEach(section => section.classList.add('hidden'));
        document.getElementById('form-' + id_menu).classList.remove('hidden');
        window.scrollTo(0, 0);
    }
    
    function kembaliKeMenu() {
        document.getElementById('area-form').classList.add('hidden');
        document.getElementById('menu-grid').classList.remove('hidden');
        window.scrollTo(0, 0);
    }
</script>
@endsection
