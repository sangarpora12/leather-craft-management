@extends('layouts.operator_app')

@section('title', 'Daftar Tugas Produksi')

@section('main_class', 'pb-28')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .glass-card {
        background: rgba(30, 30, 30, 0.6) !important;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#0f172a] bg-[radial-gradient(circle_at_top,#1e293b,transparent)]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">

        @include('components.operator-header', ['title' => 'TUGAS'])

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="px-5 py-4">
                <div class="glass-card p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-emerald-400 mr-2"></i>
                        <span class="text-emerald-400 font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-5 py-4">
                <div class="glass-card p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                        <span class="text-red-400 font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @php
            /**
             * RULE UTAMA (anti bug "klik 1 berubah 3"):
             * - Semua aksi HARUS pakai $spk->id (ID Produksi)
             * - Tidak ada grouping berdasarkan produk
             */
            $activeTab = request('tab', 'pending');
        @endphp

        {{-- ====================== TAB NAVIGATION ====================== --}}
        <div class="px-5 py-3">
            <div class="flex gap-2">
                <a href="?tab=pending"
                   class="flex-1 text-center py-3 px-2 rounded-2xl font-bold text-sm transition-all {{ $activeTab === 'pending' ? 'bg-amber-500 text-white' : 'bg-white/5 text-slate-400 hover:bg-white/10' }}"
                   style="{{ $activeTab === 'pending' ? '' : 'border: 1px solid rgba(255,255,255,0.1)' }}">
                    📥 Tugas Masuk
                    <span class="ml-1 text-xs {{ $activeTab === 'pending' ? 'text-white/80' : 'text-slate-500' }}">({{ $pendingTasks->count() }})</span>
                </a>
                <a href="?tab=process"
                   class="flex-1 text-center py-3 px-2 rounded-2xl font-bold text-sm transition-all {{ $activeTab === 'process' ? 'bg-blue-500 text-white' : 'bg-white/5 text-slate-400 hover:bg-white/10' }}"
                   style="{{ $activeTab === 'process' ? '' : 'border: 1px solid rgba(255,255,255,0.1)' }}">
                    🔨 Diproses
                    <span class="ml-1 text-xs {{ $activeTab === 'process' ? 'text-white/80' : 'text-slate-500' }}">({{ $processTasks->count() }})</span>
                </a>
                <a href="?tab=done"
                   class="flex-1 text-center py-3 px-2 rounded-2xl font-bold text-sm transition-all {{ $activeTab === 'done' ? 'bg-emerald-500 text-white' : 'bg-white/5 text-slate-400 hover:bg-white/10' }}"
                   style="{{ $activeTab === 'done' ? '' : 'border: 1px solid rgba(255,255,255,0.1)' }}">
                    ✅ Selesai
                    <span class="ml-1 text-xs {{ $activeTab === 'done' ? 'text-white/80' : 'text-slate-500' }}">({{ $doneTasks->count() }})</span>
                </a>
            </div>
        </div>

        <div class="px-5 pb-32 space-y-8">

            {{-- ====================== TUGAS MASUK (PENDING) ====================== --}}
            <section id="section-pending" class="{{ $activeTab !== 'pending' ? 'hidden' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-black tracking-widest text-slate-300 uppercase">Tugas Masuk</h2>
                    <span class="text-xs text-slate-400">{{ $pendingTasks->count() }} SPK</span>
                </div>

                <div class="space-y-4">
                    @forelse($pendingTasks as $spk)
                        @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                    @empty
                        <div class="glass-card p-6 text-center text-slate-500 text-sm">
                            Tidak ada tugas masuk.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ====================== SEDANG DIBUAT (PROSES) ====================== --}}
            <section id="section-process" class="{{ $activeTab !== 'process' ? 'hidden' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-black tracking-widest text-slate-300 uppercase">Sedang Dibuat</h2>
                    <span class="text-xs text-slate-400">{{ $processTasks->count() }} SPK</span>
                </div>

                <div class="space-y-4">
                    @forelse($processTasks as $spk)
                        @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                    @empty
                        <div class="glass-card p-6 text-center text-slate-500 text-sm">
                            Belum ada tugas yang sedang dibuat.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ====================== RIWAYAT (SELESAI) ====================== --}}
            <section id="section-done" class="{{ $activeTab !== 'done' ? 'hidden' : '' }}">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-black tracking-widest text-slate-300 uppercase">Riwayat</h2>
                        <div class="flex items-center gap-2">
                            <button id="reset-filter" class="text-xs font-semibold text-slate-400 bg-black border border-slate-500/30 rounded-lg px-3 py-1.5 transition-all cursor-pointer hover:bg-slate-800">
                                Reset Filter
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4" id="done-tasks-list">
                        @forelse($doneTasks as $spk)
                            <div class="task-item" data-date="{{ $spk->created_at }}">
                                @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                             </div>
                        @empty
                            <div class="glass-card p-6 text-center text-slate-500 text-sm">
                                Belum ada tugas yang selesai.
                            </div>
                        @endforelse
                    </div>
            </section>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to active tab section on page load
    var activeTab = '{{ $activeTab }}';
    var sections = document.querySelectorAll('section');
    
    if (activeTab === 'pending') {
        sections[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else if (activeTab === 'process') {
        sections[1].scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else if (activeTab === 'done') {
        sections[2].scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Filter functionality for done tasks - auto filter month
    const resetButton = document.getElementById('reset-filter');
    const taskItems = document.querySelectorAll('.task-item');

    function filterTasks(filterValue) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const weekAgo = new Date(today);
        weekAgo.setDate(weekAgo.getDate() - 7);
        const monthAgo = new Date(today);
        monthAgo.setMonth(monthAgo.getMonth() - 1);

        taskItems.forEach(item => {
            const itemDate = new Date(item.dataset.date);
            let showItem = true;

            switch(filterValue) {
                case 'today':
                    showItem = itemDate >= today;
                    break;
                case 'week':
                    showItem = itemDate >= weekAgo;
                    break;
                case 'month':
                    showItem = itemDate >= monthAgo;
                    break;
                default: // all - reset filter
                    showItem = true;
            }

            item.style.display = showItem ? 'block' : 'none';
        });
    }

    // Auto apply filter bulan ini saat halaman dimuat
    filterTasks('month');

    // Reset filter button functionality
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            filterTasks('all');
        });
    }
});
</script>
@endsection