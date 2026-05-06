<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        $productCategories = [
            ['nama_kategori' => 'Tas (Bags)', 'tipe' => 'produk'],
            ['nama_kategori' => 'Dompet (Wallets)', 'tipe' => 'produk'],
            ['nama_kategori' => 'Sepatu (Footwear)', 'tipe' => 'produk'],
            ['nama_kategori' => 'Pakaian (Apparel)', 'tipe' => 'produk'],
            ['nama_kategori' => 'Aksesoris (Accessories)', 'tipe' => 'produk'],
            ['nama_kategori' => 'Custom Order', 'tipe' => 'produk'],
        ];

        foreach ($productCategories as $cat) {
            Kategori::create($cat);
        }
    }
}