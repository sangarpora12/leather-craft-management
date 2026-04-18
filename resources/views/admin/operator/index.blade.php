@extends('layouts.admin')

@section('content')
<div class="space-y-6 animate-fade-in font-sans text-gray-300">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-6 border-b border-gray-800 pb-6">
        <div>
            <h1 class="text-xl font-bold text-white mb-1">Manajemen Operator Gudang</h1>
            <p class="text-xs text-gray-500">Kelola akun karyawan yang bertugas menerima perintah produksi.</p>
        </div>
        
        <button onclick="openModal('modalTambahOperator')" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 flex items-center gap-2 transition">
            <i class="fa-solid fa-user-plus"></i> Tambah Operator
        </button>
    </div>

    {{-- TABEL OPERATOR --}}
    <div class="bg-[#1f242c] rounded-xl border border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Operator</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Jabatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($operators as $op)
                    <tr class="hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    @if($op->foto)
                                        <img src="{{ asset('storage/' . $op->foto) }}" alt="{{ $op->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr($op->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-white font-bold text-sm">{{ $op->name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $op->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $op->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $op->jabatan ?? 'Staff Produksi' }}</td>
                        <td class="px-6 py-4">
                            @if($op->is_active == true || $op->is_active === 1 || is_null($op->is_active))
                            <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-400 bg-emerald-900/20 px-2 py-1 rounded-full border border-emerald-900/30 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                            </span>
                            @else
                            <span class="flex items-center gap-1.5 text-xs font-bold text-red-400 bg-red-900/20 px-2 py-1 rounded-full border border-red-900/30 w-fit">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 relative">
                            {{-- Ellipsis Button with Pop-over Menu --}}
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.outside="open = false" class="text-gray-500 hover:text-white transition p-1 rounded-md hover:bg-gray-700/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                
                                {{-- Pop-over Dropdown Menu --}}
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-[#1f242c] border border-gray-700 rounded-xl shadow-xl shadow-black/50 z-50 overflow-hidden"
                                     style="display: none;">
                                    
                                    {{-- Edit User --}}
                                    <a href="{{ route('admin.operator.edit', $op->id) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition border-b border-gray-800/50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Edit User
                                    </a>
                                    
                                    {{-- Reset Password --}}
                                    {{-- NOTE: route admin.operator.reset-password belum ada, jadi sementara disembunyikan supaya halaman tidak error. --}}
                                    {{--
                                    <form action="{{ route('admin.operator.reset-password', $op->id) }}" method="POST" class="block w-full">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Reset password operator ini ke default (123456)?')" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition w-full text-left border-b border-gray-800/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                            Atur Ulang Password
                                        </button>
                                    </form>
                                    --}}
                                    
                                    {{-- Toggle Status (Aktifkan/Nonaktifkan) --}}
                                    @if($op->is_active == true || $op->is_active === 1 || is_null($op->is_active))
                                    <form action="{{ route('admin.operator.toggle-status', $op->id) }}" method="POST" class="block w-full">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('Nonaktifkan akun operator ini? Operator tidak akan bisa login.')" class="flex items-center gap-3 px-4 py-3 text-sm text-orange-400 hover:bg-orange-900/20 hover:text-orange-300 transition w-full text-left border-b border-gray-800/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                            Nonaktifkan Akun
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.operator.toggle-status', $op->id) }}" method="POST" class="block w-full">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('Aktifkan kembali akun operator ini?')" class="flex items-center gap-3 px-4 py-3 text-sm text-green-400 hover:bg-green-900/20 hover:text-green-300 transition w-full text-left border-b border-gray-800/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Aktifkan Akun
                                        </button>
                                    </form>
                                    @endif
                                    
                                    {{-- Delete User --}}
                                    <form action="{{ route('admin.operator.destroy', $op->id) }}" method="POST" class="block w-full">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus operator ini? Data tidak bisa dikembalikan.')" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-900/20 hover:text-red-300 transition w-full text-left">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus User
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-users-slash text-2xl"></i>
                            </div>
                            <p>Belum ada operator terdaftar.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($operators->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $operators->links() }}
    </div>
    @endif

</div>

{{-- MODAL TAMBAH OPERATOR --}}
<div id="modalTambahOperator" class="fixed inset-0 z-50 hidden items-center justify-center" aria-hidden="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeModal('modalTambahOperator')"></div>
    <div class="relative bg-[#181b21] w-full max-w-md rounded-2xl border border-gray-700 shadow-2xl p-6 m-4 animate-fade-in">
        
        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-lg font-bold text-white">Tambah Operator Baru</h2>
            <button onclick="closeModal('modalTambahOperator')" class="text-gray-500 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="{{ route('admin.operator.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            {{-- Nama --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-blue-500 focus:outline-none placeholder-gray-600">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Email Login</label>
                <input type="email" name="email" required placeholder="budi@berliano.com"
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-blue-500 focus:outline-none placeholder-gray-600">
            </div>

            {{-- Jabatan (Opsional) --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Posisi / Jabatan</label>
                <select name="jabatan" class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-blue-500 focus:outline-none">
                    <option value="Operator Jahit">Operator Jahit</option>
                    <option value="Operator Potong">Operator Potong</option>
                    <option value="Operator Finishing">Operator Finishing</option>
                    <option value="Kepala Gudang">Kepala Gudang</option>
                </select>
            </div>



            {{-- Password --}}
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Password Default</label>
                <input type="text" name="password" value="123456" readonly
                    class="w-full bg-[#111] border border-gray-800 rounded-lg px-4 py-3 text-xs text-gray-500 cursor-not-allowed">
                <p class="text-[9px] text-gray-600 mt-1">*Password default: 123456 (Bisa diganti operator nanti)</p>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-700 mt-2">
                <button type="button" onclick="closeModal('modalTambahOperator')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg shadow-lg">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.getElementById(id).classList.add('flex'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
</script>
@endsection