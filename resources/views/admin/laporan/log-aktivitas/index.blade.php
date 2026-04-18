@extends('layouts.admin')

@section('page_title', 'Log Aktivitas')

@section('content')
<div class="space-y-6 animate-fade-in font-sans">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Log Aktivitas (Audit Trail)</h1>
            <p class="text-sm text-gray-400 mt-1">
                Memantau semua perubahan data secara otomatis
            </p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.log-aktivitas.cleanup') }}" method="POST" onsubmit="return confirm('Hapus log yang sudah lebih dari 30 hari?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-broom"></i> Hapus Log Lama (30+ hari)
                </button>
            </form>
            <form action="{{ route('admin.log-aktivitas.destroy-all') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA log? Tindakan ini tidak dapat dibatalkan!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i> Hapus Semua Log
                </button>
            </form>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-[#1A1C24] border border-[#2D3139] rounded-xl p-4">
        <form method="GET" action="{{ route('admin.log-aktivitas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari modul/aktivitas..." 
                    class="w-full bg-[#0D1117] border border-[#2D3139] rounded-lg px-4 py-2 text-sm text-white focus:border-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Modul</label>
                <select name="modul" class="w-full bg-[#0D1117] border border-[#2D3139] rounded-lg px-4 py-2 text-sm text-white focus:border-orange-500 outline-none">
                    <option value="">Semua Modul</option>
                    @foreach($modulList as $modul)
                        <option value="{{ $modul }}" {{ request('modul') == $modul ? 'selected' : '' }}>{{ $modul }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">User</label>
                <select name="user_id" class="w-full bg-[#0D1117] border border-[#2D3139] rounded-lg px-4 py-2 text-sm text-white focus:border-orange-500 outline-none">
                    <option value="">Semua User</option>
                    @foreach($userList as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                    class="w-full bg-[#0D1117] border border-[#2D3139] rounded-lg px-4 py-2 text-sm text-white focus:border-orange-500 outline-none">
            </div>
            <div class="md:col-span-4 flex justify-end gap-3">
                <a href="{{ route('admin.log-aktivitas.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm font-semibold transition">
                    Reset
                </a>
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white rounded-lg text-sm font-semibold transition">
                    <i class="fa-solid fa-search"></i> Cari
                </button>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-[#1A1C24] border border-[#2D3139] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#2D3139] text-gray-300">
                    <tr>
                        <th class="p-4 font-semibold">Waktu</th>
                        <th class="p-4 font-semibold">Modul</th>
                        <th class="p-4 font-semibold">Aktivitas</th>
                        <th class="p-4 font-semibold">User</th>
                        <th class="p-4 font-semibold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 text-gray-400">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                                <div class="text-xs text-gray-600">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">
                                    {{ $log->modul }}
                                </span>
                            </td>
                            <td class="p-4 text-white">
                                {{ $log->aktivitas }}
                            </td>
                            <td class="p-4 text-gray-400">
                                {{ $log->user->name ?? 'Sistem' }}
                            </td>
                            <td class="p-4 text-gray-500 font-mono text-xs">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <i class="fa-solid fa-clipboard-list text-4xl mb-3 opacity-30"></i>
                                <p>Belum ada log aktivitas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="flex justify-center">
        {{ $logs->links() }}
    </div>

</div>
@endsection