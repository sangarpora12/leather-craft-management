<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md mx-auto bg-[#0B0E14] text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    <?php echo $__env->make('components.operator-header', ['title' => 'PENERIMAAN BAHAN'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Filter Deadline -->
    <div class="px-5 py-3">
        <form method="GET" action="<?php echo e(route('operator.belanja.index')); ?>">
            <select name="deadline_filter" onchange="this.form.submit()" class="w-full bg-[#1C222D] border border-white/5 rounded-lg px-4 py-3 text-sm text-white focus:border-emerald-500 focus:outline-none">
                <option value="" <?php echo e(!request('deadline_filter') ? 'selected' : ''); ?>>📅 Semua Deadline</option>
                <option value="hari_ini" <?php echo e(request('deadline_filter') == 'hari_ini' ? 'selected' : ''); ?>>📍 Hari Ini</option>
                <option value="minggu_ini" <?php echo e(request('deadline_filter') == 'minggu_ini' ? 'selected' : ''); ?>>📆 Minggu Ini</option>
                <option value="terlewat" <?php echo e(request('deadline_filter') == 'terlewat' ? 'selected' : ''); ?>>⚠️ Terlewat (Overdue)</option>
            </select>
        </form>
    </div>

    <!-- Stats -->
    <div class="px-5 py-3">
        <div class="bg-[#1C222D] border border-white/5 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-normal uppercase">Menunggu Penerimaan</span>
                        <div class="text-lg font-normal text-white"><?php echo e($pendingPengadaan->total()); ?> Item</div>
                    </div>
                </div>
                <span class="text-xs bg-green-500/20 text-green-500 border border-green-500/30 px-3 py-1 rounded-full font-normal uppercase">Pending</span>
            </div>
        </div>
    </div>

    <!-- Pending Procurement Tasks List -->
    <div class="px-5 pb-4">
        <?php $__empty_1 = true; $__currentLoopData = $pendingPengadaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengadaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $sisa = $pengadaan->total_jumlah - $pengadaan->total_received;
            $sudahDiterima = $pengadaan->total_received;
            $deadline = \Carbon\Carbon::parse($pengadaan->deadline_terdekat);
            $isOverdue = $deadline->isPast() && $sisa > 0;
        ?>
        <div class="rounded-xl p-5 mb-4 <?php echo e($isOverdue ? 'bg-red-500/10 border border-red-500/40' : 'bg-[#1C222D] border border-white/5'); ?>">
            <div class="flex gap-4 items-start mb-4">
                <!-- FOTO BAHAN -->
                <div class="flex-shrink-0">
                    <?php if($pengadaan->bahan && $pengadaan->bahan->foto): ?>
                        <img src="<?php echo e(asset('storage/' . $pengadaan->bahan->foto)); ?>" class="w-14 h-14 rounded-xl object-cover border border-white/5">
                    <?php else: ?>
                        <div class="w-14 h-14 rounded-xl bg-[#1C222D] flex items-center justify-center border border-white/5">
                            <i class="fa-solid fa-box text-gray-500 text-xl"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <?php if($pengadaan->prioritas === 'URGENT'): ?>
                        <div class="bg-red-500/20 border border-red-500/20 rounded px-2 py-1 inline-flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded mr-2 animate-pulse"></div>
                            <span class="text-xs text-red-500 font-normal uppercase"><?php echo e($pengadaan->prioritas); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="bg-blue-500/20 border border-blue-500/20 rounded px-2 py-1 inline-flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                            <span class="text-xs text-blue-500 font-normal uppercase"><?php echo e($pengadaan->prioritas); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-base font-normal text-white"><?php echo e($pengadaan->bahan->nama_material ?? 'Material Tidak Ditemukan'); ?></div>
                    <div class="text-sm text-gray-500 font-normal">Gabungan dari <?php echo e($pengadaan->jumlah_order); ?> pesanan</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs text-gray-500 font-normal">Dipesan</span>
                    <div class="text-lg font-normal text-blue-400"><?php echo e($pengadaan->total_jumlah); ?> <span class="text-xs"><?php echo e($pengadaan->bahan->satuan ?? 'pcs'); ?></span></div>
                    <div class="text-xs text-gray-500">Total dari <?php echo e($pengadaan->jumlah_order); ?> order</div>
                </div>
            </div>
            
            <!-- Status Penerimaan -->
            <div class="bg-[#0B0E14]/50 rounded-lg p-3 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-400 font-normal">Sudah Diterima</span>
                    <span class="text-sm font-normal text-green-400"><?php echo e($sudahDiterima); ?> <?php echo e($pengadaan->bahan->satuan ?? 'pcs'); ?></span>
                </div>
                <div class="w-full bg-[#1C222D] rounded-full h-2 mb-2">
                    <?php $progress = $pengadaan->total_jumlah > 0 ? ($sudahDiterima / $pengadaan->total_jumlah) * 100 : 0; ?>
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo e($progress); ?>%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-normal">Sisa Belum Datang</span>
                    <span class="text-sm font-normal text-orange-400"><?php echo e($sisa); ?> <?php echo e($pengadaan->bahan->satuan ?? 'pcs'); ?></span>
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 <?php echo e($isOverdue ? 'text-red-400' : 'text-gray-500'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-xs font-normal <?php echo e($isOverdue ? 'text-red-400 font-bold' : 'text-gray-400'); ?>">Deadline: <?php echo e(\Carbon\Carbon::parse($pengadaan->deadline_terdekat)->format('d M Y')); ?></span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs bg-[#1C222D] text-gray-300 border border-white/5 px-3 py-1 rounded font-normal"><?php echo e($pengadaan->bahan->satuan ?? 'Unit'); ?></span>
                    <button onclick="openNotesModal(<?php echo e($pengadaan->bahan_baku_id); ?>)" class="text-xs px-3 py-1 rounded font-normal bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30">
                        🔍 Lihat Detail
                    </button>
                </div>
            </div>
            
            
            <?php if($sisa > 0): ?>
            <div class="w-full py-2 rounded-lg text-sm font-normal flex items-center justify-center gap-2 <?php echo e($isOverdue ? 'bg-red-500/10 border border-red-500 text-red-400' : 'bg-[#1C222D]/50 text-gray-400'); ?>">
                <?php if($isOverdue): ?>
                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>⚠️ DEADLINE TERLEWAT - SEGERA BELI</span>
                <?php else: ?>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Menunggu Penerimaan</span>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <!-- Sudah lengkap -->
            <div class="w-full bg-green-500/10 border border-green-500/20 text-green-400 py-3 rounded-lg text-sm font-normal uppercase flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Selesai - Otomatis terupdate
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="py-12 border-2 border-dashed border-white/5 rounded-xl text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-sm text-gray-500 font-normal">Tidak ada bahan yang menunggu penerimaan</span>
        </div>
        <?php endif; ?>

        <?php if($pendingPengadaan->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($pendingPengadaan->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Penerimaan Bahan -->
<div id="receiveModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#1C222D] border border-white/5 rounded-2xl w-full max-w-sm">
        <div class="p-5 border-b border-white/5">
            <h3 class="text-lg font-bold text-white">Terima Barang</h3>
            <p class="text-sm text-gray-400" id="modalMaterialName">Nama Material</p>
        </div>
        
        <form id="receiveForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="p-5">
                <div class="bg-[#0B0E14]/50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-gray-400">Jumlah Dipesan</span>
                        <span class="text-sm font-bold text-white" id="modalJumlahDipesan">0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-gray-400">Sudah Diterima</span>
                        <span class="text-sm font-bold text-green-400" id="modalSudahDiterima">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-gray-400">Sisa</span>
                        <span class="text-sm font-bold text-orange-400" id="modalSisa">0</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="text-xs text-gray-400 block mb-2">Jumlah Barang Diterima</label>
                    <input type="number" 
                           name="jumlah_diterima" 
                           id="jumlahDiterima"
                           min="1" 
                           class="w-full bg-[#0B0E14] border border-white/5 rounded-lg px-4 py-3 text-white font-bold text-lg focus:outline-none focus:border-green-500"
                           required>
                    <p class="text-xs text-gray-500 mt-2">Masukkan jumlah barang yang benar-benar datang</p>
                </div>
                
                <input type="hidden" name="pengadaan_id" id="pengadaanId">
            </div>
            
            <div class="p-5 border-t border-white/5 flex gap-3">
                <button type="button" onclick="closeReceiveModal()" class="flex-1 bg-[#1C222D] hover:bg-[#0B0E14] text-white py-3 rounded-lg font-normal uppercase">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-400 text-white py-3 rounded-lg font-bold uppercase">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Catatan Admin -->
<div id="notesModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#1C222D] border border-white/5 rounded-2xl w-full max-w-sm">
        <div class="p-5 border-b border-white/5">
            <h3 class="text-lg font-bold text-white" id="notesModalTitle">Catatan Admin</h3>
            <p class="text-sm text-gray-400" id="notesModalSubtitle">Nama Bahan</p>
        </div>
        <div class="p-5" id="notesContent">
        </div>
        <div class="p-5 border-t border-white/5">
            <button onclick="closeNotesModal()" class="w-full bg-[#1C222D] hover:bg-[#0B0E14] text-white py-3 rounded-lg font-normal uppercase">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
const allMaterials = {
    <?php $__currentLoopData = $pendingPengadaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengadaan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php echo e($pengadaan->bahan_baku_id); ?>: {
        name: <?php echo json_encode($pengadaan->bahan->nama_material, 15, 512) ?>,
        total_urgent: <?php echo e($pengadaan->total_urgent); ?>,
        total_biasa: <?php echo e($pengadaan->total_biasa); ?>,
        count_urgent: <?php echo e($pengadaan->count_urgent); ?>,
        count_biasa: <?php echo e($pengadaan->count_biasa); ?>,
        notes: <?php echo json_encode($pengadaan->catatan_admin, 15, 512) ?>
    },
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
};

function openNotesModal(bahanId) {
    const data = allMaterials[bahanId];
    document.getElementById('notesModalSubtitle').textContent = data.name;
    const container = document.getElementById('notesContent');
    
    let html = `
        <div class="space-y-4">
            <!-- Rincian Prioritas -->
            <div>
                <h4 class="text-xs text-gray-400 uppercase mb-2">Rincian Prioritas</h4>
                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-3 text-center">
                        <div class="text-xl font-bold text-red-400">${data.total_urgent}</div>
                        <div class="text-xs text-red-400">URGENT</div>
                    </div>
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 text-center">
                        <div class="text-xl font-bold text-blue-400">${data.total_biasa}</div>
                        <div class="text-xs text-blue-400">BIASA</div>
                    </div>
                </div>
            </div>
            
            <!-- Daftar Catatan Admin -->
            <div>
                <h4 class="text-xs text-gray-400 uppercase mb-2">Catatan Admin</h4>
    `;
    
    if (!data.notes || data.notes.length === 0) {
        html += `
            <div class="text-center text-sm text-gray-500 py-4 bg-[#0B0E14]/50 rounded-lg">
                Tidak ada catatan khusus dari Admin.
            </div>
        `;
    } else {
        html += `<ul class="space-y-2">`;
        data.notes.forEach(note => {
            html += `
                <li class="pl-3 border-l-2 border-orange-500/50 text-sm text-gray-300 py-2 bg-[#0B0E14]/30 rounded pr-3">
                    ${note}
                </li>
            `;
        });
        html += `</ul>`;
    }
    
    html += `
        </div>
    </div>
    `;
    
    container.innerHTML = html;
    document.getElementById('notesModal').classList.remove('hidden');
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.add('hidden');
}

// Close notes modal when clicking outside
document.getElementById('notesModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNotesModal();
    }
});

function openReceiveModal(id, nama, jumlah, sudahTerima, sisa, satuan) {
    document.getElementById('pengadaanId').value = id;
    document.getElementById('modalMaterialName').textContent = nama;
    document.getElementById('modalJumlahDipesan').textContent = jumlah + ' ' + satuan;
    document.getElementById('modalSudahDiterima').textContent = sudahTerima + ' ' + satuan;
    document.getElementById('modalSisa').textContent = sisa + ' ' + satuan;
    document.getElementById('jumlahDiterima').max = sisa;
    document.getElementById('jumlahDiterima').value = '';
    document.getElementById('jumlahDiterima').focus();
    
    // Update form action
    const form = document.getElementById('receiveForm');
    form.action = '/operator/pengadaan/' + id + '/receive';
    
    document.getElementById('receiveModal').classList.remove('hidden');
}

function closeReceiveModal() {
    document.getElementById('receiveModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('receiveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceiveModal();
    }
});

// Handle form submission
document.getElementById('receiveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const jumlahInput = document.getElementById('jumlahDiterima');
    const jumlah = parseInt(jumlahInput.value);
    const max = parseInt(jumlahInput.max);
    
    if (!jumlah || jumlah < 1) {
        alert('Masukkan jumlah yang valid');
        return;
    }
    
    if (jumlah > max) {
        alert('Jumlah tidak boleh melebihi sisa (' + max + ')');
        return;
    }
    
    // Submit form
    this.submit();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/belanja/index.blade.php ENDPATH**/ ?>