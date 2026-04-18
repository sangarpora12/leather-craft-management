@extends('layouts.admin')

@section('content')
<div class="bg-zinc-900 min-h-screen text-white">
    <!-- Header -->
    <div class="border-b border-zinc-800 px-6 py-4">
        <h1 class="text-2xl font-bold">Pengaturan Sistem</h1>
        <p class="text-zinc-400 text-sm">Kelola pengaturan aplikasi dan profil Anda</p>
    </div>

    <div class="flex flex-col md:flex-row">
        <!-- Sidebar / Vertical Tabs - 11 Tabs -->
        <div class="w-full md:w-64 border-r border-zinc-800 p-4 shrink-0">
            <nav class="space-y-1">
                <button onclick="switchTab('profil')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-blue-900/30 data-[active=true]:text-blue-400 text-zinc-400" data-tab="profil">
                    <i class="fa-solid fa-user-cog mr-2 w-5"></i> Profil & Akun
                </button>
                <button onclick="switchTab('satuan')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="satuan">
                    <i class="fa-solid fa-ruler-combined mr-2 w-5"></i> Satuan & Kategori
                </button>
                <button onclick="switchTab('gudang')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="gudang">
                    <i class="fa-solid fa-warehouse mr-2 w-5"></i> Konfigurasi Gudang
                </button>
                <button onclick="switchTab('laporan')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="laporan">
                    <i class="fa-solid fa-file-pdf mr-2 w-5"></i> Laporan & PDF
                </button>
                <button onclick="switchTab('keuangan')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="keuangan">
                    <i class="fa-solid fa-coins mr-2 w-5"></i> Keuangan
                </button>
                <button onclick="switchTab('notifikasi')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="notifikasi">
                    <i class="fa-solid fa-bell mr-2 w-5"></i> Notifikasi
                </button>
                <button onclick="switchTab('shift')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="shift">
                    <i class="fa-solid fa-clock mr-2 w-5"></i> Shift & Operasional
                </button>
                <button onclick="switchTab('hakakses')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="hakakses">
                    <i class="fa-solid fa-shield-alt mr-2 w-5"></i> Hak Akses
                </button>
                <button onclick="switchTab('log')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="log">
                    <i class="fa-solid fa-history mr-2 w-5"></i> Log Aktivitas
                </button>
                <button onclick="switchTab('backup')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-zinc-800 data-[active=true]:text-white text-zinc-400" data-tab="backup">
                    <i class="fa-solid fa-database mr-2 w-5"></i> Backup & Export
                </button>
                <button onclick="switchTab('danger')" class="tab-btn w-full text-left px-4 py-3 rounded-lg transition-colors hover:bg-zinc-800 data-[active=true]:bg-red-900/30 data-[active=true]:text-red-400 text-zinc-400" data-tab="danger">
                    <i class="fa-solid fa-exclamation-triangle mr-2 w-5"></i> Danger Zone
                </button>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="flex-1 p-6 overflow-y-auto">
            
            <!-- Tab 1: Profil & Akun (SANGAT LENGKAP) -->
            <div id="tab-profil" class="tab-content">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-id-card mr-2 text-blue-400"></i>
                    Profil & Akun
                </h2>
                
                @if(session('success'))
                <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg mb-6">
                    <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
                @endif
                
                @if(session('error'))
                <div class="bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg mb-6">
                    <i class="fa-solid fa-x-circle mr-2"></i>{{ session('error') }}
                </div>
                @endif
                
                @if($errors->any())
                <div class="bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Main Form with enctype for file uploads -->
                <form action="{{ route('admin.pengaturan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Card A: Profil Perusahaan (Berliano Leather) -->
                    <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center text-blue-400">
                            <i class="fa-solid fa-building mr-2"></i>
                            Profil Perusahaan (Berliano Leather)
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Logo Upload -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Logo Perusahaan</label>
                                <div class="flex items-start gap-4">
                                    <div class="shrink-0">
                                        @if(isset($pengaturanCollection['logo_perusahaan']) && $pengaturanCollection['logo_perusahaan'])
                                        <img src="{{ asset('storage/' . $pengaturanCollection['logo_perusahaan']) }}" alt="Logo Perusahaan" class="w-24 h-24 object-contain rounded-lg bg-zinc-800 border border-zinc-700 p-2">
                                        @else
                                        <div class="w-24 h-24 flex items-center justify-center rounded-lg bg-zinc-800 border border-zinc-700">
                                            <i class="fa-solid fa-image text-zinc-600 text-3xl"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="logo_perusahaan" accept="image/*" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white file:cursor-pointer">
                                        <p class="text-xs text-zinc-500 mt-1">Format: JPG, PNG, GIF. Max 2MB</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Slogan / Tagline -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Slogan / Tagline</label>
                                <input type="text" name="slogan_perusahaan" value="{{ old('slogan_perusahaan', $pengaturanCollection['slogan_perusahaan'] ?? '') }}" placeholder="Contoh: Kualitas Kulit Terbaik Indonesia" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                            </div>
                            
                            <!-- Nama Perusahaan -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Nama Perusahaan <span class="text-red-400">*</span></label>
                                <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $pengaturanCollection['nama_perusahaan'] ?? 'Berliano Leather') }}" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                            </div>
                            
                            <!-- Email Perusahaan -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Email Perusahaan</label>
                                <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $pengaturanCollection['email_perusahaan'] ?? '') }}" placeholder="info@berliano.com" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                            </div>
                            
                            <!-- No. Telepon / WA Kantor -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">No. Telepon / WA Kantor</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="text" name="telepon_perusahaan" value="{{ old('telepon_perusahaan', $pengaturanCollection['telepon_perusahaan'] ?? '') }}" placeholder="+62 812 3456 7890" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
                            
                            <!-- Alamat Lengkap Pabrik -->
                            <div class="space-y-2 lg:col-span-2">
                                <label class="block text-sm font-medium text-zinc-400">Alamat Lengkap Pabrik</label>
                                <textarea name="alamat_perusahaan" rows="3" placeholder="Jl. Raya Pengasih, Desa X, Kecamatan Y, Kabupaten Z, Provinsi..." class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-blue-500 resize-none">{{ old('alamat_perusahaan', $pengaturanCollection['alamat_perusahaan'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card B: Profil Pribadi (Admin) -->
                    <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center text-green-400">
                            <i class="fa-solid fa-user mr-2"></i>
                            Profil Pribadi (Admin)
                        </h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Foto Profil Upload -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Foto Profil</label>
                                <div class="flex items-start gap-4">
                                    <div class="shrink-0">
                                        @if($user->foto_profil)
                                        <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto Profil" class="w-24 h-24 rounded-full object-cover border-2 border-zinc-700">
                                        @else
                                        <div class="w-24 h-24 flex items-center justify-center rounded-full bg-zinc-800 border-2 border-zinc-700">
                                            <i class="fa-solid fa-user text-zinc-600 text-3xl"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="foto_profil" accept="image/*" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-600 file:text-white file:cursor-pointer">
                                        <p class="text-xs text-zinc-500 mt-1">Format: JPG, PNG. Max 1MB</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Nama Lengkap -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Nama Lengkap <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-green-500">
                            </div>
                            
                            <!-- Email Pribadi -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Email Pribadi <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-green-500">
                                </div>
                            </div>
                            
                            <!-- No. HP / WA Pribadi -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">No. HP / WA Pribadi</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </span>
                                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp ?? '') }}" placeholder="+62 812 3456 7890" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-green-500">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card C: Keamanan & Ganti Password -->
                    <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center text-yellow-400">
                            <i class="fa-solid fa-lock mr-2"></i>
                            Keamanan & Ganti Password
                        </h3>
                        
                        <!-- Warning Text -->
                        <div class="bg-yellow-900/20 border border-yellow-800/50 rounded-lg p-3 mb-6">
                            <p class="text-sm text-yellow-400/80">
                                <i class="fa-solid fa-circle-info mr-2"></i>
                                Kosongkan kolom password jika tidak ingin mengubah password
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Password Lama -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Password Lama</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password" name="current_password" placeholder="Masukkan password lama" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-10 py-2.5 text-white focus:outline-none focus:border-yellow-500">
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Password Baru -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Password Baru</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="new_password" placeholder="Min. 8 karakter" minlength="8" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-10 py-2.5 text-white focus:outline-none focus:border-yellow-500">
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Konfirmasi Password Baru -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-zinc-400">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg pl-10 pr-10 py-2.5 text-white focus:outline-none focus:border-yellow-500">
                                    <button type="button" onclick="togglePassword(this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-300">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-8 rounded-lg transition-colors flex items-center shadow-lg shadow-blue-900/20">
                            <i class="fa-solid fa-save mr-2"></i>
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Satuan & Kategori -->
            <div id="tab-satuan" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-ruler-combined mr-2 text-blue-400"></i>
                    Satuan & Kategori
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Satuan & Kategori - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur manajemen satuan dan kategori produk sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 3: Konfigurasi Gudang -->
            <div id="tab-gudang" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-warehouse mr-2 text-blue-400"></i>
                    Konfigurasi Gudang
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Konfigurasi Gudang - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur konfigurasi gudang dan stok sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 4: Laporan & PDF -->
            <div id="tab-laporan" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-file-pdf mr-2 text-blue-400"></i>
                    Laporan & PDF
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Laporan & PDF - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur format laporan dan PDF sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 5: Keuangan -->
            <div id="tab-keuangan" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-coins mr-2 text-blue-400"></i>
                    Keuangan
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Keuangan - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur pengaturan keuangan sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 6: Notifikasi -->
            <div id="tab-notifikasi" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-bell mr-2 text-blue-400"></i>
                    Notifikasi
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Notifikasi - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur pengaturan notifikasi sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 7: Shift & Operasional -->
            <div id="tab-shift" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-clock mr-2 text-blue-400"></i>
                    Shift & Operasional
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Shift & Operasional - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur pengaturan shift dan operasional sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 8: Hak Akses -->
            <div id="tab-hakakses" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-shield-alt mr-2 text-blue-400"></i>
                    Hak Akses
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Hak Akses - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur manajemen hak akses sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 9: Log Aktivitas -->
            <div id="tab-log" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-history mr-2 text-blue-400"></i>
                    Log Aktivitas
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Log Aktivitas - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur log aktivitas pengguna sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 10: Backup & Export -->
            <div id="tab-backup" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fa-solid fa-database mr-2 text-blue-400"></i>
                    Backup & Export
                </h2>
                <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Backup & Export - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur backup dan export data sedang dalam pengembangan</p>
                </div>
            </div>

            <!-- Tab 11: Danger Zone -->
            <div id="tab-danger" class="tab-content hidden">
                <h2 class="text-xl font-semibold mb-6 flex items-center text-red-400">
                    <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                    Danger Zone
                </h2>
                <div class="bg-neutral-900 border border-red-800 rounded-xl p-12 text-center">
                    <i class="fa-solid fa-clock text-zinc-600 text-5xl mb-4"></i>
                    <p class="text-zinc-400 text-lg">Tab Danger Zone - Segera Hadir</p>
                    <p class="text-zinc-500 text-sm mt-2">Fitur berbahaya (hapus data, reset sistem) sedang dalam pengembangan</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Tab Switching Function
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Show selected tab content
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Update tab button states
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.setAttribute('data-active', 'false');
            btn.classList.remove('bg-blue-900/30', 'text-blue-400', 'bg-zinc-800', 'text-white', 'bg-red-900/30', 'text-red-400');
            btn.classList.add('text-zinc-400');
        });
        
        // Set active tab button with appropriate styling
        const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
        activeBtn.setAttribute('data-active', 'true');
        
        // Apply specific styling based on tab
        if (tabName === 'profil') {
            activeBtn.classList.add('bg-blue-900/30', 'text-blue-400');
        } else if (tabName === 'danger') {
            activeBtn.classList.add('bg-red-900/30', 'text-red-400');
        } else {
            activeBtn.classList.add('bg-zinc-800', 'text-white');
        }
        activeBtn.classList.remove('text-zinc-400');
    }
    
    // Toggle Password Visibility
    function togglePassword(button) {
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Initialize first tab as active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('profil');
    });
</script>
@endsection
