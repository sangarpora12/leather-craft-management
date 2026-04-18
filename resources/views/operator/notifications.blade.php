@extends('layouts.operator_app')

@section('content')
@include('components.operator-header', ['title' => 'NOTIFIKASI'])

<div class="px-5">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Tugas dari Admin</h2>
        <p class="text-sm text-gray-500">Daftar tugas produksi yang diberikan kepada Anda</p>
    </div>

    <div class="space-y-4" id="notifications-list">
        @php
            // Get all production tasks for this operator
            $allTasks = \App\Models\Produksi::with('product')
                ->where('operator_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp
        
        @forelse($allTasks as $task)
            @php
                $statusColor = match($task->status) {
                    'SELESAI', 'Selesai', 'DONE', 'Done' => 'bg-green-100 text-green-800',
                    'PROSES', 'Proses', 'PROCESS' => 'bg-blue-100 text-blue-800',
                    'PENDING', 'Pending' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-gray-100 text-gray-800'
                };
            @endphp
            <a href="{{ route('operator.tugas.index') }}"
               class="notification-item block bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-gray-100">
                        <i class="fas fa-clipboard-list text-gray-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-800">{{ $task->product ? $task->product->nama_produk : 'Produk' }}</p>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                {{ $task->status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $task->kode_spk ?? '-' }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ $task->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                <p class="text-gray-500">Tidak ada tugas produksi</p>
                <p class="text-sm text-gray-400">Admin akan memberikan tugas produksi kepada Anda</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-6 text-center">
        <a href="{{ route('operator.tugas.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
            Lihat Semua Tugas
        </a>
    </div>
</div>
@endsection
