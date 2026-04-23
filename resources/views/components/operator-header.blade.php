{{-- Operator Header with Elegant Notification Dropdown --}}
<div class="h-16 px-4 flex items-center justify-between bg-[#0B0E14] border-b border-gray-700 sticky top-0 z-50"
     style="padding-top: env(safe-area-inset-top);">
    <button type="button" class="w-8 h-8 flex items-center justify-center hover:bg-gray-700/60 rounded-full transition"
            title="Menu" onclick="window.dispatchEvent(new CustomEvent('operator:toggleSidebar'))">
        <i class="fas fa-bars text-gray-300"></i>
    </button>
    <div class="flex-1 px-3 min-w-0">
        <div class="text-base font-semibold text-white truncate text-center">
            {{ $title ?? 'BERLIANO' }}
        </div>
    </div>
    <div class="flex items-center gap-2">
        {{-- Quick link: Laporan (Operator) --}}
        <a href="{{ route('operator.laporan.index') }}"
           class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-gray-200 hover:bg-white/10">
            <i class="fas fa-chart-line text-[12px]"></i>
            Laporan
        </a>
        {{-- Elegant Notification Bell Dropdown --}}
        <div class="relative" x-data="{ open: false }" @click.outside="open = false" x-effect="document.body.classList.toggle('overflow-hidden', open)">
            @php
                $operatorUnread = auth()->user()?->unreadNotifications()?->count() ?? 0;
            @endphp
            <button @click="open = !open" class="w-8 h-8 flex items-center justify-center relative cursor-pointer hover:bg-gray-700 rounded-full transition" title="Notifikasi">
                <i class="fas fa-bell text-gray-300 text-lg"></i>
                @if($operatorUnread > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold rounded-full h-5 min-w-5 px-1 flex items-center justify-center">
                        {{ $operatorUnread }}
                    </span>
                @endif
            </button>
            
            {{-- Elegant Dropdown Menu --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="fixed left-1/2 -translate-x-1/2 top-20 w-[min(92vw,24rem)] bg-white rounded-xl shadow-2xl border border-gray-200 z-[60] overflow-hidden"
                 style="display: none;">

                {{-- Backdrop supaya tidak "geser" dan fokus ke popup --}}
                <div class="fixed inset-0 bg-black/40 -z-10" @click="open=false"></div>
                
                {{-- Dropdown Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-white font-semibold text-base">Notifikasi Terbaru</h3>
                        <p class="text-blue-100 text-xs">Tugas dari Admin</p>
                    </div>
                    <form method="POST" action="{{ route('operator.notifications.mark-all-read') }}">
                        @csrf
                        <button type="submit" class="text-white/80 hover:text-white text-xs bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-check-double mr-1"></i> Tandai dibaca
                        </button>
                    </form>
                </div>
                
                {{-- Notification List --}}
                <div class="max-h-96 overflow-y-auto custom-scrollbar">
                    @php
                        // Notifikasi asli milik operator (database notifications)
                        $recentNotifs = auth()->user()?->notifications()?->latest()->take(8)->get() ?? collect();
                    @endphp
                    
                    @forelse($recentNotifs as $notif)
                        @php
                            $isUnread = is_null($notif->read_at);
                            // Standarisasi payload notif: pakai key 'judul' dan 'pesan'
                            $title = $notif->data['judul'] ?? ($notif->data['title'] ?? 'Notifikasi');
                            $message = $notif->data['pesan'] ?? ($notif->data['message'] ?? ($notif->data['body'] ?? '-'));
                            // Klik notif: tandai dibaca + redirect
                            $url = route('operator.notifications.read', $notif->id);
                        @endphp
                        <a href="{{ $url }}"
                           class="flex items-start gap-4 px-5 py-4 border-b border-gray-100 hover:bg-gray-50 transition duration-150 group {{ $isUnread ? 'bg-blue-50/50' : 'bg-white' }}">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-blue-600 transition">
                                        {{ $title }}
                                    </p>
                                    @if($isUnread)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $message }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-gray-300 group-hover:text-blue-500 transition">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-12 text-center">
                            <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium">Semua sudah dibaca</p>
                            <p class="text-gray-400 text-xs mt-1">Tidak ada tugas terbaru</p>
                        </div>
                    @endforelse
                </div>
                
                {{-- Dropdown Footer --}}
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200">
                    <a href="{{ route('operator.tugas.index') }}" class="flex items-center justify-center text-sm text-blue-600 hover:text-blue-700 font-medium transition">
                        Lihat Semua Tugas 
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        
        {{-- Profile --}}
        <a href="{{ route('operator.profile') }}" class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-800 rounded-full flex items-center justify-center shadow-lg shadow-black/40 border border-blue-500/30 overflow-hidden">
            @if(Auth::user()->foto)
                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-sm font-normal text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
            @endif
        </a>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
