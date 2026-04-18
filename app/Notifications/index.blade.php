@extends('layouts.admin')
@section('title_header', 'Kotak Masuk Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-white">Semua Notifikasi</h2>
        <div class="text-xs text-gray-500">
            Total: {{ $notifications->total() }} Pesan
        </div>
    </div>

    <div class="bg-[#111] border border-white/10 rounded-xl overflow-hidden shadow-2xl">
        
        {{-- LIST NOTIFIKASI --}}
        <div class="divide-y divide-white/5">
            @forelse($notifications as $notif)
                @php
                    // Cek status baca
                    $isUnread = $notif->read_at === null;
                    // Styling Gmail: Unread = Background agak terang, Teks Bold/Putih
                    // Read = Background gelap, Teks Abu
                    $bgClass = $isUnread ? 'bg-[#1a1a1a] border-l-4 border-emerald-500' : 'bg-[#0a0a0a] opacity-80';
                    $textClass = $isUnread ? 'text-white font-bold' : 'text-gray-400 font-normal';
                @endphp

                {{-- Link ke Route Read & Redirect --}}
                <a href="{{ route('admin.notifications.read', $notif->id) }}" 
                   class="block p-4 hover:bg-[#222] transition group {{ $bgClass }}">
                    
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="mt-1">
                            @if($notif->data['type'] ?? '' == 'warning')
                                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500"><i class="fas fa-exclamation-triangle"></i></div>
                            @elseif($notif->data['type'] ?? '' == 'success')
                                <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500"><i class="fas fa-check"></i></div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-500"><i class="fas fa-info"></i></div>
                            @endif
                        </div>

                        {{-- Konten Pesan --}}
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm {{ $textClass }} group-hover:text-emerald-400 transition">
                                    {{ $notif->data['title'] ?? 'Pemberitahuan Sistem' }}
                                </h4>
                                <span class="text-[10px] text-gray-600 whitespace-nowrap ml-2">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                {{ $notif->data['message'] ?? 'Tidak ada detail pesan.' }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-10 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-4xl mb-3 opacity-20"></i>
                    <p class="text-sm">Tidak ada notifikasi saat ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
        <div class="p-4 border-t border-white/5 bg-[#151515]">
            {{ $notifications->links() }} 
            {{-- Pastikan pakai pagination view tailwind atau custom style --}}
        </div>
        @endif

    </div>
</div>
@endsection