<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Pengaturan;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class BahanBakuMasterSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Menjalankan BahanBakuMasterSeeder...');

        // Hapus data lama yang dibuat oleh seeder ini untuk menghindari duplikat.
        BahanBaku::where('supplier', 'Seeder Generated')->delete();
        $this->command->info('Data lama dari seeder telah dihapus.');

        $faker = Faker::create('id_ID');

        // 1. Ambil Kategori (tipe 'Bahan') & Satuan dari tabel masing-masing
        $kategoriOptions = Kategori::where('tipe', 'Bahan')->pluck('nama_kategori')->toArray();
        $satuanOptions = Satuan::pluck('nama_satuan')->toArray();

        // Fallback jika data dari database kosong
        if (empty($kategoriOptions)) {
            $this->command->warn('Data kategori "Bahan" di tabel "kategoris" kosong. Menggunakan data default.');
            $kategoriOptions = ['Kulit', 'Aksesoris', 'Bahan Pembantu', 'Hardware', 'Kain'];
        }
        if (empty($satuanOptions)) {
            $this->command->warn('Data satuan di tabel "satuans" kosong. Menggunakan data default.');
            $satuanOptions = ['sqft', 'roll', 'kg', 'pcs', 'meter'];
        }
        $this->command->info('Kategori dan Satuan berhasil dimuat.');

        // 2. Generate data bahan baku
        $bahanBaku = [];
        $jumlahData = rand(20, 30);
        
        $namaBahanDasar = ['Kulit Sapi', 'Kulit Kambing', 'Benang', 'Lem', 'Cat', 'Resleting', 'Kain Furing', 'Gesper', 'Kancing'];
        $jenisBahan = ['Pull Up', 'Crazy Horse', 'Nappa', 'Suede', 'Polyester', 'Nilon', 'Super', 'YKK'];

        // --- LOGIKA BARU: Ambil prefix dari Pengaturan ---
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
        $this->command->info("Menggunakan prefix kode dari pengaturan: '{$prefix}'");

        // Cari nomor terakhir berdasarkan prefix yang benar
        $lastBahan = BahanBaku::where('kode_material', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $nextId = 1;
        if ($lastBahan) {
            // Ekstrak nomor dari kode terakhir secara dinamis
            $lastNumber = (int) substr($lastBahan->kode_material, strlen($prefix));
            $nextId = $lastNumber + 1;
        }
        
        $this->command->info("Membuat {$jumlahData} data bahan baku baru, dimulai dari nomor {$nextId}...");

        for ($i = 0; $i < $jumlahData; $i++) {
            $namaBahan = $faker->randomElement($namaBahanDasar) . ' ' . $faker->randomElement($jenisBahan) . ' ' . $faker->colorName;
            $kodeMaterial = $prefix . str_pad($nextId + $i, 4, '0', STR_PAD_LEFT);

            $bahanBaku[] = [
                'kode_material' => $kodeMaterial,
                'nama_material' => $namaBahan,
                'kategori' => $faker->randomElement($kategoriOptions),
                'satuan' => $faker->randomElement($satuanOptions),
                'stok' => $faker->numberBetween(10, 100),
                'stok_minimum' => 5,
                'harga_satuan' => $faker->numberBetween(100, 1000) * 100,
                'supplier' => 'Seeder Generated', // Penanda untuk pembersihan
                'is_inventory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 3. Masukkan data ke database
        BahanBaku::insert($bahanBaku);

        $this->command->info("Seeder selesai. {$jumlahData} bahan baku master berhasil ditambahkan.");
    }
}