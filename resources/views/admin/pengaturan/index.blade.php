@extends('layouts.admin')

@section('content')
<div class="min-h-screen">
    <div class="p-6 pt-2">
        <div id="menu-grid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @php
                $menus = [
                    ['id' => 'satuan', 'icon' => 'fa-ruler-combined', 'color' => 'purple', 'title' => 'Satuan & Kategori', 'desc' => 'Kelola satuan dan kategori', 'onclick' => "bukaMenu('satuan')"],
                    ['id' => 'gudang', 'icon' => 'fa-warehouse', 'color' => 'green', 'title' => 'Konfigurasi Gudang', 'desc' => 'Batas stok, margin & harga jual', 'onclick' => "bukaMenu('gudang')"],
                  
                    ['id' => 'danger', 'icon' => 'fa-exclamation-triangle', 'color' => 'red', 'title' => 'Danger Zone', 'desc' => 'Reset & hapus data', 'onclick' => "bukaMenu('danger')"],
                ];
            @endphp

            @foreach($menus as $menu)
                <div onclick="{{ $menu['onclick'] }}" class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 cursor-pointer hover:scale-105 hover:bg-zinc-800 hover:border-{{ $menu['color'] }}-500 transition-all duration-300 group shadow-sm">
                    <div class="w-14 h-14 bg-{{ $menu['color'] }}-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-{{ $menu['color'] }}-500/30 transition-colors">
                        <i class="fa-solid {{ $menu['icon'] }} text-2xl text-{{ $menu['color'] }}-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">{{ $menu['title'] }}</h3>
                    <p class="text-sm text-zinc-400">{{ $menu['desc'] }}</p>
                </div>
            @endforeach
        </div>

        <div id="area-form" class="hidden">
            <!-- Tombol Kembali -->
            <button onclick="kembaliKeMenu()" class="mb-4 flex items-center gap-2 text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Menu Pengaturan</span>
            </button>

                        <!-- 2. Tab Konfigurasi Gudang -->
                        <div class="form-section hidden" id="form-gudang">
                @include('admin.pengaturan.tabs._gudang')
            </div>
            
            <!-- 3. Tab Notifikasi -->
            <div class="form-section hidden" id="form-notifikasi">
                @include('admin.pengaturan.tabs._notifikasi')
            </div>

            <!-- 4. Tab Danger Zone -->
            <div class="form-section hidden" id="form-danger">
                @include('admin.pengaturan.tabs._danger')
            </div>

            <!-- 1. Tab Satuan & Kategori (WAJIB ADA AGAR BISA TAMPIL) -->
            <div class="form-section hidden" id="form-satuan">
                @include('admin.pengaturan.tabs._satuan')
            </div>

    
        </div>
    </div>
</div>

<script>
    function bukaMenu(id_menu) {
        // Sembunyikan grid menu utama
        document.getElementById('menu-grid').classList.add('hidden');
        // Tampilkan area form
        document.getElementById('area-form').classList.remove('hidden');
        
        // Sembunyikan semua section form terlebih dahulu agar tidak tumpang tindih
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Tampilkan section yang ID-nya cocok dengan menu yang diklik
        const target = document.getElementById('form-' + id_menu);
        if (target) {
            target.classList.remove('hidden');
        }
        
        window.scrollTo(0, 0);
    }
    
    function kembaliKeMenu() {
        // Sembunyikan area form dan tampilkan kembali grid menu
        document.getElementById('area-form').classList.add('hidden');
        document.getElementById('menu-grid').classList.remove('hidden');
        window.scrollTo(0, 0);
    }
</script>
@endsection