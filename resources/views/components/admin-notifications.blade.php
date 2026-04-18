{{-- NOTIFICATION DROPDOWN COMPONENT --}}
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="hover:text-white relative">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        {{-- Notification Badge --}}
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">{{ $notificationCount ?? 3 }}</span>
    </button>

    {{-- Notification Dropdown --}}
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="absolute right-0 mt-2 w-80 bg-[#1A1A1A] border border-white/10 rounded-xl shadow-2xl z-50" style="display: none;">
        <div class="p-4 border-b border-white/10">
            <h3 class="text-sm font-bold text-white">Notifikasi</h3>
            <p class="text-xs text-gray-400">{{ $notificationCount ?? 3 }} notifikasi belum dibaca</p>
        </div>

        <div class="max-h-64 overflow-y-auto">
            {{-- Notification Items --}}
            @if(isset($notifications) && count($notifications) > 0)
                @foreach($notifications as $notification)
                <div class="p-4 border-b border-white/5 hover:bg-white/5 transition">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 {{ $notification['icon_bg'] ?? 'bg-blue-500/20' }} rounded-full flex items-center justify-center">
                            {!! $notification['icon'] !!}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">{{ $notification['title'] }}</p>
                            <p class="text-xs text-gray-400">{{ $notification['message'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification['time'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                {{-- Default notifications --}}
                <div class="p-4 border-b border-white/5 hover:bg-white/5 transition">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-red-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">Stok Kritis!</p>
                            <p class="text-xs text-gray-400">Benang Nilon Waxed stok hampir habis</p>
                            <p class="text-xs text-gray-500 mt-1">2 menit yang lalu</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-b border-white/5 hover:bg-white/5 transition">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">Produksi Selesai</p>
                            <p class="text-xs text-gray-400">Rome Bag (Tan) telah selesai diproduksi</p>
                            <p class="text-xs text-gray-500 mt-1">15 menit yang lalu</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 hover:bg-white/5 transition">
                    <div class="flex gap-3">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-white font-medium">Pengadaan Baru</p>
                            <p class="text-xs text-gray-400">Permintaan pengadaan bahan baku masuk</p>
                            <p class="text-xs text-gray-500 mt-1">1 jam yang lalu</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="p-3 border-t border-white/10">
            <a href="{{ route('admin.notifications.index') }}" class="text-xs text-teal-400 hover:text-teal-300 font-medium">Lihat Semua Notifikasi</a>
        </div>
    </div>
</div>
