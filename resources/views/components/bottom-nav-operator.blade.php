<div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-[#0f172a]/95 backdrop-blur-lg border-t border-slate-800 px-6 py-3 flex justify-between items-end z-50 safe-bottom">
    
    {{-- DASHBOARD --}}
    @php
        $isDashboard = Request::is('operator/dashboard') || Route::is('operator.dashboard');
    @endphp
    <a href="{{ route('operator.dashboard') }}" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas {{ $isDashboard ? 'fa-solid fa-house' : 'fa-regular fa-house' }} text-lg transition-all duration-300 {{ $isDashboard ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
        <span class="text-[10px] font-normal transition-all duration-300 {{ $isDashboard ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300' }}">Dashboard</span>
        @if($isDashboard)
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        @endif
    </a>

    {{-- TUGAS --}}
    @php
        $isTugas = Request::is('operator/tugas*') || Request::is('operator/produksi*') || Route::is('operator.tugas.index') || Route::is('operator.tugas.detail') || Route::is('operator.produksi.show');
    @endphp
    <a href="{{ route('operator.tugas.index') }}" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas {{ $isTugas ? 'fa-solid fa-clipboard-list' : 'fa-regular fa-clipboard-list' }} text-lg transition-all duration-300 {{ $isTugas ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
        <span class="text-[10px] font-normal transition-all duration-300 {{ $isTugas ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300' }}">Tugas</span>
        @if(isset($taskCount) && $taskCount > 0)
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">
            {{ $taskCount > 9 ? '9+' : $taskCount }}
        </span>
        @endif
        @if($isTugas)
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        @endif
    </a>

    {{-- SCAN (Center - No active state change) --}}
    @php
        $isScan = Request::is('operator/scan*') || Route::is('operator.scan.index') || Route::is('operator.scan.*');
    @endphp
    <div class="relative -top-6">
        <a href="{{ route('operator.scan.index') }}" class="w-14 h-14 bg-gradient-to-b from-emerald-500 to-cyan-600 border-4 border-[#0f172a] rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30 hover:scale-105 hover:shadow-emerald-500/50 transition-all duration-300">
            <i class="fas fa-qrcode text-white text-xl"></i>
        </a>
        @if($isScan)
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-2 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        @endif
    </div>

    {{-- STOK (Bahan Baku) --}}
    @php
        $isStok = Request::is('operator/bahan*') || Route::is('operator.bahan.index') || Route::is('operator.bahan.create') || Route::is('operator.bahan.edit') || Route::is('operator.bahan.store') || Route::is('operator.bahan.update');
        $isInputManual = Request::is('operator/input-manual-bahan') || Route::is('operator.input-manual-bahan');
    @endphp
    <a href="{{ route('operator.bahan.index') }}" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas {{ $isStok ? 'fa-solid fa-box' : 'fa-regular fa-box' }} text-lg transition-all duration-300 {{ $isStok ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
        <span class="text-[10px] font-normal transition-all duration-300 {{ $isStok ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300' }}">Stok</span>
        @if($isStok)
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        @endif
    </a>
    

    {{-- PENGADAAN --}}
    @php
        $isPengadaan = Request::is('operator/pengadaan*') || Request::is('operator/belanja*') || Route::is('operator.pengadaan') || Route::is('operator.belanja.index') || Route::is('operator.pengadaan.request') || Route::is('operator.belanja.selesai') || Route::is('operator.pengadaan.receive_form') || Route::is('operator.pengadaan.receive');
    @endphp
    <a href="{{ route('operator.pengadaan') }}" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas {{ $isPengadaan ? 'fa-solid fa-cart-flatbed' : 'fa-regular fa-cart-flatbed' }} text-lg transition-all duration-300 {{ $isPengadaan ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
        <span class="text-[10px] font-normal transition-all duration-300 {{ $isPengadaan ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300' }}">Pengadaan</span>
        @if($isPengadaan)
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        @endif
    </a>

</div>
