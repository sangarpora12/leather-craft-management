<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Operasional - Berliano Leather</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }

        /* Smooth transition untuk dropdown sidebar */
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        details[open] summary ~ * { animation: sweep .3s ease-in-out; }
        
        @keyframes sweep {
            0%    {opacity: 0; transform: translateY(-10px)}
            100%  {opacity: 1; transform: translateY(0)}
        }

        /* Donut Chart CSS */
        .donut {
            width: 140px; height: 140px; border-radius: 50%;
            background: conic-gradient(
                #22c55e 0% 60%, 
                #3b82f6 60% 85%, 
                #f97316 85% 100%
            );
            mask: radial-gradient(farthest-side, transparent 75%, black 76%);
            -webkit-mask: radial-gradient(farthest-side, transparent 75%, black 76%);
        }
    </style>
</head>
<body class="bg-black text-white h-screen overflow-hidden flex">

    <aside class="w-64 bg-[#111111] border-r border-white/5 flex flex-col hidden md:flex h-full">
        <div class="h-20 flex-shrink-0 flex items-center px-6 border-b border-white/5 gap-3">
            <div class="w-8 h-8 bg-red-600 rounded flex items-center justify-center text-white font-bold text-xs">BL</div>
            <div>
                <h1 class="text-sm font-bold">Berliano Leather</h1>
                <p class="text-[10px] text-gray-400 font-inter">Admin Dashboard</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            
            <p class="px-3 text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-2 mt-1">Dashboard</p>
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            
            <a href="{{ route('admin.bahan-baku.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Data Bahan Baku
            </a>

            <a href="{{ route('admin.produk-jadi.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Daftar Produk Jadi
            </a>

            <a href="{{ route('admin.ringkasan-operasional') }}" class="flex items-center gap-3 px-3 py-2.5 bg-white/10 rounded-lg text-white text-xs font-medium border border-white/5">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Ringkasan Operasional
            </a>

            <a href="{{ route('admin.bom.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Bill of Material (BOM)
            </a>

            <a href="{{ route('admin.operator.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Manajemen Operator
            </a>

            <a href="{{ route('admin.monitoring.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Monitoring Proses
            </a>

            <a href="{{ route('admin.pengadaan.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Pengadaan Material
            </a>
            
            <a href="{{ route('admin.spk.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Perintah Produksi (SPK)
            </a>

            <a href="{{ route('admin.pakai-produk.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                Pakai Produk
            </a>

            <details class="group">
                <summary class="flex items-center justify-between gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium cursor-pointer transition select-none">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Laporan dan Analis
                    </div>
                    <svg class="w-3 h-3 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <div class="pl-10 pr-2 py-1 space-y-1 bg-[#1A1A1A]/50 rounded-b-lg mb-2">
                    <a href="{{ route('admin.laporan.hasil-produksi') }}" class="block py-2 text-[11px] text-gray-500 hover:text-white transition">Laporan Hasil Produksi</a>
                    <a href="{{ route('admin.laporan.lengkap') }}" class="block py-2 text-[11px] text-gray-500 hover:text-white transition">Laporan Lengkap</a>
                    <a href="{{ route('admin.laporan.analisis') }}" class="block py-2 text-[11px] text-gray-500 hover:text-white transition">Laporan & Analisis</a>
                    <a href="{{ route('admin.laporan.penerimaan-material') }}" class="block py-2 text-[11px] text-gray-500 hover:text-white transition">Penerimaan Material</a>
                    <a href="{{ route('admin.laporan.keuangan') }}" class="block py-2 text-[11px] text-gray-500 hover:text-white transition">Laporan Keuangan</a>
                </div>
            </details>

            <p class="px-3 text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-2 mt-4">Lainya</p>

            <a href="{{ route('admin.pengaturan.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-gray-400 hover:bg-white/5 hover:text-white rounded-lg text-xs font-medium transition group">
                <svg class="w-4 h-4 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>

        </nav>

        <div class="p-4 border-t border-white/5 mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-red-400 hover:bg-red-500/10 rounded-lg text-xs font-medium transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative overflow-hidden bg-black">
        
        <header class="h-20 bg-[#111111] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0">
            <div>
                <h2 class="text-xl font-bold text-white">Dashboard Ringkasan Operasional</h2>
                <p class="text-xs text-gray-500 mt-1">Analisis lengkap: Grafik Garis, Batang, dan Donat.</p>
            </div>
            <button class="bg-[#1A1A1A] border border-white/10 hover:bg-[#252525] text-white text-xs font-medium px-4 py-2 rounded-lg transition">
                Refresh Data
            </button>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex items-center gap-1 bg-green-500/10 px-1.5 py-0.5 rounded text-[10px] text-green-500 font-bold">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            5%
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Total SPK Aktif</p>
                    <h3 class="text-2xl font-bold text-white">25 <span class="text-gray-500 text-lg">SPK</span></h3>
                    <div class="absolute bottom-0 left-6 right-6 h-1 bg-gray-800 rounded-full mb-6">
                        <div class="h-full bg-blue-600 rounded-full" style="width: 60%"></div>
                    </div>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div class="flex items-center gap-1 bg-red-500/10 px-1.5 py-0.5 rounded text-[10px] text-red-500 font-bold">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            1.2%
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Nilai Inventaris</p>
                    <h3 class="text-2xl font-bold text-white">Rp 125.4 <span class="text-gray-500 text-lg">Jt</span></h3>
                    <div class="absolute bottom-0 left-6 right-6 h-1 bg-gray-800 rounded-full mb-6">
                        <div class="h-full bg-purple-600 rounded-full" style="width: 80%"></div>
                    </div>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex items-center gap-1 bg-green-500/10 px-1.5 py-0.5 rounded text-[10px] text-green-500 font-bold">
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            15%
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Output (Bulan Ini)</p>
                    <h3 class="text-2xl font-bold text-white">500 <span class="text-gray-500 text-lg">Pcs</span></h3>
                    <div class="absolute bottom-0 left-6 right-6 h-1 bg-gray-800 rounded-full mb-6">
                        <div class="h-full bg-green-500 rounded-full" style="width: 75%"></div>
                    </div>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 bg-red-500/10 rounded-lg text-red-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="bg-red-500/10 border border-red-500/20 px-2 py-0.5 rounded text-[9px] text-red-400 font-bold">
                            Perlu Tindakan
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Stok Kritis</p>
                    <h3 class="text-2xl font-bold text-white">7 <span class="text-gray-500 text-lg">Item</span></h3>
                    <div class="absolute bottom-0 left-6 right-6 h-1 bg-gray-800 rounded-full mb-6">
                        <div class="h-full bg-red-600 rounded-full" style="width: 40%"></div>
                    </div>
                </div>
            </div>

            <!-- (Sisa konten grafik dan tabel lainnya sama seperti sebelumnya...) -->
            
        </div>
    </main>
</body>
</html>
