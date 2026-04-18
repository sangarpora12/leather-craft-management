@php
    $page = 'pengaturan'; // Penanda menu aktif
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfigurasi Sistem - Berliano Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        
        /* Hide active tab content logic */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-black text-white flex h-screen overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-6 md:p-8 md:ml-64 bg-black">
        
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white">Konfigurasi Sistem</h2>
            <p class="text-gray-400 text-sm mt-1">Atur parameter dasar aplikasi, kategori, dan variabel global.</p>
        </div>

        <div class="flex border-b border-white/10 mb-8">
            <button onclick="switchTab('produk')" id="tab-btn-produk" class="px-6 py-3 text-sm font-bold text-orange-500 border-b-2 border-orange-500 transition-colors">
                Manajemen Produk
            </button>
            <button onclick="switchTab('bahan')" id="tab-btn-bahan" class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-white border-b-2 border-transparent hover:border-white/20 transition-colors">
                Bahan Baku & Satuan
            </button>
            <button onclick="switchTab('lainnya')" id="tab-btn-lainnya" class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-white border-b-2 border-transparent hover:border-white/20 transition-colors">
                Operator & Laporan
            </button>
        </div>

        <div id="tab-produk" class="tab-content active space-y-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Kategori Produk
                        </h3>
                        <button class="text-xs bg-blue-600/20 text-blue-400 px-3 py-1.5 rounded hover:bg-blue-600 hover:text-white transition">+ Tambah</button>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-black/40 border border-white/5 p-3 rounded-lg group hover:border-white/20 transition">
                            <span class="text-sm text-gray-300">Tas Selempang (Sling Bag)</span>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button class="text-gray-500 hover:text-white"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                <button class="text-gray-500 hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-black/40 border border-white/5 p-3 rounded-lg group hover:border-white/20 transition">
                            <span class="text-sm text-gray-300">Dompet (Wallet & Card Holder)</span>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button class="text-gray-500 hover:text-white"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                <button class="text-gray-500 hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-black/40 border border-white/5 p-3 rounded-lg group hover:border-white/20 transition">
                            <span class="text-sm text-gray-300">Ikat Pinggang (Belt)</span>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button class="text-gray-500 hover:text-white"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                <button class="text-gray-500 hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Alasan Keluar Produk
                        </h3>
                        <button class="text-xs bg-orange-600/20 text-orange-400 px-3 py-1.5 rounded hover:bg-orange-600 hover:text-white transition">+ Tambah</button>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-black/40 border border-white/5 p-3 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-white">Selesai (Lulus QC)</span>
                                <span class="text-[9px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded border border-green-500/30">Default</span>
                            </div>
                            <span class="text-[10px] text-gray-500 italic">Otomatis saat SPK selesai</span>
                        </div>
                        <div class="flex justify-between items-center bg-black/40 border border-white/5 p-3 rounded-lg group hover:border-white/20 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-300">Reject (QC Failed)</span>
                            </div>
                            <button class="text-gray-500 hover:text-white opacity-0 group-hover:opacity-100 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                        </div>
                        <div class="flex justify-between items-center bg-black/40 border border-white/5 p-3 rounded-lg group hover:border-white/20 transition">
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-300">Sampel / Cacat Produksi</span>
                            </div>
                            <button class="text-gray-500 hover:text-white opacity-0 group-hover:opacity-100 transition"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end pt-6 border-t border-white/10">
                <button class="bg-orange-600 hover:bg-orange-500 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-orange-900/20 flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
            </div>

        </div>

        <div id="tab-bahan" class="tab-content text-center py-20 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p>Pengaturan Satuan & Kategori Bahan Baku akan muncul di sini.</p>
        </div>

        <div id="tab-lainnya" class="tab-content text-center py-20 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p>Pengaturan Hak Akses Operator & Format Laporan.</p>
        </div>

    </main>

    <script>
        function switchTab(tabId) {
            // Sembunyikan semua konten tab
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            // Tampilkan konten tab yang dipilih
            document.getElementById('tab-' + tabId).classList.add('active');

            // Reset style tombol
            const buttons = document.querySelectorAll('button[id^="tab-btn-"]');
            buttons.forEach(btn => {
                btn.className = "px-6 py-3 text-sm font-bold text-gray-400 hover:text-white border-b-2 border-transparent hover:border-white/20 transition-colors";
            });

            // Set style tombol aktif
            const activeBtn = document.getElementById('tab-btn-' + tabId);
            activeBtn.className = "px-6 py-3 text-sm font-bold text-orange-500 border-b-2 border-orange-500 transition-colors";
        }
    </script>

</body>
</html>