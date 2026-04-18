<div class="flex justify-between items-center mb-8 bg-[#111827] sticky top-0 z-30 py-2">
    
    <div>
        <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Admin Dashboard</p>
        <h2 class="text-2xl font-bold">Selamat Datang, Admin</h2>
    </div>

    <div class="flex items-center gap-6">
        
        <div class="relative group">
            <button class="relative p-2 text-gray-400 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-2 right-2 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-[#111827]"></span>
            </button>

            <div class="absolute right-0 mt-2 w-80 bg-[#1F2937] border border-gray-700 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                    <h4 class="font-bold text-sm text-white">Pusat Notifikasi</h4>
                    <span class="text-[10px] text-blue-400 cursor-pointer">Tandai Dibaca</span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <div class="p-3 border-b border-gray-700 hover:bg-gray-800 cursor-pointer flex gap-3">
                        <div class="w-2 h-2 mt-2 bg-red-500 rounded-full flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold text-red-400">Stok Kritis: Lem Aica</p>
                            <p class="text-[10px] text-gray-400 mt-1">Stok produk 'Lem Aica Bond' telah mencapai batas minimum. Segera lakukan restock.</p>
                            <p class="text-[10px] text-gray-600 mt-2">2 jam lalu</p>
                        </div>
                    </div>
                     <div class="p-3 border-b border-gray-700 hover:bg-gray-800 cursor-pointer flex gap-3">
                        <div class="w-2 h-2 mt-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-bold text-blue-400">SPK Baru: SPK-2025-001</p>
                            <p class="text-[10px] text-gray-400 mt-1">Pesanan produksi Rome Bag (50 Pcs) telah disetujui. Silakan cek detail.</p>
                            <p class="text-[10px] text-gray-600 mt-2">5 jam lalu</p>
                        </div>
                    </div>
                </div>
                <div class="p-2 text-center border-t border-gray-700">
                    <a href="#" class="text-xs text-gray-400 hover:text-white">Lihat Semua Notifikasi</a>
                </div>
            </div>
        </div>

        <div class="relative group">
            <button class="flex items-center gap-3 hover:bg-gray-800 p-2 rounded-lg transition">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-white">Super Admin</p>
                    <p class="text-[10px] text-gray-500">Head Office</p>
                </div>
                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold border-2 border-gray-700">AD</div>
            </button>
            
            <div class="absolute right-0 mt-2 w-48 bg-[#1F2937] border border-gray-700 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 overflow-hidden">
                <a href="#" class="block px-4 py-3 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">Profil Saya</a>
                <a href="#" class="block px-4 py-3 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">Pengaturan Akun</a>
                <div class="border-t border-gray-700"></div>
                <a href="#" class="block px-4 py-3 text-sm text-red-400 hover:bg-red-900/20">Logout</a>
            </div>
        </div>

    </div>
</div>