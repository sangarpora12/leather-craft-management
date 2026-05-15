@extends('layouts.admin')

@section('page_title', 'Manajemen Operator')

@section('content')
<div class="min-h-screen bg-[#0B0E14] px-6 py-6" x-data="operatorPage()">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Operator</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola akun karyawan yang bertugas menerima perintah produksi.</p>
        </div>
        <button onclick="openModal('modalTambahOperator')" class="bg-orange-600 hover:bg-orange-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-orange-900/20 flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Tambah Operator
        </button>
    </div>

    {{-- INFORMASI SISTEM ROLE --}}
    <div class="bg-[#1C222D] border border-blue-500/20 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-info-circle text-blue-400 text-sm"></i>
            </div>
            <div class="text-xs text-gray-400 leading-relaxed">
                <span class="text-white font-bold">Sistem Role Operator:</span><br>
                • <span class="text-emerald-400 font-bold">Kepala Gudang</span> → Bisa login dashboard & punya akses semi-penuh ke sistem<br>
                • <span class="text-yellow-400 font-bold">Pekerja</span> → Tidak bisa login, tapi tetap terima notifikasi instruksi via Gmail<br>
                • Notifikasi Gmail hanya BERHENTI jika akun <span class="text-red-400">dinonaktifkan</span>. Selama akun Aktif, Pekerja tetap dapat pemberitahuan via email.
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-[#1C222D] rounded-xl p-4 mb-6 border border-white/5">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                <input type="text"
                       x-model="search"
                       @input="filterOperators()"
                       placeholder="Cari operator..."
                       class="w-full bg-[#0B0E14] border border-gray-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder:text-gray-600 focus:outline-none focus:border-orange-500 transition">
            </div>

            {{-- Filter Status --}}
            <select x-model="filterStatus" @change="filterOperators()"
                    class="bg-[#0B0E14] border border-gray-700 text-white text-sm px-4 py-2 rounded-lg focus:outline-none focus:border-orange-500 cursor-pointer min-w-[140px]">
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>

            {{-- Filter Role --}}
            <select x-model="filterRole" @change="filterOperators()"
                    class="bg-[#0B0E14] border border-gray-700 text-white text-sm px-4 py-2 rounded-lg focus:outline-none focus:border-orange-500 cursor-pointer min-w-[160px]">
                <option value="all">Semua Role</option>
                <option value="kepala_gudang">Kepala Gudang</option>
                <option value="pekerja">Pekerja</option>
            </select>
        </div>
    </div>

    {{-- GRID CARD OPERATOR --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($operators as $op)
            @php
                $isActive = $op->is_active == true || $op->is_active === 1 || is_null($op->is_active);
                $isKepalaGudang = $op->sub_role === 'kepala_gudang';
                $isVerified = !is_null($op->email_verified_at);
                $roleBadgeClass = $isKepalaGudang ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30';
                $roleIcon = $isKepalaGudang ? 'fa-crown' : 'fa-user';
            @endphp

            <div class="bg-[#1C222D] rounded-2xl border {{ $isActive ? 'border-white/5' : 'border-red-500/30' }} overflow-hidden relative transition-all duration-300 hover:translate-y-[-2px] shadow-lg"
                 data-name="{{ strtolower($op->name) }}"
                 data-status="{{ $isActive ? 'active' : 'inactive' }}"
                 data-role="{{ $op->sub_role ?? 'pekerja' }}">

                {{-- OVERLAY GELAP UNTUK NON-AKTIF --}}
                @if(!$isActive)
                <div class="absolute inset-0 bg-black/40 z-10 pointer-events-none rounded-2xl"></div>
                @endif

                <div class="p-5 relative">
                    {{-- HEADER CARD --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            {{-- Foto Profil --}}
                            <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 ring-2 {{ $isActive ? 'ring-gray-700' : 'ring-red-500/50' }}">
                                @if($op->foto_profil || $op->foto)
                                    <img src="{{ asset('storage/' . ($op->foto_profil ?? $op->foto)) }}" alt="{{ $op->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-base">
                                        {{ substr($op->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Nama & Badges --}}
                            <div>
                                <h3 class="text-base font-bold text-white">{{ $op->name }}</h3>
                                <p class="text-[11px] text-gray-400">{{ $op->jabatan ?? 'Operator Gudang' }}</p>
                                <div class="flex gap-1.5 mt-1.5 flex-wrap">
                                    {{-- Badge Role --}}
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $roleBadgeClass }}">
                                        <i class="fa-solid {{ $roleIcon }}"></i>
                                        {{ $isKepalaGudang ? 'KEPALA GUDANG' : 'PEKERJA' }}
                                    </span>
                                    {{-- Badge Status --}}
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                        <i class="fa-solid {{ $isActive ? 'fa-circle text-[6px]' : 'fa-circle-xmark' }}"></i>
                                        {{ $isActive ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                    {{-- Badge Email Verified --}}
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isVerified ? 'bg-purple-500/10 text-purple-400' : 'bg-gray-500/10 text-gray-500' }}">
                                        <i class="fa-solid {{ $isVerified ? 'fa-envelope-circle-check' : 'fa-envelope' }}"></i>
                                        {{ $isVerified ? 'EMAIL VERIFIED' : 'UNVERIFIED' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- KEBAB MENU --}}
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button @click="open = !open" class="text-gray-500 hover:text-white transition p-1.5 rounded-lg hover:bg-gray-700/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-[#1C222D] border border-gray-700 rounded-xl shadow-xl shadow-black/50 z-20 overflow-hidden"
                                 style="display: none;">

                                <a href="{{ route('admin.operator.edit', $op->id) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition border-b border-gray-800/50">
                                    <i class="fa-solid fa-pen text-blue-400 text-xs"></i> Edit User
                                </a>

                                @if(!$isVerified)
                                    {{-- AKUN BELUM VERIFIKASI: menu terbatas --}}

                                    {{-- Kirim Ulang Verifikasi --}}
                                    <form action="{{ route('admin.operator.resend-verification', $op->id) }}" method="POST" class="block w-full" id="form-kirim-verif-{{ $op->id }}">
                                        @csrf
                                        <button type="button" onclick="swalConfirm({title: 'Kirim Ulang Verifikasi?', text: 'Email verifikasi akan dikirim ulang ke {{ $op->email }}. Pastikan email aktif dan benar.', icon: 'question', confirmText: 'Ya, Kirim', onConfirm: () => document.getElementById('form-kirim-verif-{{ $op->id }}').submit()})" class="flex items-center gap-3 px-4 py-3 text-sm text-purple-400 hover:bg-purple-900/20 hover:text-purple-300 transition w-full text-left border-b border-gray-800/50">
                                            <i class="fa-solid fa-envelope text-xs"></i> Kirim Ulang Verifikasi Email
                                        </button>
                                    </form>

                                @else
                                    {{-- AKUN SUDAH TERVERIFIKASI: menu lengkap --}}

                                    {{-- Promote/Demote --}}
                                    @if(!$isKepalaGudang)
                                    <form action="{{ route('admin.operator.toggle-role', $op->id) }}" method="POST" class="block w-full" id="form-promote-{{ $op->id }}">
                                        @csrf @method('PATCH')
                                        <button type="button" onclick="swalConfirm({title: 'Angkat jadi Kepala Gudang?', text: '{{ $op->name }} akan mendapatkan akses login & hak semi-penuh. Akun akan otomatis diaktifkan.', icon: 'question', confirmText: 'Ya, Angkat', onConfirm: () => document.getElementById('form-promote-{{ $op->id }}').submit()})" class="flex items-center gap-3 px-4 py-3 text-sm text-emerald-400 hover:bg-emerald-900/20 hover:text-emerald-300 transition w-full text-left border-b border-gray-800/50">
                                            <i class="fa-solid fa-arrow-up text-xs"></i> Angkat jadi Kepala Gudang
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.operator.toggle-role', $op->id) }}" method="POST" class="block w-full" id="form-demote-{{ $op->id }}">
                                        @csrf @method('PATCH')
                                        <button type="button" onclick="swalConfirm({title: 'Turunkan jadi Pekerja?', text: '{{ $op->name }} akan kehilangan akses login. Tapi MASIH tetap bisa terima notifikasi Gmail selama akun aktif.', icon: 'warning', confirmText: 'Ya, Turunkan', onConfirm: () => document.getElementById('form-demote-{{ $op->id }}').submit()})" class="flex items-center gap-3 px-4 py-3 text-sm text-yellow-400 hover:bg-yellow-900/20 hover:text-yellow-300 transition w-full text-left border-b border-gray-800/50">
                                            <i class="fa-solid fa-arrow-down text-xs"></i> Turunkan jadi Pekerja
                                        </button>
                                    </form>
                                    @endif

                                    @if($isActive)
                                    <form action="{{ route('admin.operator.toggle-status', $op->id) }}" method="POST" class="block w-full" id="form-nonaktif-{{ $op->id }}">
                                        @csrf @method('PATCH')
                                        <button type="button" onclick="swalConfirm({title: 'Nonaktifkan Akun?', text: 'Akun {{ $op->name }} akan dinonaktifkan. Tidak akan terima notifikasi Gmail lagi.', icon: 'warning', confirmText: 'Ya, Nonaktifkan', onConfirm: () => document.getElementById('form-nonaktif-{{ $op->id }}').submit()})" class="flex items-center gap-3 px-4 py-3 text-sm text-orange-400 hover:bg-orange-900/20 hover:text-orange-300 transition w-full text-left border-b border-gray-800/50">
                                            <i class="fa-solid fa-ban text-xs"></i> Nonaktifkan Akun
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.operator.toggle-status', $op->id) }}" method="POST" class="block w-full" id="form-aktif-{{ $op->id }}">
                                        @csrf @method('PATCH')
                                        <button type="button" onclick="swalConfirm({title: 'Aktifkan Akun?', text: 'Akun {{ $op->name }} akan diaktifkan kembali. Notifikasi Gmail akan kembali dikirim jika email sudah terverifikasi.', icon: 'question', confirmText: 'Ya, Aktifkan', onConfirm: () => document.getElementById('form-aktif-{{ $op->id }}').submit()})" class="flex items-center gap-3 px-4 py-3 text-sm text-green-400 hover:bg-green-900/20 hover:text-green-300 transition w-full text-left border-b border-gray-800/50">
                                            <i class="fa-solid fa-check-circle text-xs"></i> Aktifkan Akun
                                        </button>
                                    </form>
                                    @endif
                                @endif

                                <form action="{{ route('admin.pekerja-master.destroy', $op->id) }}" method="POST" class="block w-full" id="form-hapus-{{ $op->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="swalConfirm({title: 'Hapus Operator?', text: 'Data {{ $op->name }} tidak bisa dikembalikan!', icon: 'error', confirmText: 'Ya, Hapus', onConfirm: () => document.getElementById('form-hapus-{{ $op->id }}').submit()})" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition w-full text-left">
                                        <i class="fa-solid fa-trash text-xs"></i> Hapus User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- STATISTIC SECTION --}}
                    <div class="space-y-3 mb-5">
                        {{-- Input Bahan Terbanyak --}}
                        <div class="bg-[#0B0E14] rounded-xl p-3 border border-white/5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gray-700/30 flex items-center justify-center">
                                        <i class="fa-solid fa-box text-blue-400 text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-500 uppercase tracking-wider">Input Bahan Terbanyak</p>
                                        @if($op->bahan_terbanyak)
                                        <p class="text-xs text-white font-bold truncate max-w-[140px]">{{ $op->bahan_terbanyak->nama }}</p>
                                        @else
                                        <p class="text-xs text-gray-600">-</p>
                                        @endif
                                    </div>
                                </div>
                                @if($op->bahan_terbanyak)
                                <span class="text-xs font-bold text-purple-400">{{ $op->bahan_terbanyak->total }} <span class="text-[10px] text-gray-500">{{ $op->bahan_terbanyak->satuan }}</span></span>
                                @endif
                            </div>
                        </div>

                        {{-- Produk Selesai Terbanyak --}}
                        <div class="bg-[#0B0E14] rounded-xl p-3 border border-white/5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gray-700/30 flex items-center justify-center">
                                        <i class="fa-solid fa-check text-green-400 text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-500 uppercase tracking-wider">Produk Selesai Terbanyak</p>
                                        @if($op->produk_terbanyak)
                                        <p class="text-xs text-white font-bold truncate max-w-[140px]">{{ $op->produk_terbanyak->nama }}</p>
                                        @else
                                        <p class="text-xs text-gray-600">-</p>
                                        @endif
                                    </div>
                                </div>
                                @if($op->produk_terbanyak)
                                <span class="text-xs font-bold text-emerald-400">{{ $op->produk_terbanyak->total }} <span class="text-[10px] text-gray-500">{{ $op->produk_terbanyak->satuan }}</span></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- INFO NOTIFIKASI --}}
                    <div class="bg-[#0B0E14] rounded-xl p-3 border border-white/5 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bell text-[10px] {{ $isActive && $isVerified ? 'text-green-400' : 'text-gray-600' }}"></i>
                                <span class="text-[10px] text-gray-500">Notifikasi Gmail:</span>
                            </div>
                            <span class="text-[10px] font-bold {{ $isActive && $isVerified ? 'text-green-400' : 'text-red-400' }}">
                                @if(!$isActive)
                                    ✕ Berhenti (Akun Nonaktif)
                                @elseif(!$isVerified)
                                    ⏳ Email Belum Diverifikasi
                                @else
                                    ✓ Siap Dikirim
                                @endif
                            </span>
                        </div>
                        <div class="mt-1.5 text-[9px] text-gray-600">
                            @if(!$isKepalaGudang)
                                @if($isActive && $isVerified)
                                    ✅ Pekerja ini akan tetap terima instruksi via Gmail
                                @else
                                    ⛔ Pekerja ini tidak akan terima Gmail karena {{ !$isActive ? 'akun nonaktif' : 'email belum diverifikasi' }}
                                @endif
                            @else
                                🔑 Kepala Gudang — dapat login & notifikasi in-app + email
                            @endif
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex gap-3">
                        @if($isActive)
                            <a href="{{ route('admin.operator.edit', $op->id) }}"
                               class="flex-1 h-9 bg-[#1A1D29] hover:bg-[#252836] border border-white/5 rounded-lg flex items-center justify-center text-gray-300 text-xs font-semibold transition-colors"
                               title="Edit akun operator">
                                <i class="fa-solid fa-pen mr-1.5 text-[10px]"></i> Edit Akun
                            </a>
                            <a href="{{ route('admin.pekerja-master.kinerja', $op->id) }}"
                               class="flex-1 h-9 bg-[#1A1D29] hover:bg-[#252836] border border-white/5 rounded-lg text-white text-xs font-semibold flex items-center justify-center transition-colors"
                               title="Lihat detail kinerja operator">
                                <i class="fa-solid fa-chart-simple mr-1.5 text-[10px]"></i> Lihat Kinerja
                            </a>
                        @elseif($isVerified)
                            {{-- AKUN SUDAH VERIF TAPI NONAKTIF: tampilkan tombol Aktifkan --}}
                            <form action="{{ route('admin.operator.toggle-status', $op->id) }}" method="POST" class="w-full" id="form-aktifkan-{{ $op->id }}">
                                @csrf @method('PATCH')
                                <button type="button" onclick="swalConfirm({title: 'Aktifkan Kembali?', text: 'Akun {{ $op->name }} akan diaktifkan kembali. Notifikasi Gmail akan kembali dikirim.', icon: 'question', confirmText: 'Ya, Aktifkan', onConfirm: () => document.getElementById('form-aktifkan-{{ $op->id }}').submit()})"
                                        class="w-full h-9 bg-[#1A1D29] hover:bg-[#252836] border border-white/5 rounded-lg text-white text-xs font-semibold flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-power-off mr-1.5 text-[10px]"></i> Aktifkan Kembali
                                </button>
                            </form>
                        @else
                            {{-- AKUN BELUM VERIFIKASI: hanya info, tidak ada tombol aktifkan --}}
                            <div class="w-full h-9 bg-[#1A1D29] border border-white/5 rounded-lg flex items-center justify-center text-gray-500 text-xs font-semibold cursor-default">
                                <i class="fa-solid fa-hourglass-half mr-1.5 text-[10px]"></i> Menunggu Verifikasi Email
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-20">
                <div class="w-16 h-16 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-users-slash text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500">Belum ada operator terdaftar.</p>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($operators->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $operators->links() }}
    </div>
    @endif

