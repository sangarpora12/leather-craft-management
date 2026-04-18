<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Berliano Operator'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --op-bg: #0f0f0f;
            --op-surface: rgba(255, 255, 255, 0.05);
            --op-stroke: rgba(255, 255, 255, 0.1);
            --op-stroke-2: rgba(255, 255, 255, 0.08);
            --op-text-dim: rgba(255, 255, 255, 0.6);
        }

        body {
            background: var(--op-bg);
            color: #ffffff;
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
            -webkit-tap-highlight-color: transparent;
        }

        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        .safe-top { padding-top: env(safe-area-inset-top); }

        .operator-main { padding-bottom: 7.5rem; overflow-y: auto; }

        .op-glass {
            background: var(--op-surface);
            border: 1px solid var(--op-stroke);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .op-card {
            background: rgba(30, 30, 30, 0.8);
            border: 1px solid var(--op-stroke-2);
            border-radius: 28px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .op-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            color: #ffffff;
        }
        .op-input::placeholder { color: rgba(255, 255, 255, 0.5); }
        .op-input:focus {
            outline: none;
            border-color: rgba(59, 130,246, 0.8);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .op-divider { border-color: rgba(255, 255, 255, 0.1); }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

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
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="text-white">

    <!-- Global Loading Overlay -->
    <div id="global-loading-overlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Sedang memproses data...</div>
    </div>

    <div x-data="{ sidebarOpen: false }"
         x-init="window.addEventListener('operator:toggleSidebar', () => { sidebarOpen = !sidebarOpen })">
        <!-- Sidebar Drawer -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-50" style="display:none;">
            <div class="absolute inset-0 bg-black/70" @click="sidebarOpen=false"></div>
            <aside class="absolute left-0 top-0 h-full w-72 bg-[#0f1115] border-r border-white/10 shadow-2xl p-4"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="-translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="-translate-x-full">
                <div class="flex items-center justify-between px-2 pb-4 border-b border-white/10">
                    <div class="text-sm font-semibold text-white">Menu Operator</div>
                    <button class="w-8 h-8 rounded-full hover:bg-white/10" @click="sidebarOpen=false">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                <?php echo $__env->make('operator.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>
        </div>

    <main class="operator-main max-w-md mx-auto min-h-screen <?php echo $__env->yieldContent('main_class', ''); ?>">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php if (! empty(trim($__env->yieldContent('bottom_nav')))): ?>
        <?php echo $__env->yieldContent('bottom_nav'); ?>
    <?php else: ?>
        <?php echo $__env->make('components.bottom-nav-operator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

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
            // Auto-hide after max time to prevent getting stuck
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
                // Don't show loading for search/filter forms
                if (form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]')) {
                    var submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]');
                    // Disable the submit button to prevent double-click
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.6';
                        submitBtn.style.cursor = 'not-allowed';
                    }
                    showGlobalLoading();
                }
            }
        }, true);

        // Re-enable buttons if page loads with errors (validation failed)
        document.addEventListener('DOMContentLoaded', function() {
            // Re-enable all disabled buttons
            document.querySelectorAll('button:disabled, input[type="submit"]:disabled').forEach(function(btn) {
                btn.disabled = false;
                btn.style.opacity = '';
                btn.style.cursor = '';
            });
            hideGlobalLoading();
        });
    })();
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script src="<?php echo e(asset('js/operator/tugas.js')); ?>"></script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

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
</html><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/layouts/operator_app.blade.php ENDPATH**/ ?>