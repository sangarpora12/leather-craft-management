<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Berliano Leather</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0D1117;
        }
        .login-card {
            background-color: #1A1C24;
            border: 1px solid #2D3139;
        }
        .input-field {
            background-color: #20232A;
            border: 1px solid #2D3139;
            color: white;
        }
        .input-field:focus {
            border-color: #FD730D;
            outline: none;
        }
        .btn-primary {
            background-color: #FD730D;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #E6670B;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <?php
        $namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first();
        $logoPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first();
        $displayName = $namaPerusahaan ? $namaPerusahaan->nilai : 'Berliano Leather';
        $displayLogo = $logoPerusahaan ? $logoPerusahaan->nilai : null;
    ?>

    <div class="login-card w-full max-w-[420px] rounded-2xl p-10 shadow-2xl">
        <div class="text-center mb-10">
            <div class="flex justify-center mb-4">
                <?php if($displayLogo): ?>
                    <img src="<?php echo e(asset('storage/' . $displayLogo)); ?>" alt="Logo" class="h-16 object-contain">
                <?php else: ?>
                    <i class="fas fa-gem text-4xl text-[#FD730D]"></i>
                <?php endif; ?>
            </div>
            <h1 class="text-white text-2xl font-bold tracking-[0.15em] uppercase"><?php echo e($displayName); ?></h1>
            <p class="text-gray-400 text-sm mt-2">Sistem Informasi Manajemen Produksi</p>
        </div>

        <form action="<?php echo e(route('login.process')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="bg-red-900/30 border border-red-500 text-red-200 p-3 rounded-lg text-xs">
                    <?php echo e($message); ?>

                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div class="space-y-2">
                <label class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest">Username Admin</label>
                <div class="relative">
                    <input type="text" name="username" placeholder="Masukkan username admin" value="<?php echo e(old('username')); ?>" 
                        class="input-field w-full rounded-xl px-4 py-4 text-sm" required>
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest">Password</label>
                    <a href="<?php echo e(route('password.request')); ?>" class="text-[#D75A00] text-[10px] font-bold hover:underline">Lupa Password?</a>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="••••••••" 
                        class="input-field w-full rounded-xl px-4 py-4 text-sm" required>
                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <p class="text-[10px] text-gray-500 leading-relaxed text-center mt-4">
                    Klik link di atas jika lupa sandi. Cek Gmail Anda, buat password baru, lalu login ulang.
                </p>
            </div>

            <button type="submit" class="btn-primary w-full py-4 rounded-xl text-white font-bold text-sm tracking-wide shadow-lg shadow-orange-900/20">
                MASUK DASHBOARD
            </button>
        </form>

        <div class="mt-12 text-center">
            <p class="text-gray-600 text-[10px] tracking-wide">
                © 2026 <?php echo e($displayName); ?> Factory. <span class="text-gray-700">v1.0</span>
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/auth/login.blade.php ENDPATH**/ ?>