

<?php $__env->startSection('content'); ?>
<div class="animate-fade-in">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Profil Saya</h1>
        <p class="text-zinc-400 text-sm mt-1">Kelola informasi akun dan keamanan Anda</p>
    </div>

    
    <?php if(session('success')): ?>
    <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg mb-6">
        <i class="fa-solid fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        
        <div class="lg:col-span-1">
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 sticky top-24">
                
                <div class="text-center mb-6">
                    <?php if($user->foto_profil): ?>
                    <div class="w-32 h-32 mx-auto rounded-full border-4 border-zinc-700 shadow-xl overflow-hidden mb-4">
                        <img src="<?php echo e(asset('storage/' . $user->foto_profil)); ?>" alt="Foto Profil" class="w-full h-full object-cover">
                    </div>
                    <?php else: ?>
                    <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-red-600 to-red-900 border-4 border-zinc-700 flex items-center justify-center mb-4 shadow-xl">
                        <i class="fa-solid fa-user text-4xl text-white"></i>
                    </div>
                    <?php endif; ?>
                    <h2 class="text-xl font-bold text-white"><?php echo e($user->name); ?></h2>
                    <span class="inline-block mt-2 px-3 py-1 bg-red-900/50 text-red-400 text-xs font-medium rounded-full border border-red-800">
                        <i class="fa-solid fa-shield-halved mr-1"></i>Administrator
                    </span>
                </div>

                
                <div class="space-y-4 pt-4 border-t border-zinc-800">
                    <div class="flex items-center gap-3 text-zinc-400">
                        <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center">
                            <i class="fa-solid fa-envelope text-zinc-500"></i>
                        </div>
                        <span class="text-sm truncate"><?php echo e($user->email); ?></span>
                    </div>
                    <?php if($user->no_hp): ?>
                    <div class="flex items-center gap-3 text-zinc-400">
                        <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center">
                            <i class="fa-solid fa-phone text-zinc-500"></i>
                        </div>
                        <span class="text-sm"><?php echo e($user->no_hp); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center gap-3 text-zinc-400">
                        <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-zinc-500"></i>
                        </div>
                        <span class="text-sm">Terakhir login: <?php echo e($user->updated_at->diffForHumans()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-2 space-y-6">
            
            
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <i class="fa-solid fa-id-card mr-2 text-blue-400"></i>
                    Informasi Personal
                </h3>
                
                <form action="<?php echo e(route('admin.profile.update')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">Nama Lengkap</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                        </div>
                        
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">Email</label>
                            <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                        </div>
                        
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" value="<?php echo e(old('no_hp', $user->no_hp ?? '')); ?>" placeholder="+62 812 3456 7890"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                        </div>
                        
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">Foto Profil</label>
                            <input type="file" name="foto_profil" accept="image/*"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:cursor-pointer">
                            <p class="text-xs text-zinc-500">Format: JPG, PNG. Max 10MB</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2.5 px-6 rounded-lg transition-colors flex items-center shadow-lg shadow-blue-900/20">
                            <i class="fa-solid fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            
<div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl mt-6">
    <h3 class="text-white font-bold mb-4 flex items-center gap-2">
        <i class="fa-solid fa-building text-[#00BFA5]"></i> Profil Perusahaan
    </h3>
    
    <form action="<?php echo e(route('admin.profile.company')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" value="<?php echo e($config['nama_perusahaan'] ?? ''); ?>" 
                       class="w-full bg-[#0D1117] border border-[#2D3139] rounded-xl px-4 py-3 text-sm text-white mt-2 outline-none focus:border-[#00BFA5]">
            </div>

            
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase">Logo Perusahaan</label>
                <input type="file" name="logo_perusahaan" 
                       class="w-full bg-[#0D1117] border border-[#2D3139] rounded-xl px-4 py-2 text-sm text-white mt-2 outline-none">
                <p class="text-[10px] text-gray-500 mt-1 italic">Logo akan muncul di sidebar dan laporan PDF.</p>
            </div>
        </div>

        <button type="submit" class="mt-6 bg-[#00BFA5] hover:bg-[#00A892] text-[#0D1117] font-bold text-xs px-6 py-3 rounded-xl transition">
            SIMPAN PROFIL PERUSAHAAN
        </button>
    </form>
</div>

            
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center">
                    <i class="fa-solid fa-lock mr-2 text-purple-400"></i>
                    Ganti Password
                </h3>
                
                <form action="<?php echo e(route('admin.profile.password')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="space-y-4 max-w-md">
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">Password Lama</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                    <i class="fa-solid fa-key"></i>
                                </span>
                                <input type="password" name="current_password" required placeholder="Masukkan password lama"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-purple-500">
                            </div>
                        </div>
                        
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">Password Baru</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="new_password" required minlength="8" placeholder="Min. 8 karakter"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-purple-500">
                            </div>
                        </div>
                        
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-zinc-400">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="new_password_confirmation" required minlength="8" placeholder="Ulangi password baru"
                                    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-purple-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-start">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-medium py-2.5 px-6 rounded-lg transition-colors flex items-center shadow-lg shadow-purple-900/20">
                            <i class="fa-solid fa-key mr-2"></i>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/profile/index.blade.php ENDPATH**/ ?>