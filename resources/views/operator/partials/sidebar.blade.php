{{-- Sidebar untuk Role OPERATOR (Minimalis) --}}

<nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

    <p class="px-3 text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-2 mt-1">OPERATOR PANEL</p>

    <a href="{{ route('operator.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('operator.dashboard') ? 'bg-white/10 text-white border border-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-lg text-xs font-medium transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
        </svg>
        Dashboard Tugas
    </a>

    <a href="{{ route('operator.tugas.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('operator.tugas.*') ? 'bg-white/10 text-white border border-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-lg text-xs font-medium transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Daftar SPK (Tugas)
    </a>

    <div class="border-t border-white/10 my-2"></div>
    <p class="px-3 text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-2">LAPORAN</p>

    <a href="{{ route('operator.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('operator.laporan.*') && !request()->routeIs('operator.laporan.cetak') ? 'bg-white/10 text-white border border-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-lg text-xs font-medium transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Laporan Saya
    </a>

    <a href="{{ route('operator.laporan.cetak') }}" class="flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('operator.laporan.cetak') ? 'bg-white/10 text-white border border-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} rounded-lg text-xs font-medium transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h6a2 2 0 002-2z" />
        </svg>
        Cetak Laporan
    </a>

</nav>
