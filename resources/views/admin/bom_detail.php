@php
$page = 'bom'; // Penanda halaman aktif

// Simulasi Data dari Database
$product = [
    'name' => 'Rome Bag - Coklat',
    'sku' => 'PRO-T-001',
    'image' => 'bag.jpg' 
];

// Data Dummy Bahan Baku Awal
$materials = [
    ['name' => 'Kulit Sapi Grade A', 'code' => 'BAH-K-001', 'qty' => 4.5, 'unit' => 'Sq Feet', 'waste' => 5, 'price_total' => 180000],
    ['name' => 'Resleting YKK 30cm', 'code' => 'BAH-A-005', 'qty' => 1, 'unit' => 'Pcs', 'waste' => 0, 'price_total' => 15000]
];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOM Detail - <?= $product['name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="bg-black text-white flex h-screen overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto md:ml-64 p-8 bg-black">
        <div class="flex justify-between items-end mb-8">
            <div>
                <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">BOM List / Edit Resep</p>
                <h2 class="text-2xl font-bold">Kelola Resep: <?= $product['name'] ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="md:col-span-2 bg-[#1A1A1A] rounded-xl p-6 border border-white/10 flex items-center gap-4">
                <div class="w-16 h-16 bg-[#222] rounded-lg flex items-center justify-center text-xs text-gray-400">IMG</div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg"><?= $product['name'] ?></h3>
                    <p class="text-gray-400 text-sm">SKU: <?= $product['sku'] ?></p>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl p-6 border border-white/10 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-0 top-0 p-10 bg-orange-500 opacity-5 rounded-full blur-xl"></div>
                <p class="text-gray-400 text-sm mb-1">Total Estimasi HPP (Material)</p>
                <h3 class="text-3xl font-bold text-green-400" id="totalHPP">Rp 0</h3>
                <span class="text-xs text-gray-500">/ pcs</span>
            </div>
        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 overflow-hidden mb-8">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <h3 class="font-bold">Komponen Bahan Baku</h3>
                <span class="text-xs text-gray-400"><?= count($materials) ?> Komponen Terdaftar</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="p-4">Nama Material</th>
                            <th class="p-4">Kuantitas</th>
                            <th class="p-4">Satuan</th>
                            <th class="p-4">Waste (%)</th>
                            <th class="p-4 text-right">Biaya</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5" id="materialTableBody">
                        <?php foreach($materials as $mat): ?>
                        <tr>
                            <td class="p-4">
                                <div class="font-medium"><?= $mat['name'] ?></div>
                                <div class="text-xs text-gray-400"><?= $mat['code'] ?></div>
                            </td>
                            <td class="p-4"><?= $mat['qty'] ?></td>
                            <td class="p-4"><?= $mat['unit'] ?></td>
                            <td class="p-4 text-yellow-500"><?= $mat['waste'] > 0 ? $mat['waste'].'%' : '-' ?></td>
                            <td class="p-4 text-right font-medium cost-value" data-val="<?= $mat['price_total'] ?>">Rp <?= number_format($mat['price_total'], 0, ',', '.') ?></td>
                            <td class="p-4 text-center">
                                <button class="text-red-400 hover:text-red-300" onclick="deleteRow(this)">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 border-dashed p-6">
            <h4 class="font-bold text-sm mb-4">Tambah Komponen Baru</h4>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <label class="block text-xs text-gray-400 mb-2">Pilih Material</label>
                    <select class="w-full bg-black border border-white/10 rounded px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                        <option>Kategori</option>
                        <option>Benang</option>
                        <option>Lem</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs text-gray-400 mb-2">Kuantitas</label>
                    <input type="number" placeholder="0.0" class="w-full bg-black border border-white/10 rounded px-4 py-3 text-sm text-white focus:outline-none focus:border-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-gray-400 mb-2">Waste</label>
                    <input type="text" value="0%" class="w-full bg-black border border-white/10 rounded px-4 py-3 text-sm text-white focus:outline-none">
                </div>
                <div class="md:col-span-2">
                    <button class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded transition shadow-lg shadow-orange-500/20">Tambahkan</button>
                </div>
            </div>
        </div>
        
        <div class="h-20"></div>
    </main>

    <script>
        // Script sederhana untuk hitung total (Client Side)
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.cost-value').forEach(el => {
                total += parseInt(el.getAttribute('data-val'));
            });
            document.getElementById('totalHPP').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }
        
        function deleteRow(btn) {
            btn.closest('tr').remove();
            calculateTotal();
        }

        calculateTotal();
    </script>
</body>
</html>