@extends('layouts.operator_app')

@section('title', 'Notifikasi')

@section('main_class', 'pb-28')

@section('content')
<div class="min-h-screen bg-[#0f172a] bg-[radial-gradient(circle_at_top,#1e293b,transparent)]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">
        @include('components.operator-header', ['title' => 'NOTIFIKASI'])

        <div class="px-5 py-5 pb-32">
            <div class="bg-slate-900/60 border border-slate-700 rounded-3xl overflow-hidden">
                <div class="p-4 border-b border-slate-700 flex items-center justify-between">
                    <div class="text-white font-bold">Semua Notifikasi</div>
                    <form method="POST" action="{{ route('operator.notifications.mark-all-read') }}">
                        @csrf
                        <button class="text-xs font-semibold px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white hover:bg-slate-700">
                            Tandai dibaca
                        </button>
                    </form>
                </div>

                <div>
                    @forelse($paginatedNotifications as $notif)
                        @php
                            $isUnread = is_null($notif->read_at);
                            $title = $notif->data['judul'] ?? ($notif->data['title'] ?? 'Notifikasi');
                            $message = $notif->data['pesan'] ?? ($notif->data['message'] ?? ($notif->data['body'] ?? '-'));
                        @endphp
                        <a href="{{ route('operator.notifications.read', $notif->id) }}"
                           class="block px-4 py-4 border-b border-slate-800 hover:bg-slate-800/40 {{ $isUnread ? 'bg-blue-500/10' : '' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full mt-2 {{ $isUnread ? 'bg-red-500' : 'bg-slate-600' }}"></div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold text-sm">{{ $title }}</div>
                                    <div class="text-slate-300 text-xs mt-1">{{ $message }}</div>
                                    <div class="text-slate-500 text-[11px] mt-2">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-slate-400">Belum ada notifikasi.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-4">
                {{ $paginatedNotifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
