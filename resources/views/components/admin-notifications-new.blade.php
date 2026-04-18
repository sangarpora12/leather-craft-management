{{-- Admin Notifications - Elegant Dropdown --}}
<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open" class="text-gray-500 hover:text-white transition relative p-2 rounded-lg hover:bg-white/5" type="button">
        <i class="fa-solid fa-bell text-lg"></i>
    </button>
    
    {{-- Elegant Dropdown Menu --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 mt-3 w-96 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden"
         style="display: none;">
        
        {{-- Dropdown Header --}}
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-5 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-white font-semibold text-base">Notifikasi Terbaru</h3>
                <p class="text-red-100 text-xs">Update Produksi & Stok</p>
            </div>
            <button class="text-white/80 hover:text-white text-xs bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition">
                <i class="fas fa-check-double mr-1"></i> Tandai dibaca
            </button>
        </div>
        
        {{-- Notification List --}}
        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            @php
                // Get latest production updates and critical stock alerts
                $recentProductions = \App\Models\Produksi::with('product')
                    ->orderBy('updated_at', 'desc')
                    ->take(3)
                    ->get();
                
                $criticalStock = \App\Models\BahanBaku::whereRaw('stok <= COALESCE(stok_minimum, 10)')
                    ->orderBy('stok', 'asc')
                    ->take(2)
                    ->get();
                
                // Merge and sort by date
                $notifications = collect();
                
                foreach ($recentProductions as $p) {
                    $notifications->push([
                        'type' => 'production',
                        'title' => $p->product ? $p->product->nama_produk : 'Produk',
                        'subtitle' => $p->kode_spk ?? '-',
                        'status' => $p->status,
                        'date' => $p->updated_at,
                        'route' => route('admin.produksi.index')
                    ]);
                }
                
                foreach ($criticalStock as $b) {
                    $notifications->push([
                        'type' => 'stock',
                        'title' => $b->nama_bahan,
                        'subtitle' => 'Stok: ' . $b->stok . ' ' . $b->satuan,
                        'status' => 'Kritis',
                        'date' => $b->updated_at,
                        'route' => route('admin.bahan.index')
                    ]);
                }
                
                $notifications = $notifications->sortByDesc('date')->take(5);
            @endphp
            
            @forelse($notifications as $index => $notif)
                @php
                    $isUnread = $index < 2; // First 2 are "unread" for demo
                    
                    if ($notif['type'] === 'production') {
                        $statusInfo = match($notif['status']) {
                            'SELESAI', 'Selesai', 'DONE', 'Done' => ['bg' => 'green', 'icon' => 'fa-check-circle', 'label' => 'Selesai'],
                            'PROSES', 'Proses', 'PROCESS' => ['bg' => 'blue', 'icon' => 'fa-spinner fa-spin', 'label' => 'Diproses'],
                            'PENDING', 'Pending' => ['bg' => 'yellow', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                            default => ['bg' => 'gray', 'icon' => 'fa-question-circle', 'label' => $notif['status']]
                        };
                    } else {
                        $statusInfo = ['bg' => 'red', 'icon' => 'fa-exclamation-triangle', 'label' => 'Kritis'];
                    }
                @endphp
                <a href="{{ $notif['route'] }}" 
                   class="flex items-start gap-4 px-5 py-4 border-b border-gray-100 hover:bg-gray-50 transition duration-150 group {{ $isUnread ? 'bg-red-50/50' : 'bg-white' }}">
                    {{-- Icon with color based on type --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 
                        @if($statusInfo['bg'] === 'green') bg-green-100 text-green-600
                        @elseif($statusInfo['bg'] === 'blue') bg-blue-100 text-blue-600
                        @elseif($statusInfo['bg'] === 'yellow') bg-yellow-100 text-yellow-600
                        @elseif($statusInfo['bg'] === 'red') bg-red-100 text-red-600
                        @else bg-gray-100 text-gray-600 @endif">
                        <i class="fas {{ $statusInfo['icon'] }}"></i>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-gray-800 truncate group-hover:text-red-600 transition">
                                {{ $notif['title'] }}
                            </p>
                            @if($isUnread)
                                <span class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            <span class="font-medium">{{ $notif['subtitle'] }}</span>
                            <span class="mx-1">•</span>
                            <span class="@if($statusInfo['bg'] === 'green') text-green-600 @elseif($statusInfo['bg'] === 'blue') text-blue-600 @elseif($statusInfo['bg'] === 'yellow') text-yellow-600 @elseif($statusInfo['bg'] === 'red') text-red-600 @endif">
                                {{ $statusInfo['label'] }}
                            </span>
                        </p>
                        <p class="text-[11px] text-gray-400 mt-1">
                            <i class="far fa-clock mr-1"></i>
                            {{ $notif['date']->diffForHumans() }}
                        </p>
                    </div>
                    
                    {{-- Arrow --}}
                    <div class="text-gray-300 group-hover:text-red-500 transition">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </a>
            @empty
                <div class="px-5 py-12 text-center">
                    <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Semua aman</p>
                    <p class="text-gray-400 text-xs mt-1">Tidak ada notifikasi terbaru</p>
                </div>
            @endforelse
        </div>
        
        {{-- Dropdown Footer --}}
        <div class="bg-gray-50 px-5 py-3 border-t border-gray-200">
            <a href="{{ route('admin.produksi.index') }}" class="flex items-center justify-center text-sm text-red-600 hover:text-red-700 font-medium transition">
                Pantau Produksi 
                <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
