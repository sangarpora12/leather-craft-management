<?php $__env->startSection('page_title', 'Bahan Baku'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="space-y-6 animate-fade-in font-sans">

    
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-2">
        <div>
            <p class="text-xs text-gray-500">Kelola database material, stok awal, dan batas reorder point.</p>
        </div>
        
        <div class="flex gap-3">
            
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#2D3139] shadow-xl flex justify-between items-center">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">Total SKU Material</p>
                <h3 class="text-2xl font-extrabold text-white"><?php echo e($bahans->total()); ?> <span class="text-sm font-normal text-gray-500">Item</span></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#2D3139] flex items-center justify-center">
                <i class="fa-solid fa-boxes-stacked text-white text-xl"></i>
            </div>
        </div>

        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#2D3139] shadow-xl flex justify-between items-center">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">Nilai Aset Material</p>
                <h3 class="text-2xl font-extrabold text-[#00BFA5] whitespace-nowrap">Rp <?php echo e(number_format($totalAsetBahan ?? 0, 0, ',', '.')); ?></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#2D3139] border border-[#2D3139] flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-white text-xl"></i>
            </div>
        </div>

        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#FF5252]/30 shadow-xl flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-[#FF5252]/5 rounded-bl-full"></div>
            <div class="relative z-10">
                <p class="text-[#FF5252] text-[10px] font-bold uppercase tracking-widest mb-2">Stok Kritis / Habis</p>
                <h3 class="text-2xl font-extrabold text-[#FF5252]"><?php echo e($totalKritis ?? 0); ?> <span class="text-sm font-normal text-red-900/50">Item</span></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#FF5252]/10 border border-[#FF5252]/20 flex items-center justify-center relative z-10">
                <i class="fa-solid fa-clipboard-check text-[#FF5252] text-xl"></i>
            </div>
        </div>
    </div>

    
    <form action="<?php echo e(route('admin.bahan.index')); ?>" method="GET" class="flex flex-col md:flex-row gap-3 mb-2">
        
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 md:w-64 shadow-lg">
            <i class="fa-solid fa-search text-gray-500 text-sm"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Kode, Nama..." class="bg-transparent border-none outline-none text-xs text-white placeholder-gray-500 ml-3 w-full">
        </div>

        
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 shadow-lg">
            <i class="fa-solid fa-folder text-gray-400 text-xs mr-2"></i>
            <select name="kategori" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-xs font-bold text-gray-300 w-32 cursor-pointer appearance-none">
                <option value="" class="bg-[#1A1C24]">Semua Kategori</option>
                <?php $__empty_1 = true; $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <option value="<?php echo e($kategori->nama_kategori); ?>" class="bg-[#1A1C24]" <?php echo e(request('kategori') == $kategori->nama_kategori ? 'selected' : ''); ?>><?php echo e($kategori->nama_kategori); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <option value="" disabled class="bg-[#1A1C24] text-gray-500">Data kategori kosong</option>
                <?php endif; ?>
            </select>
            <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
        </div>

        
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 shadow-lg">
            <i class="fa-solid fa-layer-group text-gray-400 text-xs mr-2"></i>
            <select name="status_stok" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-xs font-bold text-gray-300 w-32 cursor-pointer appearance-none">
                <option value="" class="bg-[#1A1C24]">Semua Status</option>
                <option value="aman" class="bg-[#1A1C24]" <?php echo e(request('status_stok') == 'aman' ? 'selected' : ''); ?>>Stok Aman</option>
                <option value="kritis" class="bg-[#1A1C24]" <?php echo e(request('status_stok') == 'kritis' ? 'selected' : ''); ?>>Stok Kritis</option>
                <option value="habis" class="bg-[#1A1C24]" <?php echo e(request('status_stok') == 'habis' ? 'selected' : ''); ?>>Habis</option>
            </select>
            <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
        </div>

        
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 shadow-lg">
            <i class="fa-solid fa-arrow-down-wide-short text-gray-400 text-xs mr-2"></i>
            <select name="sort_by" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-xs font-bold text-gray-300 w-36 cursor-pointer appearance-none">
                <option value="terbaru" class="bg-[#1A1C24]" <?php echo e(request('sort_by') == 'terbaru' || !request('sort_by') ? 'selected' : ''); ?>>Terbaru</option>
                <option value="stok_terbanyak" class="bg-[#1A1C24]" <?php echo e(request('sort_by') == 'stok_terbanyak' ? 'selected' : ''); ?>>Stok Terbanyak</option>
                <option value="stok_sedikit" class="bg-[#1A1C24]" <?php echo e(request('sort_by') == 'stok_sedikit' ? 'selected' : ''); ?>>Stok Sedikit</option>
                <option value="harga_termahal" class="bg-[#1A1C24]" <?php echo e(request('sort_by') == 'harga_termahal' ? 'selected' : ''); ?>>Harga Termahal</option>
                <option value="harga_termurah" class="bg-[#1A1C24]" <?php echo e(request('sort_by') == 'harga_termurah' ? 'selected' : ''); ?>>Harga Termurah</option>
            </select>
            <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
        </div>

        
        <?php if(request('search') || request('kategori') || request('status_stok') || (request('sort_by') && request('sort_by') != 'terbaru')): ?>
            <a href="<?php echo e(route('admin.bahan.index')); ?>" class="bg-[#2D3139] hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-lg flex items-center transition">
                <i class="fa-solid fa-rotate-left mr-2"></i>Reset
            </a>
        <?php endif; ?>
    </form>

    
    <div class="bg-[#1A1C24] rounded-2xl border border-[#2D3139] shadow-xl overflow-hidden">
        
        <div class="px-6 py-5 border-b border-[#2D3139] flex justify-between items-center bg-[#1A1C24]">
            <h2 class="text-sm font-bold text-white tracking-wide">Daftar Bahan Baku</h2>
            <span class="text-[11px] text-gray-500 font-medium">Total: <?php echo e($bahans->total()); ?> Item</span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="text-[10px] text-gray-500 uppercase tracking-widest border-b border-[#2D3139] bg-[#0D1117]">
                        <th class="px-6 py-4 font-bold">Foto</th>
                        <th class="px-6 py-4 font-bold">Kode Bahan</th>
                        <th class="px-6 py-4 font-bold">Nama Material</th>
                        <th class="px-6 py-4 font-bold text-center">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-bold text-center">Satuan (Unit)</th>
                        <th class="px-6 py-4 font-bold text-center">Min. Stok</th>
                        <th class="px-6 py-4 font-bold text-right">Harga Beli</th>
                        <th class="px-6 py-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139] text-sm">
                    <?php $__empty_1 = true; $__currentLoopData = $bahans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        // Cek status kritis
                        // IKUTI PENGATURAN GUDANG: stok_minimum_global adalah acuan utama
                        // (fallback 5 bila belum diset)
                        $batasStokItem = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);
                        $isKritis = ((float) $b->stok) <= $batasStokItem;
                    ?>
                    <tr class="hover:bg-[#20232A]/80 transition-colors <?php echo e($isKritis ? 'bg-[#FF5252]/5' : ''); ?>">
                        
                        <td class="px-6 py-4">
                            <?php if($b->foto): ?>
                                <img src="<?php echo e(asset('storage/' . $b->foto)); ?>" alt="Foto <?php echo e($b->nama_material); ?>" class="w-12 h-12 rounded-lg object-cover border border-[#2D3139]">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-lg bg-[#2D3139] flex items-center justify-center border border-gray-700/50">
                                    <i class="fa-solid fa-image text-gray-500 text-sm"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="text-[#FD730D] font-extrabold text-xs tracking-wide"><?php echo e($b->kode_material); ?></span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-white"><?php echo e($b->nama_material); ?></div>
                            <div class="text-[10px] text-gray-500 mt-1 font-medium">Kategori: <?php echo e($b->kategori); ?></div>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="<?php echo e($isKritis ? 'text-[#FF5252]' : 'text-[#00BFA5]'); ?> font-extrabold text-base"><?php echo e((float) $b->stok); ?></span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block bg-[#2D3139] text-gray-300 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-gray-700/50"><?php echo e($b->satuan); ?></span>
                        </td>
                        
                        <td class="px-6 py-4 text-center text-gray-500 text-xs font-semibold">
                            <?php echo e($batasStokItem); ?>

                        </td>
                        
                        <td class="px-6 py-4 text-right text-gray-300 text-xs font-semibold">
                            Rp <?php echo e(number_format($b->harga_satuan, 0, ',', '.')); ?>

                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='openEditModal(<?php echo json_encode($b, 15, 512) ?>)' class="w-8 h-8 rounded-lg bg-[#2D3139] text-gray-400 hover:text-white hover:bg-gray-600 transition border border-gray-700/50 flex items-center justify-center" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button onclick="viewStockLayers(<?php echo e($b->id); ?>, '<?php echo e($b->nama_material); ?>')" class="w-8 h-8 rounded-lg bg-[#2D3139] text-gray-400 hover:text-white hover:bg-gray-600 transition border border-gray-700/50 flex items-center justify-center" title="View Detail / Layer">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-16 text-gray-500 text-xs italic border-dashed border-t border-[#2D3139]">
                            Data material belum tersedia.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-[#2D3139] bg-[#1A1C24] text-xs text-gray-500">
            <?php echo e($bahans->links()); ?>

        </div>
    </div>
</div>

<?php echo $__env->make('admin.bahan._stock-layers-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div id="modalTambah" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalTambah')"></div>
    <div style="position: relative; background: #1A1C24; width: 100%; max-width: 32rem; border-radius: 1.5rem; border: 1px solid #2D3139; padding: 1.5rem; margin: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: white;">Input Stok Masuk</h2>
                <p style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; mt-1">Catat bahan baku baru dari supplier</p>
            </div>
            <button onclick="closeModal('modalTambah')" style="color: #9ca3af; background: #2D3139; width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 1px solid #374151; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="<?php echo e(route('admin.bahan.store')); ?>" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <?php echo csrf_field(); ?>
            
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Kode Material</label>
                <input type="text" id="kode_material" name="kode_material" readonly placeholder="Otomatis terisi saat pilih bahan..." style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: #9ca3af; font-size: 0.875rem;">
            </div>

            <div style="position: relative;">
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Nama Bahan</label>
                <input type="text" id="nama_material" name="nama_material" placeholder="Ketik nama bahan..." autocomplete="off" required style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                <div id="suggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: #1A1C24; border: 1px solid #2D3139; border-top: none; border-radius: 0 0 0.75rem 0.75rem; max-height: 10rem; overflow-y: auto; z-index: 10; display: none;"></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Supplier</label>
                    <input type="text" id="supplier" name="supplier" placeholder="Contoh: CV. Kulit Jaya" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Ref / Nota</label>
                    <input type="text" id="ref_no" name="ref_no" placeholder="No Nota / Barcode" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Kategori</label>
                    <select id="kategori" name="kategori" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php $__empty_1 = true; $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($kategori->nama_kategori); ?>"><?php echo e($kategori->nama_kategori); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option disabled>Data kategori belum ada</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Satuan</label>
                    <select id="satuan" name="satuan" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="">-- Pilih Satuan --</option>
                        <?php $__empty_1 = true; $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <option value="<?php echo e($satuan->nama_satuan); ?>"><?php echo e($satuan->nama_satuan); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <option disabled>Data satuan belum ada</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Jumlah Masuk</label>
                    <input type="number" id="stok" name="stok" placeholder="0" required style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem; color: white; font-size: 0.875rem; font-weight: bold; text-align: center;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Harga Beli (Rp)</label>
                    <input type="number" id="harga_satuan" name="harga_satuan" placeholder="Rp" required style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem; color: white; font-size: 0.875rem; font-weight: bold; text-align: center;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Foto Bahan <span class="text-gray-500">(Opsional)</span></label>
                <input type="file" name="foto" accept="image/*" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                <p style="font-size: 0.625rem; color: #6b7280; margin-top: 0.35rem;">Max 20 MB</p>
            </div>

            <button type="submit" style="width: 100%; background: #FD730D; color: white; font-weight: bold; padding: 1rem; border-radius: 1rem; font-size: 0.875rem; margin-top: 0.5rem; border:none; cursor:pointer;">
                Simpan Data
            </button>
        </form>
    </div>
</div>

<div id="modalEdit" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalEdit')"></div>
    <div style="position: relative; background: #1A1C24; width: 100%; max-width: 30rem; border-radius: 1.5rem; border: 1px solid #2D3139; padding: 1rem; margin: 1rem; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: white;">Edit Material</h2>
                <p style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; mt-1">Ubah data bahan baku</p>
            </div>
            <button onclick="closeModal('modalEdit')" style="color: #9ca3af; background: #2D3139; width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 1px solid #374151; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form id="formEdit" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Nama Bahan</label>
                <input type="text" id="edit_nama_material" name="nama_material" required style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Supplier</label>
                    <input type="text" id="edit_supplier" name="supplier" placeholder="Contoh: CV. Kulit Jaya" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Ref / Nota</label>
                    <input type="text" id="edit_ref_no" name="ref_no" placeholder="No Nota / Barcode" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Kategori</label>
                    <select id="edit_kategori" name="kategori" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="Bahan Utama">Bahan Utama</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Kimia">Kimia</option>
                        <option value="Packaging">Packaging</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Satuan</label>
                    <select id="edit_satuan" name="satuan" required style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="">-- Pilih Satuan --</option>
                        <?php $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($satuan->nama_satuan); ?>"><?php echo e($satuan->nama_satuan); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Jumlah</label>
                    <input type="number" step="any" id="edit_stok" name="stok" readonly onmousedown="return false;" style="width: 100%; background: #20232A; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem; color: #9ca3af; font-size: 0.875rem; font-weight: bold; text-align: center; cursor: not-allowed;">
                    <p style="font-size: 0.625rem; color: #6b7280; margin-top: 0.35rem;">Stok dikelola sistem FIFO</p>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Harga Beli (Rp)</label>
                    <input type="number" id="edit_harga_satuan" name="harga_satuan" readonly onmousedown="return false;" style="width: 100%; background: #20232A; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem; color: #9ca3af; font-size: 0.875rem; font-weight: bold; text-align: center; cursor: not-allowed;">
                    <p style="font-size: 0.625rem; color: #6b7280; margin-top: 0.35rem;">Ubah melalui Pengadaan</p>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Foto Bahan Saat Ini</label>
                <div id="edit_foto_preview" class="mb-3"></div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Upload Foto Baru <span class="text-gray-500">(Opsional)</span></label>
                <input type="file" name="foto" accept="image/*" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                <p style="font-size: 0.625rem; color: #6b7280; margin-top: 0.35rem;">Biarkan kosong jika tidak ingin mengubah foto. Max 20 MB</p>
            </div>

            <button type="submit" style="width: 100%; background: #FD730D; color: white; font-weight: bold; padding: 1rem; border-radius: 1rem; font-size: 0.875rem; margin-top: 0.5rem; border:none; cursor:pointer;">
                Update Data
            </button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function openEditModal(data) {
    openModal('modalEdit');
    document.getElementById('formEdit').action = "/admin/bahan/" + data.id;
    document.getElementById('edit_nama_material').value = data.nama_material;
    document.getElementById('edit_supplier').value = data.supplier || '';
    document.getElementById('edit_ref_no').value = data.ref_no || '';
    document.getElementById('edit_kategori').value = data.kategori || 'Bahan Utama';
    document.getElementById('edit_satuan').value = data.satuan || 'Pcs';
    document.getElementById('edit_stok').value = data.stok;
    document.getElementById('edit_harga_satuan').value = data.harga_satuan;

    // Tampilkan preview foto lama
    const fotoPreview = document.getElementById('edit_foto_preview');
    if(data.foto) {
        fotoPreview.innerHTML = '<img src="/storage/' + data.foto + '" class="w-20 h-20 rounded-lg object-cover border border-[#2D3139]">';
    } else {
        fotoPreview.innerHTML = '<div class="w-20 h-20 rounded-lg bg-[#2D3139] flex items-center justify-center border border-gray-700/50"><i class="fa-solid fa-image text-gray-500"></i></div>';
    }
}

// AUTOCOMPLETE FUNCTIONALITY (TIDAK DISENTUH SAMA SEKALI)
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('nama_material');
    var suggestions = document.getElementById('suggestions');
    var timeout;

    input.addEventListener('input', function() {
        var query = this.value.trim();
        if (query.length < 1) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            return;
        }

        clearTimeout(timeout);
        timeout = setTimeout(function() {
            fetch('<?php echo e(route("api.bahan.search")); ?>?q=' + encodeURIComponent(query), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(function(item) {
                        var itemDiv = document.createElement('div');
                        itemDiv.style.padding = '0.75rem 1rem';
                        itemDiv.style.cursor = 'pointer';
                        itemDiv.style.color = 'white';
                        itemDiv.style.fontSize = '0.875rem';
                        itemDiv.style.borderBottom = '1px solid #374151';
                        itemDiv.style.textAlign = 'left';
                        // Tambahkan foto thumbnail pada autocomplete
                        let fotoHTML = '';
                        if(item.foto) {
                            fotoHTML = '<img src="/storage/' + item.foto + '" class="w-8 h-8 rounded-lg object-cover mr-3 flex-shrink-0">';
                        } else {
                            fotoHTML = '<div class="w-8 h-8 rounded-lg bg-[#2D3139] flex items-center justify-center mr-3 flex-shrink-0"><i class="fa-solid fa-image text-gray-500 text-xs"></i></div>';
                        }
                        itemDiv.style.display = 'flex';
                        itemDiv.style.alignItems = 'center';
                        itemDiv.innerHTML = fotoHTML + '<div class="flex-1">' + item.nama_material + ' <span style="color: #9ca3af; font-size: 0.75rem;">(' + item.kode_material + ')</span></div>';
                        
                        // Simpan data dalam dataset
                        itemDiv.dataset.kode = item.kode_material;
                        itemDiv.dataset.nama = item.nama_material;
                        itemDiv.dataset.kategori = item.kategori || '';
                        itemDiv.dataset.satuan = item.satuan || '';
                        itemDiv.dataset.supplier = item.supplier || '';
                        itemDiv.dataset.refNo = item.ref_no || '';
                        
                        itemDiv.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#374151';
                        });
                        itemDiv.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = 'transparent';
                        });
                        itemDiv.addEventListener('click', function() {
                            // Isi field otomatis
                            document.getElementById('kode_material').value = this.dataset.kode;
                            document.getElementById('nama_material').value = this.dataset.nama;
                            document.getElementById('supplier').value = this.dataset.supplier;
                            document.getElementById('ref_no').value = this.dataset.refNo;
                            
                            // Set kategori
                            var kategoriSelect = document.getElementById('kategori');
                            var foundKategori = false;
                            for(var i = 0; i < kategoriSelect.options.length; i++) {
                                if(kategoriSelect.options[i].value === this.dataset.kategori) {
                                    kategoriSelect.selectedIndex = i;
                                    foundKategori = true;
                                    break;
                                }
                            }
                            if(!foundKategori) kategoriSelect.selectedIndex = 0;
                            
                            // Set satuan
                            var satuanSelect = document.getElementById('satuan');
                            var foundSatuan = false;
                            for(var i = 0; i < satuanSelect.options.length; i++) {
                                if(satuanSelect.options[i].value === this.dataset.satuan) {
                                    satuanSelect.selectedIndex = i;
                                    foundSatuan = true;
                                    break;
                                }
                            }
                            if(!foundSatuan) satuanSelect.selectedIndex = 0;
                            
                            // KOSONGKAN stok dan harga - user harus isi sendiri
                            document.getElementById('stok').value = '';
                            document.getElementById('harga_satuan').value = '';
                            
                            // Sembunyikan suggestions
                            suggestions.style.display = 'none';
                            suggestions.innerHTML = '';
                        });
                        suggestions.appendChild(itemDiv);
                    });
                    suggestions.style.display = 'block';
                } else {
                    suggestions.style.display = 'none';
                }
            })
            .catch(error => {
                console.log('Fetch error:', error);
            });
        }, 300);
    });

    // Tutup suggestions saat klik di luar
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
    
    // Reset form saat modal dibuka
    document.querySelector('button[onclick="openModal(\'modalTambah\')"]').addEventListener('click', function() {
        document.getElementById('kode_material').value = '';
        document.getElementById('nama_material').value = '';
        document.getElementById('supplier').value = '';
        document.getElementById('ref_no').value = '';
        document.getElementById('kategori').selectedIndex = 0;
        document.getElementById('satuan').selectedIndex = 0;
        document.getElementById('stok').value = '';
        document.getElementById('harga_satuan').value = '';
        document.getElementById('suggestions').style.display = 'none';
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/bahan/index.blade.php ENDPATH**/ ?>