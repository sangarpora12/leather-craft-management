@extends('layouts.admin')

@section('page_title', 'Perintah Produksi')

@section('content')
<div class="space-y-6 animate-fade-in font-sans text-gray-300">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">

        <a href="{{ route('admin.produksi.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 flex items-center gap-2 transition whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Buat SPK Baru
        </a>
    </div>

    <!-- Filter Panel -->
    <div class="bg-[#1f242c] border border-gray-800 rounded-xl p-4">
        <form method="GET" action="{{ route('admin.produksi.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end" onchange="this.form.submit()" oninput="this.form.submit()">
                
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Cari SPK / Produk</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Kode SPK atau Nama Produk..."
                           class="w-full bg-[#2D3139] border border-gray-700 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                    <select name="status" id="status" class="w-full bg-[#2D3139] border border-gray-700 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="semua" {{ request('status', 'semua') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                        <option value="PROSES" {{ request('status') == 'PROSES' ? 'selected' : '' }}>Proses</option>
                        <option value="SELESAI" {{ request('status') == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div>
                    <label for="operator_id" class="block text-sm font-medium text-gray-400 mb-1">Operator</label>
                    <select name="operator_id" id="operator_id" class="w-full bg-[#2D3139] border border-gray-700 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="semua">Semua Operator</option>
                        @foreach($operators as $operator)
                            <option value="{{ $operator->id }}" {{ request('operator_id') == $operator->id ? 'selected' : '' }}>{{ $operator->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="sort_by" class="block text-sm font-medium text-gray-400 mb-1">Urutkan Berdasarkan</label>
                    <select name="sort_by" id="sort_by" class="w-full bg-[#2D3139] border border-gray-700 text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="terbaru" {{ request('sort_by', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Tanggal Terbaru</option>
                        <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Tanggal Terlama</option>
                        <option value="target_terdekat" {{ request('sort_by') == 'target_terdekat' ? 'selected' : '' }}>Target Terdekat</option>
                        <option value="target_terjauh" {{ request('sort_by') == 'target_terjauh' ? 'selected' : '' }}>Target Terjauh</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-end gap-2">
                    <a href="{{ route('admin.produksi.index') }}" class="w-full sm:w-auto bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition text-sm text-center">
                        Reset Filter
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL DAFTAR SPK --}}
    <form id="bulk-delete-form" action="{{ route('admin.produksi.bulk_destroy') }}" method="POST">
        @csrf
        @method('DELETE')
        
        <div class="bg-[#1f242c] rounded-xl border border-gray-800 overflow-hidden shadow-xl">
            
            {{-- Tombol Aksi Massal --}}
            <div id="bulk-actions" class="hidden p-4 bg-[#111316] border-b border-gray-800">
                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded-lg transition text-sm flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i>
                    Hapus <span id="selected-count">0</span> SPK Selesai yang Ditandai
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#111316] text-gray-400 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">
                                <input type="checkbox" id="select-all" class="bg-gray-700 border-gray-600 rounded text-blue-500 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-4">Kode SPK</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Target</th>
                            <th class="px-6 py-4 text-center">Operator</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        {{-- Perhatikan variabelnya adalah $produksis, bukan $produks --}}
                        @forelse($produksis as $spk)
                        <tr class="hover:bg-[#252a33] transition group">
                            <td class="px-6 py-4">
                                @if($spk->status === 'SELESAI')
                                    <input type="checkbox" name="ids[]" value="{{ $spk->id }}" class="spk-checkbox bg-gray-700 border-gray-600 rounded text-blue-500 focus:ring-blue-500">
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-amber-400 bg-amber-400/10 px-2 py-1 rounded text-xs">{{ $spk->product->kode_produk ?? 'N/A' }}</span>
                                <div class="text-[10px] text-gray-500 mt-1 font-mono">{{ $spk->kode_spk }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($spk->product && $spk->product->foto)
                                        <img src="{{ asset('storage/' . $spk->product->foto) }}" class="w-10 h-10 rounded-lg object-cover bg-gray-800">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center text-sm"><i class="fa-solid fa-box"></i></div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-white">{{ $spk->product->nama_produk ?? 'Produk Dihapus' }}</div>
                                         <div class="mt-1 flex flex-col gap-1.5">
                                             <div class="flex flex-wrap gap-1">
                                                 @php
                                                     $kategoriList = [];
                                                     if ($spk->produk_jadi) {
                                                         if (is_array($spk->produk_jadi->kategori)) {
                                                             $kategoriList = $spk->produk_jadi->kategori;
                                                         } elseif (is_string($spk->produk_jadi->kategori)) {
                                                             $decoded = json_decode($spk->produk_jadi->kategori, true);
                                                             $kategoriList = is_array($decoded) ? $decoded : (trim($spk->produk_jadi->kategori) ? explode(',', $spk->produk_jadi->kategori) : []);
                                                         }
                                                     }
                                                 @endphp
                                                 @forelse($kategoriList as $kategori)
                                                     <span class="px-2 py-0.5 bg-sky-500/20 text-sky-300 border border-sky-500/40 rounded text-[10px] font-medium text-capitalize">
                                                         {{ trim($kategori) }}
                                                     </span>
                                                 @empty
                                                     <span class="text-[10px] text-gray-500">-</span>
                                                 @endforelse
                                             </div>

                                             <div class="flex flex-wrap gap-1">
                                                 @php
                                                     $warnaList = [];
                                                     if ($spk->produk_jadi) {
                                                         if (is_array($spk->produk_jadi->warna)) {
                                                             $warnaList = $spk->produk_jadi->warna;
                                                         } elseif (is_string($spk->produk_jadi->warna)) {
                                                             $decoded = json_decode($spk->produk_jadi->warna, true);
                                                             $warnaList = is_array($decoded) ? $decoded : (trim($spk->produk_jadi->warna) ? explode(',', $spk->produk_jadi->warna) : []);
                                                         }
                                                     }
                                                     $warnaMapping = [
                                                         'merah' => 'bg-red-500/20 text-red-300 border border-red-500/40',
                                                         'hitam' => 'bg-gray-800/80 text-gray-200 border border-gray-600/40',
                                                         'coklat' => 'bg-amber-900/40 text-amber-300 border border-amber-600/40',
                                                         'brown' => 'bg-amber-900/40 text-amber-300 border border-amber-600/40',
                                                         'cream' => 'bg-yellow-100/20 text-yellow-200 border border-yellow-500/40',
                                                         'putih' => 'bg-white/10 text-white border border-white/30',
                                                         'hijau' => 'bg-green-500/20 text-green-300 border border-green-500/40',
                                                         'biru' => 'bg-blue-500/20 text-blue-300 border border-blue-500/40',
                                                         'kuning' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/40',
                                                         'pink' => 'bg-pink-500/20 text-pink-300 border border-pink-500/40',
                                                         'orange' => 'bg-orange-500/20 text-orange-300 border border-orange-500/40',
                                                         'ungu' => 'bg-purple-500/20 text-purple-300 border border-purple-500/40',
                                                         'abu' => 'bg-gray-500/20 text-gray-300 border border-gray-500/40',
                                                         'black' => 'bg-gray-800/80 text-gray-200 border border-gray-600/40',
                                                         'dark brown' => 'bg-amber-900/50 text-amber-200 border border-amber-600/50',
                                                         'peach' => 'bg-orange-300/20 text-orange-200 border border-orange-400/40',
                                                     ];
                                                 @endphp
                                                 @forelse($warnaList as $warna)
                                                     @php $warnaStr = is_array($warna) ? json_encode($warna) : (string) $warna; @endphp
                                                     <span class="px-2 py-0.5 rounded text-[10px] font-medium text-capitalize {{ $warnaMapping[strtolower(trim($warnaStr))] ?? 'bg-gray-700/50 text-gray-200 border border-gray-600/30' }}">
                                                         {{ trim($warnaStr) }}
                                                     </span>
                                                 @empty
                                                     <span class="text-[10px] text-gray-500">-</span>
                                                 @endforelse
                                             </div>

                                             <div class="flex flex-wrap gap-1">
                                                 @php
                                                     $motifList = [];
                                                     if ($spk->produk_jadi) {
                                                         if (is_array($spk->produk_jadi->jenis_motif)) {
                                                             $motifList = $spk->produk_jadi->jenis_motif;
                                                         } elseif (is_string($spk->produk_jadi->jenis_motif)) {
                                                             $decoded = json_decode($spk->produk_jadi->jenis_motif, true);
                                                             $motifList = is_array($decoded) ? $decoded : (trim($spk->produk_jadi->jenis_motif) ? explode(',', $spk->produk_jadi->jenis_motif) : []);
                                                         }
                                                     }
                                                     $motifMapping = [
                                                         'polos' => 'bg-slate-500/20 text-slate-200 border border-slate-500/40',
                                                         'croco' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40',
                                                         'kulit buaya' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40',
                                                         'lizard' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40',
                                                         'jeruk' => 'bg-orange-500/20 text-orange-300 border border-orange-500/40',
                                                         'kulit jeruk' => 'bg-orange-500/20 text-orange-300 border border-orange-500/40',
                                                         'suede' => 'bg-amber-500/20 text-amber-300 border border-amber-500/40',
                                                         'nappa' => 'bg-violet-500/20 text-violet-300 border border-violet-500/40',
                                                         'crazy horse' => 'bg-rose-500/20 text-rose-300 border border-rose-500/40',
                                                     ];
                                                 @endphp
                                                 @forelse($motifList as $motif)
                                                     @php $motifStr = is_array($motif) ? json_encode($motif) : (string) $motif; @endphp
                                                     <span class="px-2 py-0.5 rounded text-[10px] font-medium text-capitalize {{ $motifMapping[strtolower(trim($motifStr))] ?? 'bg-indigo-700/40 text-indigo-200 border border-indigo-500/40' }}">
                                                         {{ trim($motifStr) }}
                                                     </span>
                                                 @empty
                                                     <span class="text-[10px] text-gray-500">-</span>
                                                 @endforelse
                                             </div>
                                         </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-white font-bold">{{ intval($spk->jumlah_produksi) }}</span>
                                <span class="text-xs text-gray-500">Pcs</span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs">
                                @if($spk->operator)
                                    <span class="font-semibold text-white">{{ $spk->operator->name }}</span>
                                @else
                                    <span class="text-gray-500 italic">- Belum Ada -</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                     $statusClass = match($spk->status) {
                                         'PENDING' => 'bg-transparent text-yellow-400 border border-yellow-400/50',
                                         'PROSES' => 'bg-transparent text-blue-400 border border-blue-400/50',
                                         'SELESAI' => 'bg-transparent text-emerald-400 border border-emerald-400/50',
                                         default => 'bg-transparent text-red-400 border border-red-400/50'
                                     };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                    {{ $spk->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($spk->status == 'PENDING' && $spk->created_at->diffInSeconds(now()) <= 10)
                                        <div x-data="{ 
                                            timeLeft: 10 - {{ $spk->created_at->diffInSeconds(now()) }},
                                            init() {
                                                let interval = setInterval(() => {
                                                    this.timeLeft--
                                                    if (this.timeLeft <= 0) {
                                                        clearInterval(interval)
                                                        $el.closest('div').remove()
                                                    }
                                                }, 1000)
                                            }
                                        }" class="flex items-center gap-2">
                                            <div class="relative w-7 h-7">
                                                <svg class="w-7 h-7 transform -rotate-90" viewBox="0 0 36 36">
                                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#374151" stroke-width="2"/>
                                                    <circle cx="18" cy="18" r="16" fill="none" stroke="#ef4444" stroke-width="2"
                                                        stroke-dasharray="100" 
                                                        :stroke-dashoffset="(100 - (timeLeft / 10 * 100))"
                                                        stroke-linecap="round"
                                                        class="transition-all duration-1000 ease-linear"/>
                                                </svg>
                                                <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-red-400" x-text="timeLeft"></div>
                                            </div>
                                            <form action="{{ route('admin.produksi.cancel', $spk->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold py-1 px-3 rounded text-xs transition">
                                                    Cancel
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                    <a href="{{ route('admin.produksi.show', $spk->id) }}" class="text-gray-500 hover:text-white hover:border-white/30 transition px-3 py-1.5 rounded-lg border border-transparent text-xs">
                                        <i class="fa-solid fa-eye mr-1"></i>Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-3 opacity-30"></i>
                                <p>Belum ada perintah produksi.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-800">
                {{ $produksis->appends(request()->query())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const spkCheckboxes = document.querySelectorAll('.spk-checkbox');
        const bulkActionsBar = document.getElementById('bulk-actions');
        const selectedCountSpan = document.getElementById('selected-count');
        const bulkDeleteForm = document.getElementById('bulk-delete-form');

        function updateBulkActions() {
            const selectedCheckboxes = document.querySelectorAll('.spk-checkbox:checked');
            const count = selectedCheckboxes.length;

            if (count > 0) {
                bulkActionsBar.classList.remove('hidden');
                selectedCountSpan.textContent = count;
            } else {
                bulkActionsBar.classList.add('hidden');
            }
            
            selectAllCheckbox.checked = count > 0 && count === spkCheckboxes.length;
        }

        selectAllCheckbox.addEventListener('change', function () {
            spkCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        spkCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        bulkDeleteForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const selectedCheckboxes = document.querySelectorAll('.spk-checkbox:checked');
            
            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'Tidak Ada Data Terpilih',
                    text: 'Silakan tandai SPK yang ingin dihapus terlebih dahulu.',
                    icon: 'warning',
                    background: '#1f242c',
                    color: '#e2e8f0',
                    confirmButtonColor: '#3b82f6',
                });
                return;
            }

            Swal.fire({
                title: `Yakin Hapus ${selectedCheckboxes.length} SPK?`,
                text: "Tindakan ini tidak dapat dibatalkan. Semua data terkait SPK yang dipilih akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                background: '#1f242c',
                color: '#e2e8f0',
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Initial check
        updateBulkActions();
    });
</script>
@endpush