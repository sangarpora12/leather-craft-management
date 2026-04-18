<div class="flex-1 overflow-y-auto p-4 space-y-6">
    
    <div class="space-y-4">
        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-3 mb-2">
            MENU UTAMA
        </div>
        <nav class="space-y-1">
            <a href="{{ route('operator.dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-gray-300 hover:bg-[#1A1A1A] rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('operator.dashboard') ? 'bg-orange-600/20 text-orange-400 font-bold' : '' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('operator.tugas.index') }}" class="flex items-center gap-3 px-3 py-2 text-gray-300 hover:bg-[#1A1A1A] rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('operator.tugas.*') ? 'bg-orange-600/20 text-orange-400 font-bold' : '' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Tugas Produksi
            </a>

            <a href="{{ route('operator.bahan.index') }}" class="flex items-center gap-3 px-3 py-2 text-gray-300 hover:bg-[#1A1A1A] rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('operator.bahan.*') ? 'bg-orange-600/20 text-orange-400 font-bold' : '' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m4 4v10m8-4l-8 4m0-4V7" /></svg>
                Stok Bahan Baku
            </a>

            <a href="{{ route('operator.scan.index') }}" class="flex items-center gap-3 px-3 py-2 text-gray-300 hover:bg-[#1A1A1A] rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('operator.scan.*') ? 'bg-orange-600/20 text-orange-400 font-bold' : '' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4c1 0 2 1 2 2v3m0 0h-4m0 0H9m4 0h-4m-4 4h4m6-4h.01M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                Scan Nota
            </a>

            <a href="{{ route('operator.belanja.index') }}" class="flex items-center gap-3 px-3 py-2 text-gray-300 hover:bg-[#1A1A1A] rounded-lg text-sm font-medium transition 
                {{ request()->routeIs('operator.belanja.*') ? 'bg-orange-600/20 text-orange-400 font-bold' : '' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                Daftar Belanja
            </a>
        </nav>
    </div>
</div>