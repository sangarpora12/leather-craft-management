<!-- MODAL LIHAT LAYER STOK (FIFO) -->
<div id="modalStockLayers" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;">
    <div style="position:fixed;inset:0;background:rgba(0,0,0,0.8);" onclick="closeModal('modalStockLayers')"></div>
    <div style="position:relative;background:#1A1A1A;width:100%;max-width:42rem;border-radius:1.5rem;border:1px solid rgba(255,255,255,0.1);padding:1.5rem;margin:1rem;max-height:80vh;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <div>
                <h2 style="font-size:1.25rem;font-weight:bold;color:white;font-style:italic;text-transform:uppercase;">Riwayat Stok Layer (FIFO)</h2>
                <p style="font-size:0.625rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.1em;" id="layerMaterialName">Loading...</p>
            </div>
            <button onclick="closeModal('modalStockLayers')" style="color:#9ca3af;background:rgba(255,255,255,0.05);width:2.5rem;height:2.5rem;border-radius:50%;border:1px solid rgba(255,255,255,0.1);">
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
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.layers && data.layers.length > 0) {
            let html = '<table style="width:100%;border-collapse:collapse;font-size:0.875rem;">';
            html += '<thead><tr style="border-bottom:1px solid #374151;">';
            html += '<th style="padding:0.75rem;text-align:left;color:#9ca3af;">Tanggal</th>';
            html += '<th style="padding:0.75rem;text-align:right;color:#9ca3af;">Sisa</th>';
            html += '<th style="padding:0.75rem;text-align:right;color:#9ca3af;">Harga</th>';
            html += '<th style="padding:0.75rem;text-align:left;color:#9ca3af;">Sumber</th>';
            html += '</tr></thead><tbody>';
            
            data.layers.forEach(function(layer) {
                html += '<tr style="border-bottom:1px solid #1f2937;">';
                html += '<td style="padding:0.75rem;color:white;">' + layer.tanggal_masuk + '</td>';
                html += '<td style="padding:0.75rem;text-align:right;color:#10b981;font-weight:bold;">' + layer.qty_sisa + '</td>';
                html += '<td style="padding:0.75rem;text-align:right;color:white;">Rp ' + new Intl.NumberFormat('id-ID').format(layer.harga_beli) + '</td>';
                html += '<td style="padding:0.75rem;color:#9ca3af;">' + (layer.sumber_harga || '-') + '</td>';
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            document.getElementById('stockLayersContent').innerHTML = html;
        } else {
            document.getElementById('stockLayersContent').innerHTML = '<div style="text-align:center;padding:2rem;color:#9ca3af;">Belum ada layer stok untuk material ini.</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('stockLayersContent').innerHTML = '<div style="text-align:center;padding:2rem;color:#ef4444;">Gagal memuat data.</div>';
    });
}
</script>
