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
    .task-checkbox:checked + .glass-card {
        background: rgba(50, 20, 20, 0.8) !important;
        border-color: rgba(239, 68, 68, 0.5);
    }
    .task-checkbox:checked + .glass-card .check-icon {
        opacity: 1;
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
                <form action="{{ route('operator.tugas.bulk-delete') }}" method="POST" id="delete-form">
                    @csrf
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-black tracking-widest text-slate-300 uppercase">Riwayat</h2>
                        <div class="flex items-center gap-2">
                            <button type="button" id="select-all-btn"
                                    class="text-xs font-semibold text-slate-400 bg-slate-500/10 border border-slate-500/30 rounded-lg px-3 py-1.5 transition-all">
                                Pilih Semua
                            </button>
                            <button type="button" id="delete-btn" disabled
                                    class="text-xs font-semibold text-red-400 bg-red-500/10 border border-red-500/30 rounded-lg px-3 py-1.5 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                                Hapus <span id="delete-counter" class="hidden ml-1"></span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($doneTasks as $spk)
                            @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                        @empty
                            <div class="glass-card p-6 text-center text-slate-500 text-sm">
                                Belum ada tugas yang selesai.
                            </div>
                        @endforelse
                    </div>
                </form>
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

    // Bulk delete functionality
    const deleteBtn = document.getElementById('delete-btn');
    const deleteForm = document.getElementById('delete-form');
    const checkboxes = document.querySelectorAll('.task-checkbox');
    const deleteCounter = document.getElementById('delete-counter');
    const selectAllBtn = document.getElementById('select-all-btn');

    function updateDeleteButton() {
        const checkedCount = document.querySelectorAll('.task-checkbox:checked').length;
        if (checkedCount > 0) {
            deleteBtn.disabled = false;
            deleteCounter.textContent = `(${checkedCount})`;
            deleteCounter.classList.remove('hidden');
        } else {
            deleteBtn.disabled = true;
            deleteCounter.classList.add('hidden');
        }

        if (checkedCount === checkboxes.length && checkboxes.length > 0) {
            selectAllBtn.textContent = 'Batal Pilih';
        } else {
            selectAllBtn.textContent = 'Pilih Semua';
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButton();
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('is-checked');
            } else {
                label.classList.remove('is-checked');
            }
        });
    });

    selectAllBtn.addEventListener('click', function() {
        const areAllChecked = checkboxes.length > 0 && Array.from(checkboxes).every(c => c.checked);
        const shouldSelect = !areAllChecked;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked !== shouldSelect) {
                checkbox.checked = shouldSelect;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    deleteBtn.addEventListener('click', function() {
        const checkedCount = document.querySelectorAll('.task-checkbox:checked').length;
        if (checkedCount > 0) {
            Swal.fire({
                title: `Hapus ${checkedCount} tugas?`,
                text: "Tugas yang sudah dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: '#1e293b',
                color: '#cbd5e1'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteForm.submit();
                }
            })
        }
    });

    updateDeleteButton(); // Initial check
});
</script>
@endsection