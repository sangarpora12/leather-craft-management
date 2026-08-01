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
<div class="min-h-screen bg-[#0B0E14]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">

        @include('components.operator-header', ['title' => 'TUGAS'])

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="px-5 py-4">
                <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-emerald-400 mr-2"></i>
                        <span class="text-emerald-400 font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-5 py-4">
                <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                        <span class="text-red-400 font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ====================== TAB NAVIGATION ====================== --}}
        <div class="px-5 pt-4">
            <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-1.5 flex gap-1.5">
                <button onclick="switchTab('pending')" id="tab-pending" class="tab-button flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 bg-amber-700 text-white">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Belum Dikerjakan</span>
                        <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingTasks->count() }}</span>
                    </div>
                </button>
                
                <button onclick="switchTab('proses')" id="tab-proses" class="tab-button flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 bg-transparent text-gray-400 hover:text-gray-300">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Di Proses</span>
                        <span class="bg-white/10 text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ $processTasks->count() }}</span>
                    </div>
                </button>
                
                <button onclick="switchTab('selesai')" id="tab-selesai" class="tab-button flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-300 bg-transparent text-gray-400 hover:text-gray-300">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Selesai</span>
                        <span class="bg-white/10 text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ $doneTasks->count() }}</span>
                    </div>
                </button>
            </div>
        </div>

        {{-- ====================== TAB CONTENT ====================== --}}
        <div class="px-5 pb-32 space-y-8">
            
            {{-- PENDING TAB --}}
            <div id="content-pending" class="tab-content">
                <div class="space-y-4">
                    @forelse($pendingTasks as $spk)
                        @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                    @empty
                        <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-6 text-center text-gray-500 text-sm">
                            Tidak ada SPK yang menunggu.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- PROSES TAB --}}
            <div id="content-proses" class="tab-content hidden">
                <div class="space-y-4">
                    @forelse($processTasks as $spk)
                        @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                    @empty
                        <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-6 text-center text-gray-500 text-sm">
                            Belum ada SPK yang sedang dikerjakan.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SELESAI TAB --}}
            <div id="content-selesai" class="tab-content hidden">
                <div class="space-y-4">
                    @forelse($doneTasks as $spk)
                        @include('operator.tugas.partials.spk-card', ['spk' => $spk])
                    @empty
                        <div class="bg-[#1C222D] border border-white/5 rounded-2xl p-6 text-center text-gray-500 text-sm">
                            Belum ada SPK yang selesai.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pagination (pakai $produksis karena query utamanya paginate) --}}
            @if($produksis->hasPages())
                <div class="pt-2">
                    {{ $produksis->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

{{-- PUSH SCRIPTS: Pastikan ini ada di luar/bawah endsection --}}
@push('scripts')
<script>
    // window.startTugas membuat fungsi ini bisa dipanggil dari tombol di komponen mana saja
    window.startTugas = function(id) {
        Swal.fire({
            title: 'Mulai Kerjakan Tugas?',
            text: "Aksi ini akan mengubah status tugas menjadi 'PROSES'.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a', // emerald-600
            cancelButtonColor: '#52525b', // zinc-600
            confirmButtonText: 'Ya, Mulai Kerjakan!',
            cancelButtonText: 'Batal',
            background: '#1e293b', // slate-800
            color: '#e2e8f0' // slate-200
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memulai tugas...',
                    background: '#1e293b', // slate-800
                    color: '#e2e8f0', // slate-200
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
    
                fetch(`/operator/tugas/${id}/ambil`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Ambil token dengan aman
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Tugas telah dimulai.',
                            background: '#1e293b',
                            color: '#e2e8f0',
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.reload(); // Reload halaman biar pindah tab otomatis
                        });
                    } else {
                        throw new Error(data.message || 'Gagal memulai tugas.');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message || 'Terjadi kesalahan.',
                        background: '#1e293b',
                        color: '#e2e8f0',
                    });
                });
            }
        });
    }
</script>
    @endpush

    @push('scripts')
    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show selected tab content
            const selectedContent = document.getElementById('content-' + tabName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
            
            // Reset all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-amber-700', 'text-white');
                button.classList.add('bg-transparent', 'text-gray-400');
                
                // Reset badge styles
                const badge = button.querySelector('span:last-child');
                if (badge) {
                    badge.classList.remove('bg-white/20', 'text-white');
                    badge.classList.add('bg-white/10', 'text-gray-400');
                }
            });
            
            // Activate selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            if (activeButton) {
                activeButton.classList.remove('bg-transparent', 'text-gray-400');
                activeButton.classList.add('bg-amber-700', 'text-white');
                
                // Activate badge
                const activeBadge = activeButton.querySelector('span:last-child');
                if (activeBadge) {
                    activeBadge.classList.remove('bg-white/10', 'text-gray-400');
                    activeBadge.classList.add('bg-white/20', 'text-white');
                }
            }
            
            // Store selected tab in localStorage
            localStorage.setItem('selectedTugasTab', tabName);
        }
        
        // Load saved tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTab = localStorage.getItem('selectedTugasTab') || 'pending';
            switchTab(savedTab);
        });
    </script>
    @endpush
