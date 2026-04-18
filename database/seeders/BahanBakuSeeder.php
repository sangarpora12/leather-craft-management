<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil prefix biar kodenya gak berantakan
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';

        // 2. Daftar 20 bahan baku "Sangar" buat Berliano Leather
        $data = [
            ['nama' => 'Kulit Sapi Pull-up Brown', 'kat' => 'Kulit', 'sat' => 'sqft', 'harga' => 35000, 'stok' => 50, 'min' => 10],
            ['nama' => 'Kulit Sapi Crazy Horse Tan', 'kat' => 'Kulit', 'sat' => 'sqft', 'harga' => 42000, 'stok' => 5, 'min' => 10], // Kritis
            ['nama' => 'Kulit Sapi Nappa Black', 'kat' => 'Kulit', 'sat' => 'sqft', 'harga' => 38000, 'stok' => 25, 'min' => 10],
            ['nama' => 'Kulit Kambing Suede Grey', 'kat' => 'Kulit', 'sat' => 'sqft', 'harga' => 22000, 'stok' => 40, 'min' => 10],
            ['nama' => 'Kulit Sapi Vegtan Natural', 'kat' => 'Kulit', 'sat' => 'sqft', 'harga' => 45000, 'stok' => 0, 'min' => 5], // Habis
            ['nama' => 'Benang Polyester Waxed 0.8mm', 'kat' => 'Aksesoris', 'sat' => 'roll', 'harga' => 15000, 'stok' => 12, 'min' => 3],
            ['nama' => 'Lem Kuning Super', 'kat' => 'Bahan Pembantu', 'sat' => 'kg', 'harga' => 55000, 'stok' => 8, 'min' => 2],
            ['nama' => 'Cat Pinggir (Edge Paint) Black', 'kat' => 'Bahan Pembantu', 'sat' => 'botol', 'harga' => 25000, 'stok' => 1, 'min' => 3], // Kritis
            ['nama' => 'Tokonole Finish Agent', 'kat' => 'Bahan Pembantu', 'sat' => 'botol', 'harga' => 120000, 'stok' => 4, 'min' => 2],
            ['nama' => 'Resleting YKK Metal No. 5', 'kat' => 'Aksesoris', 'sat' => 'meter', 'harga' => 18000, 'stok' => 100, 'min' => 10],
            ['nama' => 'Kepala Resleting Gold No. 5', 'kat' => 'Aksesoris', 'sat' => 'pcs', 'harga' => 5000, 'stok' => 50, 'min' => 20],
            ['nama' => 'Ring D 2cm Solid Brass', 'kat' => 'Hardware', 'sat' => 'pcs', 'harga' => 7500, 'stok' => 2, 'min' => 15], // Kritis
            ['nama' => 'Buckle Sabuk 3.5cm Silver', 'kat' => 'Hardware', 'sat' => 'pcs', 'harga' => 12500, 'stok' => 30, 'min' => 5],
            ['nama' => 'Paku Keling (Rivet) 7mm', 'kat' => 'Hardware', 'sat' => 'pack', 'harga' => 25000, 'stok' => 10, 'min' => 2],
            ['nama' => 'Magnet Dompet 14mm Chrome', 'kat' => 'Hardware', 'sat' => 'pcs', 'harga' => 3500, 'stok' => 80, 'min' => 20],
            ['nama' => 'Kain Furing Drill High Quality', 'kat' => 'Kain', 'sat' => 'yard', 'harga' => 28000, 'stok' => 15, 'min' => 5],
            ['nama' => 'Jarum Jahit Kulit Manual', 'kat' => 'Alat', 'sat' => 'set', 'harga' => 12000, 'stok' => 20, 'min' => 5],
            ['nama' => 'Box Packaging Berliano Large', 'kat' => 'Kemasan', 'sat' => 'pcs', 'harga' => 15000, 'stok' => 100, 'min' => 10],
            ['nama' => 'Dustbag Kain Putih Soft', 'kat' => 'Kemasan', 'sat' => 'pcs', 'harga' => 8000, 'stok' => 4, 'min' => 20], // Kritis
            ['nama' => 'Kartu Garansi & Thank You Card', 'kat' => 'Kemasan', 'sat' => 'pcs', 'harga' => 500, 'stok' => 500, 'min' => 50],
        ];

        foreach ($data as $index => $item) {
            $kode = $prefix . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            
            BahanBaku::updateOrCreate(
                ['nama_material' => $item['nama']],
                [
                    'kode_material' => $kode,
                    'kategori'      => $item['kat'],
                    'satuan'        => $item['sat'],
                    'harga_satuan'  => $item['harga'],
                    'stok'          => $item['stok'],
                    'stok_minimum'  => $item['min'],
                    'supplier'      => 'Supplier Utama Jogja',
                ]
            );
        }
    }
}