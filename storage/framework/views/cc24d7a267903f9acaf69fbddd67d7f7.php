<!-- Modal untuk menampilkan layer stok -->
<div id="stockLayersModal" class="fixed inset-0 z-50 items-center justify-center hidden" style="background: rgba(0,0,0,0.8);">
    <div class="relative w-full max-w-2xl p-4 mx-auto my-8">
        <div class="relative bg-[#1A1C24] rounded-2xl shadow-lg border border-[#2D3139]">
            
            <div class="flex items-start justify-between p-5 border-b border-[#2D3139] rounded-t">
                <div>
                    <h3 class="text-xl font-bold text-white" id="layerMaterialName">
                        Detail Stok Material
                    </h3>
                    <p class="text-xs text-gray-500 uppercase tracking-wider mt-1">Rincian FIFO per Batch Pembelian</p>
                </div>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-600 hover:text-white rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" onclick="document.getElementById('stockLayersModal').classList.add('hidden')">
                    <i class="fa-solid fa-xmark text-xl w-5 h-5"></i>
                </button>
            </div>
            
            <div class="p-6 space-y-6">
                <div id="stockLayersContent" class="max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                    <!-- Konten dinamis dari JS -->
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function viewStockLayers(id, namaMaterial) {
    document.getElementById('layerMaterialName').textContent = namaMaterial;
    const modal = document.getElementById('stockLayersModal');
    const contentDiv = document.getElementById('stockLayersContent');
    
    contentDiv.innerHTML = '<div class="text-center py-8"><i class="fa-solid fa-spinner fa-spin text-2xl text-gray-500"></i><p class="mt-2 text-sm text-gray-400">Memuat data...</p></div>';
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch('/admin/bahan/' + id + '/stock-layers')
        .then(response => response.json())
        .then(data => {

            // Error Handling: Cek jika ada error sinkronisasi
            if (data.sync_error) {
                contentDiv.innerHTML = `<div class="text-center py-8">
                                            <i class="fa-solid fa-triangle-exclamation text-2xl text-yellow-500"></i>
                                            <p class="mt-2 text-sm text-yellow-400">${data.message}</p>
                                        </div>`;
                return; // Hentikan eksekusi lebih lanjut
            }

            // Fungsi helper untuk format Rupiah
            const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

            let html = `
                <div class="text-sm text-gray-300 mb-4 p-3 bg-[#0D1117] rounded-lg border border-[#2D3139]">
                    <div class="grid grid-cols-3 gap-4">
                        <div><span class="font-bold text-gray-500 text-xs block">Total Stok</span> <span class="text-lg font-extrabold text-[#00BFA5]">${parseFloat(data.bahan.stok)} ${data.bahan.satuan}</span></div>
                        <div><span class="font-bold text-gray-500 text-xs block">Total Nilai Aset</span> <span class="text-lg font-extrabold text-white">Rp ${formatRupiah(data.total_asset_value)}</span></div>
                        <div><span class="font-bold text-gray-500 text-xs block">Jumlah Layer</span> <span class="text-lg font-extrabold text-white">${data.layers.length}</span></div>
                    </div>
                </div>
            `;

            if (data.layers && data.layers.length > 0) {
                html += `
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs text-gray-400 uppercase bg-[#0D1117]">
                            <tr>
                                <th scope="col" class="px-4 py-3 rounded-l-lg">Tgl. Masuk</th>
                                <th scope="col" class="px-4 py-3">No. Nota</th>
                                <th scope="col" class="px-4 py-3">Stok Sisa</th>
                                <th scope="col" class="px-4 py-3 rounded-r-lg text-right">Harga Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.layers.forEach(layer => {
                    html += `
                        <tr class="border-b border-[#2D3139] hover:bg-[#20232A]">
                            <td class="px-4 py-3 font-medium text-white">${layer.tanggal_masuk}</td>
                            <td class="px-4 py-3 text-gray-400">${layer.no_nota || '-'}</td>
                            <td class="px-4 py-3 font-bold text-[#FD730D]">${layer.stok_sisa}</td>
                            <td class="px-4 py-3 text-right">Rp ${formatRupiah(layer.harga_satuan)}</td>
                        </tr>
                    `;
                });
                html += '</tbody></table>';
            } else {
                html += '<div class="text-center py-8 mt-4 border-t border-dashed border-gray-700"><i class="fa-solid fa-box-open text-2xl text-gray-500"></i><p class="mt-2 text-sm text-gray-400">Belum ada catatan stok masuk untuk material ini.</p></div>';
            }
            
            contentDiv.innerHTML = html;

            // Tambahkan tombol Hapus di footer modal
            const footer = document.createElement('div');
            footer.className = 'mt-4 pt-4 border-t border-gray-700 flex justify-end';
            footer.innerHTML = `<button onclick="confirmDelete(${data.bahan.id}, '${data.bahan.nama_material}')" class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white font-bold py-2 px-4 rounded-lg text-xs border border-red-500/20">
                                    <i class="fa-solid fa-trash mr-2"></i>Hapus Material Ini
                                </button>`;
            contentDiv.appendChild(footer);
        })
        .catch(error => {
            console.error('Error fetching stock layers:', error);
            contentDiv.innerHTML = '<div class="text-center py-8"><i class="fa-solid fa-circle-exclamation text-2xl text-red-500"></i><p class="mt-2 text-sm text-red-400">Gagal memuat data. Silakan coba lagi.</p></div>';
        });
}
</script><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/bahan/_stock-layers-modal.blade.php ENDPATH**/ ?>