@extends('layouts.admin')

@section('title', 'Hak Akses - Berliano Leather')

@section('content')
<div class="p-6 ml-64 min-h-screen bg-gray-900">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Kelola Hak Akses</h1>
        <p class="text-gray-400 mt-1">Kelola role dan akses pengguna dalam sistem</p>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
        <p class="text-green-400 text-sm">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
        <p class="text-red-400 text-sm">{{ session('error') }}</p>
    </div>
    @endif

    <!-- User List -->
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-lg font-semibold text-white">Daftar Pengguna</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-700/30 transition">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs mr-3">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="text-white font-medium">{{ $user->name }}</span>
                                @if($user->id === $currentUser->id)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-500/20 text-blue-400 rounded">Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($user->role === 'admin')
                            <span class="px-2 py-1 text-xs font-medium bg-purple-500/20 text-purple-400 rounded">
                                Administrator
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium bg-blue-500/20 text-blue-400 rounded">
                                Operator
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $user->jabatan ?? '-' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($user->id !== $currentUser->id)
                            <form action="{{ route('admin.hak-akses.update-role', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <select name="role" onchange="this.form.submit()" class="bg-gray-700 text-gray-300 text-sm rounded px-2 py-1 border border-gray-600 focus:border-red-500 focus:outline-none">
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="operator" {{ $user->role === 'operator' ? 'selected' : '' }}>Operator</option>
                                </select>
                            </form>
                            @else
                            <span class="text-gray-500 text-sm italic">Tidak dapat diubah</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-6 bg-gray-800 rounded-xl border border-gray-700 p-4">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-white font-medium">Informasi Hak Akses</h3>
                <p class="text-gray-400 text-sm mt-1">
                    <strong>Administrator</strong> memiliki akses penuh ke semua fitur termasuk mengelola pengguna, melihat semua laporan, dan pengaturan sistem.
                    <br>
                    <strong>Operator</strong> memiliki akses terbatas untuk mengoperasikan fitur produksi dan input data.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
