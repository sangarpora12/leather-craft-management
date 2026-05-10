<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use App\Models\LogStok;
use App\Models\User;
use Illuminate\Database\Seeder;

class BahanBakuVarianSeeder extends Seeder
{
    public function run(): void
    {
        $operatorNames = ['Budi', 'Siti', 'Ahmad', 'Rina', 'Doni', 'Maya', 'Rudi', 'Dewi'];
        $operatorIds = User::where('role', 'operator')->pluck('id')->toArray();

        $bahanUtama = BahanBaku::whereIn('nama_material', [
            'Kulit Sapi Premium', 'Kulit Kambing Import', 'Kain Lining Sutra',
            'Kain Denim Tebal', 'Benang Jahit Nilon',
        ])->get()->keyBy('nama_material');

        $varianList = [
            'Kulit Sapi Premium'   => ['Merah', 'Hitam', 'Putih'],
            'Kulit Kambing Import' => ['Cokelat Muda', 'Abu-abu', 'Tan'],
            'Kain Lining Sutra'    => ['Biru Dongker', 'Krem', 'Pink'],
            'Kain Denim Tebal'     => ['Biru', 'Hijau', 'Cokelat Tua'],
            'Benang Jahit Nilon'   => ['Putih', 'Hitam', 'Kuning'],
        ];

        echo "Menambahkan varian warna untuk 5 bahan...\n";
        $totalVarian = 0;

        foreach ($varianList as $namaMaterial => $warnaList) {
            if (!isset($bahanUtama[$namaMaterial])) {
                echo "  [!] '{$namaMaterial}' tidak ditemukan, skip.\n";
                continue;
            }

            $master = $bahanUtama[$namaMaterial];
            
            foreach ($warnaList as $warna) {
                $exists = BahanBaku::where('kode_material', $master->kode_material)
                    ->whereRaw('LOWER(warna) = LOWER(?)', [$warna])
                    ->exists();

                if ($exists) {
                    echo "  [-] {$master->kode_material} - {$namaMaterial} ({$warna}) sudah ada\n";
                    continue;
                }

                $stok = rand(3, 30);
                $harga = round($master->harga_satuan * (1 + (rand(-10, 10) / 100)) / 1000) * 1000;

                $bahan = BahanBaku::create([
                    'kode_material' => $master->kode_material,
                    'nama_material' => $namaMaterial,
                    'warna'         => $warna,
                    'stok'          => $stok,
                    'satuan'        => $master->satuan,
                    'kategori'      => $master->kategori,
                    'harga_satuan'  => $harga,
                    'is_inventory'  => true,
                ]);

                BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => now()->subDays(rand(1, 30))->toDateString(),
                    'qty_masuk'     => $stok,
                    'qty_sisa'      => $stok,
                    'harga_beli'    => $harga,
                    'sumber_harga'  => 'Seeder Varian Warna',
                ]);

                $opName = $operatorNames[array_rand($operatorNames)];
                $opId = !empty($operatorIds) ? $operatorIds[array_rand($operatorIds)] : null;

                LogStok::create([
                    'bahan_baku_id'  => $bahan->id,
                    'operator_id'    => $opId,
                    'jenis_transaksi' => 'TAMBAH_STOK',
                    'jumlah'          => $stok,
                    'keterangan'      => 'Seeder - ' . $opName,
                    'created_at'      => now()->subDays(rand(1, 30)),
                    'updated_at'      => now()->subDays(rand(1, 30)),
                ]);

                $totalVarian++;
                echo "  [+] {$master->kode_material} - {$namaMaterial} ({$warna}) Stok:{$stok} Harga:Rp " . number_format($harga, 0, ',', '.') . "\n";
            }
        }

        echo "\nSelesai! {$totalVarian} varian warna baru ditambahkan.\n";
    }
}