<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Informasi Berliano Leather</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #0F111A; color: #fff; font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2D3139; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4A4A4A; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #2D3139; border-radius: 999px; }
        
        [x-cloak] { display: none !important; }

        @media print {
            aside, header, nav, .sidebar-container { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; }
            body { background: white !important; color: black !important; }
            .bg-zinc-900, .rounded-xl, .border { background: white !important; border: 1px solid #ddd !important; }
        }

        /* Global Loading Overlay */
        #global-loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        #global-loading-overlay.active {
            display: flex;
        }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(16, 185, 129, 0.2);
            border-top: 4px solid #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            margin-top: 16px;
            color: #e2e8f0;
            font-size: 14px;
            font-weight: 500;
            animation: pulse-text 1.5s ease-in-out infinite;
        }
        @keyframes pulse-text {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
    </style>
</head>
<body class="bg-[#0F111A] antialiased">

    <!-- Global Loading Overlay -->
    <div id="global-loading-overlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Sedang memproses data...</div>
    </div>


    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        {{-- ================= SIDEBAR (FULL MENU) ================= --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#161B26] border-r border-[#2A3140] flex flex-col transition-transform duration-300 md:translate-x-0 md:static md:z-auto shadow-xl">
            
            <div class="h-[72px] flex flex-col justify-center px-6 border-b border-[#2A3140] bg-[#161B26]">
                @php
                    $namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
                    $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
                @endphp
                <div class="flex items-center gap-3 w-full">
                    @if($logoPerusahaan)
                        <img src="{{ asset('storage/' . $logoPerusahaan) }}" alt="Logo" class="h-8 w-8 shrink-0 object-cover rounded-md border border-[#2D3139]">
                    @else
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-gradient-to-br from-[#FD730D] to-orange-700 flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-gem text-xs text-white"></i>
                        </div>
                    @endif
                    <div class="flex flex-col min-w-0">
                        <h1 class="text-sm font-extrabold text-white truncate leading-tight">{{ $namaPerusahaan }}</h1>
                        <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Admin Panel</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto py-6 custom-scrollbar">
                {{-- 1. DASHBOARD --}}
                <div class="mb-6">
                    <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Utama</p>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-chart-line w-5"></i> Dashboard
                    </a>
                </div>

                {{-- 2. DATA MASTER --}}
                <div class="mb-6">
                    <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Data Master</p>
                    <a href="{{ route('admin.bahan-baku.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('admin.bahan-baku.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-box-open w-5"></i> Bahan Baku
                    </a>
                    <a href="{{ route('admin.produk-jadi.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('admin.produk-jadi.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-shirt w-5"></i> Produk Jadi
                    </a>
                    <a href="{{ route('pekerja-master.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('pekerja-master.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-users w-5"></i> Manajemen Operator
                    </a>
                </div>

                {{-- 3. PRODUKSI --}}
                <div class="mb-6">
                    <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Manajemen Produksi</p>
                    <a href="{{ route('bom.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('bom.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-scroll w-5"></i> Bill of Materials (BOM)
                    </a>
                    <a href="{{ route('produksi.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('produksi.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-clipboard-list w-5"></i> Perintah Produksi (SPK)
                    </a>
                </div>

                 {{-- 4. LOGISTIK --}}
                 <div class="mb-6">
                     <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Logistik</p>
                       <a href="{{ route('pengadaan.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('pengadaan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                           <i class="fa-solid fa-cart-shopping w-5"></i> Pengadaan Material
                       </a>
                  </div>

                 {{-- 4b. E-COMMERCE --}}
                 <div class="mb-6">
                     <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">E-Commerce</p>
                     <a href="{{ route('admin.penjualan.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('admin.penjualan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                         <i class="fa-solid fa-store w-5"></i> Detail Penjualan
                     </a>
                 </div>

                 {{-- 5. PENGATURAN & LAPORAN --}}
                 <div class="mb-4">
                     <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Laporan & Sistem</p>
                     <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('laporan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                        <i class="fa-solid fa-file-invoice w-5"></i> Laporan
                    </a>
                     <a href="{{ route('pengaturan') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 {{ request()->routeIs('pengaturan*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent' }}">
                         <i class="fa-solid fa-gear w-5"></i> Pengaturan Sistem
                     </a>
                 </div>

            </nav>

            <div class="p-4 border-t border-[#2A3140] bg-[#161B26]">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold text-[#FF5252] hover:bg-[#FF5252]/10 rounded-xl transition">
                        <i class="fa-solid fa-power-off w-5"></i> Keluar Sistem
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"></div>

        {{-- ================= AREA KANAN (TOPBAR) ================= --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#0F111A] relative">
            
            <header class="h-[72px] bg-[#0F111A] border-b border-[#2A3140] flex items-center justify-between px-6 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    {{-- Tombol Menu Mobile --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg bg-[#20232A] text-gray-300 border border-[#2D3139]">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    {{-- Page Title di Navbar --}}
                    <h1 id="navbar-title" class="text-base font-semibold text-white truncate hidden md:block max-w-xs">
                        @php
                            $currentRoute = \Request::route()?->getName();
                            $titleMap = [
                                'admin.dashboard' => 'Dashboard',
                                'admin.bahan.index' => 'Data Bahan Baku',
                                'admin.bahan.create' => 'Tambah Bahan Baku',
                                'admin.bahan.show' => 'Detail Bahan Baku',
                                'admin.bahan.edit' => 'Edit Bahan Baku',
                                'admin.bahan.edit-varian' => 'Edit Varian Bahan',
                                'admin.suppliers.index' => 'Data Supplier',
                                'admin.suppliers.create' => 'Tambah Supplier',
                                'admin.suppliers.edit' => 'Edit Supplier',
                                'admin.suppliers.show' => 'Detail Supplier',
                                'admin.produk-jadi.index' => 'Produk Jadi',
                                'admin.produk-jadi.create' => 'Tambah Produk Jadi',
                                'admin.produk-jadi.edit' => 'Edit Produk Jadi',
                                'admin.produk-jadi.show' => 'Detail Produk Jadi',
                                'admin.pekerja-master.index' => 'Manajemen Operator',
                                'admin.pekerja-master.create' => 'Tambah Operator',
                                'admin.pekerja-master.edit' => 'Edit Operator',
                                'admin.pekerja-master.kinerja' => 'Kinerja Operator',
                                'admin.operator.index' => 'Manajemen Operator',
                                'admin.operator.create' => 'Tambah Operator',
                                'admin.operator.edit' => 'Edit Operator',
                                'admin.bom.index' => 'Bill of Materials (BOM)',
                                'admin.bom.create' => 'Tambah BOM',
                                'admin.bom.edit' => 'Edit BOM',
                                'admin.bom.resep' => 'Resep Produk',
                                'admin.produksi.index' => 'Perintah Produksi (SPK)',
                                'admin.produksi.perintah' => 'Kirim Perintah Produksi',
                                'produksi.create' => 'Kirim Perintah Produksi',
                                'admin.produksi.show' => 'Detail Produksi',
                                'admin.produksi.edit' => 'Edit Produksi',
                                'admin.produksi.review' => 'Review Produksi',
                                'admin.produksi.monitoring' => 'Monitoring Produksi',
                                'admin.pakai-produk.index' => 'Produk Keluar (Jual)',
                                'admin.pakai-produk.create' => 'Tambah Produk Keluar',
                                'admin.pakai-produk.history' => 'Riwayat Produk Keluar',
                                'admin.pengadaan.index' => 'Pengadaan Material',
                                'admin.laporan.index' => 'Laporan',
                                'admin.laporan.analisis' => 'Analisis Laporan',
                                'admin.laporan.hasil-produksi' => 'Hasil Produksi',
                                'admin.laporan.harian' => 'Laporan Harian',
                                'admin.pengaturan.index' => 'Pengaturan Sistem',
                                'admin.pengaturan.notifikasi' => 'Pengaturan Notifikasi',
                                'admin.pengaturan.danger-zone' => 'Danger Zone',
                                'admin.penjualan.index' => 'Detail Penjualan',
                                'admin.penjualan.show' => 'Detail Pesanan',
                                'admin.profile' => 'Profil Admin',
                                'admin.hak-akses.index' => 'Hak Akses',
                                'admin.log-aktivitas.index' => 'Log Aktivitas',
                                'admin.scan.pending' => 'Scan Pending',
                                'admin.scan.detail' => 'Detail Scan',
                                'admin.ringkasan-operasional' => 'Ringkasan Operasional',
                            ];
                            $pageTitle = $titleMap[$currentRoute] ?? \View::getSection('page_title') ?: 'Dashboard';
                        @endphp
                        {{ $pageTitle }}
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    {{-- NOTIFIKASI POPUP (DROP DOWN) --}}
                    <div x-data="{ notifOpen: false }" class="relative">
                        <button @click="notifOpen = !notifOpen" class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-[#20232A] hover:text-white transition relative">
                            <i class="fa-solid fa-bell text-xl"></i>
                            @php
                                $unreadCount = auth()->user()->unreadNotifications->count();
                                $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
                                $totalNotifBadge = $unreadCount + $pendingOrdersCount;
                            @endphp
                            @if($totalNotifBadge > 0)
                                <span class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-[#FF5252] text-[10px] font-black text-white border-2 border-[#0D1117] shadow-lg">
                                    {{ $totalNotifBadge }}
                                </span>
                                <span class="absolute top-0 right-0 flex h-5 w-5 animate-ping rounded-full bg-[#FF5252] opacity-75"></span>
                            @endif
                        </button>
                        
                        <!-- Dropdown Notifikasi -->
                        <div x-show="notifOpen" @click.outside="notifOpen = false" x-cloak class="absolute right-0 mt-3 w-80 sm:w-96 bg-[#1A1C24] border border-[#2D3139] rounded-2xl shadow-2xl overflow-hidden z-50">
                            <div class="p-4 border-b border-[#2D3139] flex justify-between items-center">
                                <h3 class="text-sm font-bold text-white">Notifikasi</h3>
                                <div class="flex gap-2">
                                    <button onclick="event.preventDefault(); document.getElementById('form-mark-all').submit();" class="text-[10px] px-2 py-1 bg-blue-600/20 text-blue-400 rounded-lg hover:bg-blue-600/30 transition">
                                        ✅ Baca Semua
                                    </button>
                                    <button onclick="event.preventDefault(); Swal.fire({...swalDarkTheme, title:'Hapus Semua Notifikasi?', text:'Semua notifikasi akan dihapus permanen.', icon:'warning', showCancelButton:true, confirmButtonText:'Ya, Hapus Semua', cancelButtonText:'Batal', reverseButtons:true}).then((r)=>{if(r.isConfirmed) document.getElementById('form-delete-all').submit();})" class="text-[10px] px-2 py-1 bg-red-600/20 text-red-400 rounded-lg hover:bg-red-600/30 transition">
                                        🗑️ Hapus Semua
                                    </button>
                                </div>
                            </div>
                            <form id="form-mark-all" action="{{ route('notifications.markAllRead') }}" method="POST" class="hidden">@csrf</form>
                            
                            @if($pendingOrdersCount > 0)
                            <div class="bg-yellow-900/20 border-b border-yellow-700/30 p-3">
                                <a href="{{ route('admin.penjualan.index', ['status' => 'pending']) }}" class="flex items-center gap-3 hover:bg-yellow-900/30 rounded-lg p-2 transition">
                                    <div class="w-8 h-8 rounded-full bg-yellow-600/20 flex items-center justify-center">
                                        <i class="fa-solid fa-clock text-yellow-400 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-yellow-300">{{ $pendingOrdersCount }} Pesanan Pending</p>
                                        <p class="text-[10px] text-gray-400">Klik untuk melihat pesanan menunggu</p>
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <div class="max-h-72 overflow-y-auto custom-scrollbar">
                                @php
                                    $semuaNotif = auth()->user()->notifications()->take(15)->get();
                                @endphp
                                @forelse($semuaNotif as $n)
                                <a href="{{ route('notifications.read', $n->id) }}" class="block px-4 py-3 border-b border-[#2D3139]/50 hover:bg-[#20232A] transition {{ is_null($n->read_at) ? 'bg-blue-900/10 border-l-2 border-l-blue-500' : '' }}">
                                    <div class="flex items-start gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-white truncate">{{ $n->data['judul'] ?? 'Notifikasi' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 leading-relaxed line-clamp-2">{{ $n->data['pesan'] ?? '' }}</p>
                                            <p class="text-[9px] text-gray-600 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            <form action="{{ route('notifications.markAllRead') }}" method="POST" onclick="event.stopPropagation();">
                                                @csrf @method('DELETE')
                                                <button class="text-[9px] px-1.5 py-0.5 bg-gray-700 text-gray-400 rounded hover:bg-red-600/30 hover:text-red-400">✕</button>
                                            </form>
                                        </div>
                                    </div>
                                </a>
                                @empty
                                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                                    <i class="fa-solid fa-bell-slash text-2xl mb-2 block"></i>
                                    Tidak ada notifikasi
                                </div>
                                @endforelse
                            </div>
                            <a href="{{ route('notifications.index') }}" class="block text-center text-[11px] py-3 bg-[#20232A] text-gray-400 hover:text-white hover:bg-[#2A3140] transition">
                                Lihat Semua Notifikasi →
                            </a>
                        </div>
                    </div>

                    {{-- Tombol Refresh --}}
                    <button class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-[#20232A] hover:text-white transition" onclick="location.reload()">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>

                    {{-- Profil Link (langsung ke halaman profil) --}}
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 pl-4 border-l border-[#2D3139] hover:bg-[#20232A] rounded-xl px-3 transition">
                        @php $userFresh = auth()->user()->fresh(); @endphp
                        <div class="hidden sm:flex flex-col items-end text-right">
                            <span class="text-sm font-bold text-white leading-none">{{ $userFresh->name }}</span>
                            <span class="text-[10px] text-[#00BFA5] font-semibold mt-1 uppercase tracking-wider">Administrator</span>
                        </div>
                        
                        <div class="w-10 h-10 rounded-full bg-[#20232A] border-2 border-[#2D3139] flex items-center justify-center overflow-hidden">
                            @if($userFresh->foto_profil && \Storage::disk('public')->exists($userFresh->foto_profil))
                                <img src="{{ asset('storage/' . $userFresh->foto_profil) }}?v={{ time() }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-[#00BFA5] font-black uppercase text-sm">
                                    {{ substr($userFresh->name ?? 'ZU', 0, 2) }}
                                </span>
                            @endif
                        </div>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 md:p-8 animate-fade-in relative">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Global Loading Overlay Script -->
    <script>
    (function() {
        var loadingOverlay = document.getElementById('global-loading-overlay');
        var loadingTimeout = null;
        var maxLoadingTime = 30000; // 30 seconds timeout

        // Show loading overlay
        window.showGlobalLoading = function() {
            loadingOverlay.classList.add('active');
            loadingTimeout = setTimeout(function() {
                hideGlobalLoading();
            }, maxLoadingTime);
        };

        // Hide loading overlay
        window.hideGlobalLoading = function() {
            loadingOverlay.classList.remove('active');
            if (loadingTimeout) {
                clearTimeout(loadingTimeout);
                loadingTimeout = null;
            }
        };

        // Listen for all form submissions
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.tagName === 'FORM' && form.method === 'POST') {
                var submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                }
                showGlobalLoading();
            }
        }, true);

        // Re-enable buttons if page loads with errors (validation failed)
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('button:disabled, input[type="submit"]:disabled').forEach(function(btn) {
                btn.disabled = false;
                btn.style.opacity = '';
                btn.style.cursor = '';
            });
            hideGlobalLoading();
        });
    })();
    </script>

    @stack('scripts')

    {{-- SweetAlert2 Dark Theme Config --}}
    <script>
        // Global SweetAlert Dark Theme Config
        const swalDarkTheme = {
            background: '#1C222D',
            color: '#fff',
            confirmButtonColor: '#FF6B00',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonColor: '#2D3139',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl border border-white/10',
                title: 'text-white font-bold text-lg',
                htmlContainer: 'text-gray-400 text-sm',
                confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold shadow-lg',
                cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold border border-white/10',
                icon: 'border-none'
            },
            buttonsStyling: false
        };

        // Toast Dark Config
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#1C222D',
            color: '#fff',
            customClass: {
                popup: 'rounded-xl border border-white/10 shadow-2xl',
                title: 'text-sm font-semibold text-white ml-2',
                timerProgressBar: 'bg-orange-500'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Global Confirm Function
        window.swalConfirm = function(options) {
            return Swal.fire({
                ...swalDarkTheme,
                title: options.title || 'Konfirmasi',
                text: options.text || 'Apakah Anda yakin?',
                icon: options.icon || 'warning',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: options.confirmText || 'Ya, Lanjutkan',
                cancelButtonText: options.cancelText || 'Batal',
                reverseButtons: true,
                focusCancel: true,
                ...options.custom
            }).then((result) => {
                if (result.isConfirmed && options.onConfirm) {
                    options.onConfirm();
                }
                return result.isConfirmed;
            });
        };

        // Global Success Alert
        window.swalSuccess = function(title, text) {
            return Swal.fire({
                ...swalDarkTheme,
                title: title || 'Berhasil!',
                text: text || '',
                icon: 'success',
                confirmButtonText: 'OK',
                showCancelButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        };

        // Global Error Alert
        window.swalError = function(title, text) {
            return Swal.fire({
                ...swalDarkTheme,
                title: title || 'Terjadi Kesalahan!',
                text: text || '',
                icon: 'error',
                confirmButtonText: 'Mengerti',
                showCancelButton: false
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                });
            @endif
        });
    </script>
</body>
</html>