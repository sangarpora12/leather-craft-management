<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use App\Models\LogStok;
use App\Models\User;
use App\Models\Pengaturan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahanBakuRandomSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil prefix dari pengaturan
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';

        // Daftar operator
        $operatorNames = ['Budi', 'Siti', 'Ahmad', 'Rina', 'Doni', 'Maya', 'Rudi', 'Dewi'];
        $operatorIds = User::where('role', 'operator')->pluck('id')->toArray();

        // Daftar nama bahan
        $bahanList = [
            ['Kulit Sapi Premium', 'Kulit'],
            ['Kulit Kambing Import', 'Kulit'],
            ['Kain Lining Sutra', 'Kain'],
            ['Kain Denim Tebal', 'Kain'],
            ['Benang Jahit Nilon', 'Benang'],
            ['Benang Poliester', 'Benang'],
            ['Lem Kuning Industri', 'Kimia'],
            ['Lem Super Glue', 'Kimia'],
            ['Resleting Jepang 30cm', 'Aksesoris'],
            ['Resleting Lokal 25cm', 'Aksesoris'],
            ['Kancing Kait Baja', 'Aksesoris'],
            ['Kancing Magnet Kuat', 'Aksesoris'],
            ['Cat Semprot Hitam Doff', 'Kimia'],
            ['Perekat Kain Spray', 'Kimia'],
            ['Busa Pelapis 5mm', 'Bahan'],
            ['Busa Pelapis 10mm', 'Bahan'],
            ['Tali Kur 8mm', 'Aksesoris'],
            ['Tali Kur 5mm', 'Aksesoris'],
            ['Kertas Pola Coklat', 'Bahan'],
            ['Karet Elastis Lebar', 'Bahan'],
        ];

        // Daftar warna
        $warnaList = ['Hitam', 'Cokelat Tua', 'Cokelat Muda', 'Merah', 'Biru Dongker', 'Putih', 'Abu-abu', 'Tan', 'Krem', 'Hijau', 'Kuning', 'Tidak Ada Warna'];

        // Daftar satuan
        $satuanList = ['Lembar', 'Meter', 'Rol', 'Pcs', 'Kg', 'Set', 'Botol', 'Pack'];

        $kodeCounter = 100;

        echo "Seeding 20 bahan baku...\n";

        foreach ($bahanList as $index => $item) {
            $nama = $item[0];
            $kategori = $item[1];
            $kodeCounter++;
            $kodeMaterial = $prefix . $kodeCounter;

            // Pilih warna random
            $warna = $warnaList[array_rand($warnaList)];

            // Harga random (20.000 - 500.000)
            $harga = rand(20000, 500000);

            // Stok random (5 - 100)
            $stok = rand(5, 100);

            // Satuan
            $satuan = $satuanList[array_rand($satuanList)];

            $bahan = BahanBaku::create([
                'kode_material' => $kodeMaterial,
                'nama_material' => $nama,
                'warna'         => $warna,
                'stok'          => $stok,
                'satuan'        => $satuan,
                'kategori'      => $kategori,
                'harga_satuan'  => $harga,
                'is_inventory'  => true,
            ]);

            // Catat stok layer
            BahanStokLayer::create([
                'bahan_baku_id' => $bahan->id,
                'tanggal_masuk' => now()->subDays(rand(1, 30))->toDateString(),
                'qty_masuk'     => $stok,
                'qty_sisa'      => $stok,
                'harga_beli'    => $harga,
                'sumber_harga'  => 'Seeder Data Awal',
            ]);

            // Catat log stok untuk grafik (dengan tanggal random 1-30 hari lalu)
            $hariLalu = rand(1, 30);
            $operatorId = !empty($operatorIds) ? $operatorIds[array_rand($operatorIds)] : null;
            $operatorName = $operatorNames[array_rand($operatorNames)];

            \App\Models\LogStok::create([
                'bahan_baku_id'  => $bahan->id,
                'operator_id'    => $operatorId,
                'jenis_transaksi' => 'TAMBAH_STOK',
                'jumlah'          => $stok,
                'keterangan'      => 'Seeder - ' . $operatorName,
                'created_at'      => now()->subDays($hariLalu),
                'updated_at'      => now()->subDays($hariLalu),
            ]);

            echo "  [OK] {$kodeMaterial} - {$nama} ({$warna}) Stok: {$stok} Harga: Rp " . number_format($harga, 0, ',', '.') . "\n";
        }

        // Update langsung kolom created_at di database untuk memastikan
        $allBahan = BahanBaku::orderBy('id', 'desc')->take(20)->get();
        foreach ($allBahan as $b) {
            $hariLalu = rand(1, 30);
            DB::table('bahan_bakus')->where('id', $b->id)->update([
                'created_at' => now()->subDays($hariLalu),
                'updated_at' => now()->subDays($hariLalu),
            ]);
        }

        echo "\nSelesai! 20 bahan baku berhasil ditambahkan.\n";
    }
}