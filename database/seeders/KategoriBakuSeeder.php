<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriBakuSeeder extends Seeder
{
    public function run()
    {
        // Sesuaikan 'nama_kategori' dengan hasil pengecekan Schema tadi
        $categories = [
            ['nama_kategori' => 'Kulit', 'tipe' => 'bahan'],
            ['nama_kategori' => 'Kain & Pelapis', 'tipe' => 'bahan'],
            ['nama_kategori' => 'Benang', 'tipe' => 'bahan'],
            ['nama_kategori' => 'Hardware & Aksesoris', 'tipe' => 'bahan'],
            ['nama_kategori' => 'Kimia & Perawatan', 'tipe' => 'bahan'],
            ['nama_kategori' => 'Lain-lain', 'tipe' => 'bahan'],
        ];

        foreach ($categories as $cat) {
            Kategori::create($cat);
        }
    }
}