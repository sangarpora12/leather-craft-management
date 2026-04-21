<!-- MODAL DETAIL BAHAN BAKU -->
<div id="modalStockLayers" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;">
    <div style="position:fixed;inset:0;background:rgba(0,0,0,0.8);" onclick="closeModal('modalStockLayers')"></div>
    <div style="position:relative;background:#1A1C24;width:100%;max-width:40rem;border-radius:1.5rem;border:1px solid #2D3139;padding:1.5rem;margin:1rem;max-height:85vh;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div>
                <h2 style="font-size:1.25rem;font-weight:bold;color:white;">Detail Bahan Baku</h2>
                <p style="font-size:0.625rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.1em;" id="layerMaterialName">Loading...</p>
            </div>
            <button onclick="closeModal('modalStockLayers')" style="color:#9ca3af;background:#2D3139;width:2.5rem;height:2.5rem;border-radius:50%;border:1px solid #374151;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div id="stockLayersContent">
            <div style="text-align:center;padding:1rem;color:#9ca3af;" id="loadingLayers">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data...
            </div>
        </div>
    </div>
</div>

<script>
function viewStockLayers(id, namaMaterial) {
    document.getElementById('layerMaterialName').textContent = namaMaterial;
    document.getElementById('stockLayersContent').innerHTML = '<div style="text-align:center;padding:1rem;color:#9ca3af;" id="loadingLayers"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data...</div>';
    openModal('modalStockLayers');
    
    fetch('/admin/bahan/' + id + '/stock-layers', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        let html = '';
        
        // LAYOUT 2 KOLOM
        html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-0">';
        
        // KOLOM KIRI: FOTO
        html += '<div class="flex flex-col items-center justify-center">';
        if(data.bahan.foto) {
            html += '<img src="/storage/' + data.bahan.foto + '" class="w-32 h-32 rounded-xl object-cover border border-[#2D3139] mb-2">';
        } else {
            html += '<div class="w-32 h-32 rounded-xl bg-[#2D3139] flex items-center justify-center border border-gray-700/50 mb-2"><i class="fa-solid fa-box text-gray-500 text-3xl"></i></div>';
        }
        html += '</div>';
        
        // KOLOM KANAN: DETAIL TEKS
        html += '<div class="space-y-2 text-xs">';
        html += '<div class="flex justify-between py-1 border-b border-[#2D3139]"><span class="text-gray-500">Kode Material</span><span class="text-[#FD730D] font-bold">' + data.bahan.kode_material + '</span></div>';
        html += '<div class="flex justify-between py-1 border-b border-[#2D3139]"><span class="text-gray-500">Nama</span><span class="text-white font-bold">' + data.bahan.nama_material + '</span></div>';
        html += '<div class="flex justify-between py-1 border-b border-[#2D3139]"><span class="text-gray-500">Stok Saat Ini</span><span class="text-[#00BFA5] font-bold">' + parseInt(data.bahan.stok) + ' ' + data.bahan.satuan + '</span></div>';
        html += '<div class="flex justify-between py-1 border-b border-[#2D3139]"><span class="text-gray-500">Kategori</span><span class="text-white">' + data.bahan.kategori + '</span></div>';
        html += '<div class="flex justify-between py-1 border-b border-[#2D3139]"><span class="text-gray-500">Harga Satuan</span><span class="text-white">Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(data.bahan.harga_satuan)) + '</span></div>';
        html += '</div>';
        html += '</div>';

        // TOMBOL COLLAPSE FIFO
        html += '<div class="mt-4">';
        html += '<button onclick="toggleFifoTable()" style="width:100%;padding:0.75rem;background:#2D3139;color:#9ca3af;border-radius:0.75rem;border:none;cursor:pointer;font-size:0.75rem;" class="flex justify-between items-center">';
        html += '<span><i class="fa-solid fa-clock-rotate-left mr-2"></i> Lihat Riwayat Stok (FIFO)</span>';
        html += '<i class="fa-solid fa-chevron-down transition-transform" id="fifoChevron"></i>';
        html += '</button>';
        html += '</div>';

        // TABEL FIFO (SEMBUNYI AWAL)
        html += '<div id="fifoTableContainer" style="display:none;margin-top:1rem;" class="overflow-x-auto">';
        
        if (data.layers && data.layers.length > 0) {
            html += '<table style="width:100%;border-collapse:collapse;font-size:0.75rem;">';
            html += '<thead><tr style="border-bottom:1px solid #374151;">';
            html += '<th style="padding:0.5rem;text-align:left;color:#9ca3af;">Tanggal</th>';
            html += '<th style="padding:0.5rem;text-align:right;color:#9ca3af;">Sisa</th>';
            html += '<th style="padding:0.5rem;text-align:right;color:#9ca3af;">Harga</th>';
            html += '</tr></thead><tbody>';
            
            data.layers.forEach(function(layer) {
                html += '<tr style="border-bottom:1px solid #1f2937;">';
                html += '<td style="padding:0.5rem;color:white;">' + layer.tanggal_masuk + '</td>';
                html += '<td style="padding:0.5rem;text-align:right;color:#10b981;font-weight:bold;">' + parseInt(layer.qty_sisa) + '</td>';
                html += '<td style="padding:0.5rem;text-align:right;color:white;">Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(layer.harga_beli)) + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
        } else {
            html += '<div style="text-align:center;padding:1.5rem;color:#9ca3af;font-size:0.75rem;">Belum ada riwayat layer stok untuk material ini.</div>';
        }

        html += '</div>';

        document.getElementById('stockLayersContent').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('stockLayersContent').innerHTML = '<div style="text-align:center;padding:2rem;color:#ef4444;">Gagal memuat data.</div>';
    });
}

// FUNGSI TOGGLE FIFO TABLE
function toggleFifoTable() {
    const container = document.getElementById('fifoTableContainer');
    const chevron = document.getElementById('fifoChevron');
    
    if(container.style.display === 'none') {
        container.style.display = 'block';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        container.style.display = 'none';
        chevron.style.transform = 'rotate(0deg)';
    }
}
</script><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/bahan/_stock-layers-modal.blade.php ENDPATH**/ ?>