</div>

{{-- ALERT SUCCESS/ERROR --}}
@if ($errors->any())
<div class="fixed top-4 right-4 z-50 bg-red-500/20 border border-red-500 text-red-500 p-4 rounded-xl text-xs max-w-sm">
    <strong>Ups! Ada yang salah:</strong>
    <ul class="mt-2 ml-4 list-disc">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- MODAL TAMBAH OPERATOR --}}
<div id="modalTambahOperator" class="fixed inset-0 z-50 hidden items-center justify-center" aria-hidden="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeModal('modalTambahOperator')"></div>
    <div class="relative bg-[#181b21] w-full max-w-md rounded-2xl border border-gray-700 shadow-2xl p-6 m-4 animate-fade-in">

        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-lg font-bold text-white">Tambah Operator Baru</h2>
            <button onclick="closeModal('modalTambahOperator')" class="text-gray-500 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="{{ route('admin.pekerja-master.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none placeholder-gray-600">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Email Login</label>
                <input type="email" name="email" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                    placeholder="email@contoh.com"
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none placeholder-gray-600">
                <p class="text-[9px] text-gray-600 mt-1">*Digunakan untuk notifikasi Gmail. Pastikan email aktif & bisa diverifikasi.</p>
            </div>

            {{-- Jabatan --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Posisi / Jabatan</label>
                <select name="jabatan" class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none">
                    <option value="Staff Produksi">Staff Produksi</option>
                    <option value="Operator Jahit">Operator Jahit</option>
                    <option value="Operator Potong">Operator Potong</option>
                    <option value="Kepala Gudang">Kepala Gudang</option>
                </select>
            </div>

            {{-- Sub Role --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Tipe Akun</label>
                <select name="sub_role" class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none">
                    <option value="pekerja">Pekerja (Tidak Bisa Login, tapi terima Gmail)</option>
                    <option value="kepala_gudang">Kepala Gudang (Bisa Login & Akses Sistem)</option>
                </select>
                <p class="text-[9px] text-gray-600 mt-1">*Kepala Gudang bisa login dashboard. Pekerja hanya terima instruksi via Gmail (tidak bisa login).</p>
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Password</label>
                <input type="text" name="password" value="123456" required
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none placeholder-gray-600">
                <p class="text-[9px] text-gray-600 mt-1">*Minimal 6 karakter. Bisa diganti nanti oleh operator.</p>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-700 mt-2">
                <button type="button" onclick="closeModal('modalTambahOperator')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-lg shadow-lg">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    function operatorPage() {
        return {
            search: '',
            filterStatus: 'all',
            filterRole: 'all',
            filterOperators() {
                const cards = document.querySelectorAll('[data-name]');
                cards.forEach(card => {
                    const name = card.dataset.name;
                    const status = card.dataset.status;
                    const role = card.dataset.role;

                    const matchSearch = name.includes(this.search.toLowerCase());
                    const matchStatus = this.filterStatus === 'all' || status === this.filterStatus;
                    const matchRole = this.filterRole === 'all' || role === this.filterRole;

                    card.style.display = (matchSearch && matchStatus && matchRole) ? '' : 'none';
                });
            }
        };
    }

    // Gunakan swalConfirm global dari layout (parameter object: {title, text, icon, confirmText, onConfirm})
</script>
@endsection