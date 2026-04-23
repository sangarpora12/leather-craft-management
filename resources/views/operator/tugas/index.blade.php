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

        <div class="px-5 pb-32 space-y-8">

            {{-- ====================== PENDING ====================== --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-black tracking-widest text-white uppercase">Produk Belum Dikerjakan</h2>
                    <span class="text-xs text-gray-400">{{ $pendingTasks->count() }} SPK</span>
                </div>

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

            {{-- ====================== PROSES ====================== --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-black tracking-widest text-white uppercase">Produk Di Proses</h2>
                    <span class="text-xs text-gray-400">{{ $processTasks->count() }} SPK</span>
                </div>

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

            {{-- ====================== SELESAI ====================== --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-black tracking-widest text-white uppercase">Produk Selesai</h2>
                    <span class="text-xs text-gray-400">{{ $doneTasks->count() }} SPK</span>
                </div>

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
    
                fetch(`/operator/produksi/${id}/start`, {
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