<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriLengkapSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriBahan = [
            ['nama_kategori' => 'Kulit',          'tipe' => 'bahan'],
            ['nama_kategori' => 'Kain',           'tipe' => 'bahan'],
            ['nama_kategori' => 'Benang',          'tipe' => 'bahan'],
            ['nama_kategori' => 'Kimia',           'tipe' => 'bahan'],
            ['nama_kategori' => 'Aksesoris',       'tipe' => 'bahan'],
            ['nama_kategori' => 'Busa & Peredam',  'tipe' => 'bahan'],
            ['nama_kategori' => 'Packing',          'tipe' => 'bahan'],
            ['nama_kategori' => 'Bahan Pelengkap',  'tipe' => 'bahan'],
        ];

        $kategoriProduk = [
            ['nama_kategori' => 'Tas',             'tipe' => 'produk'],
            ['nama_kategori' => 'Dompet',          'tipe' => 'produk'],
            ['nama_kategori' => 'Sepatu',           'tipe' => 'produk'],
            ['nama_kategori' => 'Ikat Pinggang',    'tipe' => 'produk'],
            ['nama_kategori' => 'Jaket',            'tipe' => 'produk'],
            ['nama_kategori' => 'Aksesoris Fashion','tipe' => 'produk'],
        ];

        echo "Menambahkan kategori bahan...\n";
        foreach ($kategoriBahan as $k) {
            Kategori::updateOrCreate(
                ['nama_kategori' => $k['nama_kategori'], 'tipe' => 'bahan'],
                $k
            );
            echo "  [+] Bahan: {$k['nama_kategori']}\n";
        }

        echo "\nMenambahkan kategori produk...\n";
        foreach ($kategoriProduk as $k) {
            Kategori::updateOrCreate(
                ['nama_kategori' => $k['nama_kategori'], 'tipe' => 'produk'],
                $k
            );
            echo "  [+] Produk: {$k['nama_kategori']}\n";
        }

        echo "\nSelesai! Semua kategori berhasil ditambahkan.\n";
    }
}