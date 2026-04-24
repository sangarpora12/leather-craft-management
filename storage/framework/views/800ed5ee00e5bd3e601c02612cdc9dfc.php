<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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

        
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#161B26] border-r border-[#2A3140] flex flex-col transition-transform duration-300 md:translate-x-0 md:static md:z-auto shadow-xl">
            
            <div class="h-[72px] flex flex-col justify-center px-6 border-b border-[#2A3140] bg-[#161B26]">
                <?php
                    $namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
                    $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai;
                ?>
                <div class="flex items-center gap-3 w-full">
                    <?php if($logoPerusahaan): ?>
                        <img src="<?php echo e(asset('storage/' . $logoPerusahaan)); ?>" alt="Logo" class="h-8 w-8 shrink-0 object-cover rounded-md border border-[#2D3139]">
                    <?php else: ?>
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-gradient-to-br from-[#FD730D] to-orange-700 flex items-center justify-center shadow-lg">
                            <i class="fa-solid fa-gem text-xs text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-col min-w-0">
                        <h1 class="text-sm font-extrabold text-white truncate leading-tight"><?php echo e($namaPerusahaan); ?></h1>
                        <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Admin Panel</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 px-4 space-y-1 overflow-y-auto py-6 custom-scrollbar">
                
                <div class="mb-6">
                    <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Utama</p>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-chart-line w-5"></i> Dashboard
                    </a>
                </div>

                
                <div class="mb-6">
                    <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Data Master</p>
                    <a href="<?php echo e(route('admin.bahan.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.bahan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-box-open w-5"></i> Bahan Baku
                    </a>
                    <a href="<?php echo e(route('admin.suppliers.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.suppliers.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-truck-field w-5"></i> Supplier
                    </a>
                    <a href="<?php echo e(route('admin.produk-jadi.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.produk-jadi.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-shirt w-5"></i> Produk Jadi
                    </a>
                    <a href="<?php echo e(route('admin.pekerja-master.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.pekerja-master.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-users w-5"></i> Manajemen Operator
                    </a>
                </div>

                
                <div class="mb-6">
                    <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Manajemen Produksi</p>
                    <a href="<?php echo e(route('admin.bom.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.bom.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-scroll w-5"></i> Bill of Materials (BOM)
                    </a>
                    <a href="<?php echo e(route('admin.produksi.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.produksi.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-clipboard-list w-5"></i> Perintah Produksi (SPK)
                    </a>
                    <a href="<?php echo e(route('admin.pakai-produk.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.pakai-produk.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-dolly w-5"></i> Produk Keluar (Jual)
                    </a>
                </div>

                 
                 <div class="mb-6">
                     <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Logistik</p>
                      <a href="<?php echo e(route('admin.pengadaan.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.pengadaan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                          <i class="fa-solid fa-cart-shopping w-5"></i> Pengadaan Material
                      </a>
                 </div>

                 
                 <div class="mb-4">
                     <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Laporan & Sistem</p>
                     <a href="<?php echo e(route('admin.laporan.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.laporan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                        <i class="fa-solid fa-file-invoice w-5"></i> Laporan
                    </a>
                     <a href="<?php echo e(route('admin.pengaturan.index')); ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all duration-300 <?php echo e(request()->routeIs('admin.pengaturan.*') ? 'bg-white/10 text-white border-l-4 border-[#FF6B00]' : 'text-[#94A3B8] hover:bg-[#1F2533] hover:text-white border-l-4 border-transparent'); ?>">
                         <i class="fa-solid fa-gear w-5"></i> Pengaturan Sistem
                     </a>
                 </div>

            </nav>

            <div class="p-4 border-t border-[#2A3140] bg-[#161B26]">
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm font-bold text-[#FF5252] hover:bg-[#FF5252]/10 rounded-xl transition">
                        <i class="fa-solid fa-power-off w-5"></i> Keluar Sistem
                    </button>
                </form>
            </div>
        </aside>

        
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"></div>

        
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#0F111A] relative">
            
            <header class="h-[72px] bg-[#0F111A] border-b border-[#2A3140] flex items-center justify-between px-6 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg bg-[#20232A] text-gray-300 border border-[#2D3139]">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    
                    <h1 id="navbar-title" class="text-base font-semibold text-white truncate hidden md:block max-w-xs">
                        <?php if(\Request::routeIs('admin.laporan.pengaturan.*') || \Request::routeIs('admin.pengaturan.*')): ?>
                            Pengaturan Sistem
                        <?php else: ?>
                            <?php echo $__env->yieldContent('page_title', 'Dashboard'); ?>
                        <?php endif; ?>
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    
                    <div class="relative">
                        <a href="<?php echo e(route('admin.notifications.index')); ?>" class="w-10 h-10 flex items-center justify-center rounded-full text-gray-400 hover:bg-[#20232A] hover:text-white transition relative">
                            <i class="fa-solid fa-bell text-xl"></i>
                            
                            <?php $unreadCount = auth()->user()->unreadNotifications->count(); ?>
                            <?php if($unreadCount > 0): ?>
                                <span class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-[#FF5252] text-[10px] font-black text-white border-2 border-[#0D1117] shadow-lg">
                                    <?php echo e($unreadCount); ?>

                                </span>
                                <span class="absolute top-0 right-0 flex h-5 w-5 animate-ping rounded-full bg-[#FF5252] opacity-75"></span>
                            <?php endif; ?>
                        </a>
                    </div>

                    
                    <button class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-[#20232A] hover:text-white transition" onclick="location.reload()">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>

                    
                    <a href="<?php echo e(route('admin.profile')); ?>" class="flex items-center gap-3 pl-4 border-l border-[#2D3139] hover:bg-[#20232A] rounded-xl px-3 transition">
                        <?php $userFresh = auth()->user()->fresh(); ?>
                        <div class="hidden sm:flex flex-col items-end text-right">
                            <span class="text-sm font-bold text-white leading-none"><?php echo e($userFresh->name); ?></span>
                            <span class="text-[10px] text-[#00BFA5] font-semibold mt-1 uppercase tracking-wider">Administrator</span>
                        </div>
                        
                        <div class="w-10 h-10 rounded-full bg-[#20232A] border-2 border-[#2D3139] flex items-center justify-center overflow-hidden">
                            <?php if($userFresh->foto_profil): ?>
                                <img src="<?php echo e(asset('storage/' . $userFresh->foto_profil)); ?>?v=<?php echo e(time()); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-[#00BFA5] font-black uppercase text-sm">
                                    <?php echo e(substr($userFresh->name ?? 'ZU', 0, 2)); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 md:p-8 animate-fade-in relative">
                <?php echo $__env->yieldContent('content'); ?>
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

    <script>
    // Auto-set title based on active menu
    document.addEventListener('DOMContentLoaded', function() {
        const titleEl = document.getElementById('navbar-title');
        if (!titleEl) return;
        
        // Check if page has set a custom title via @section
        const customTitle = titleEl.textContent.trim();
        if (customTitle && customTitle !== 'Dashboard') return;
        
        // Auto-detect from active menu
        const activeLink = document.querySelector('aside a.bg-\\[\\#2D3139\\]');
        if (activeLink) {
            const text = activeLink.textContent.trim();
            if (text) titleEl.textContent = text;
        }
    });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    
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
            <?php if(session('success')): ?>
                Toast.fire({
                    icon: 'success',
                    title: '<?php echo e(session('success')); ?>'
                });
            <?php endif; ?>

            <?php if(session('error')): ?>
                Toast.fire({
                    icon: 'error',
                    title: '<?php echo e(session('error')); ?>'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/layouts/admin.blade.php ENDPATH**/ ?>