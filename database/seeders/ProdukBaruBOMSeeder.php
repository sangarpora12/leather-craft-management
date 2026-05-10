<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProdukJadi;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;

class ProdukBaruBOMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Generate Kode Produk Otomatis dari Pengaturan Gudang
     */
    private function generateKodeProduk(): string
    {
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_produk')->value('nilai') ?? 'PROD';

        // Cari kode produk terakhir dengan prefix yang sama
        $lastProduct = ProdukJadi::where('kode_produk', 'LIKE', $prefix . '-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(kode_produk, '-', -1) AS UNSIGNED) DESC")
            ->first();

        $newNumber = 1;
        if ($lastProduct) {
            // Ekstrak nomor dari kode terakhir
            $parts = explode('-', $lastProduct->kode_produk);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Kode Produk Otomatis dengan Increment
     */
    private function generateKodeProdukIncremental(): string
    {
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_produk')->value('nilai') ?? 'PROD';
        
        // Gunakan static variable untuk tracking nomor terakhir
        static $lastNumber = null;
        
        if ($lastNumber === null) {
            // Cari kode produk terakhir dengan prefix yang sama
            $lastProduct = ProdukJadi::where('kode_produk', 'LIKE', $prefix . '-%')
                ->orderByRaw("CAST(SUBSTRING_INDEX(kode_produk, '-', -1) AS UNSIGNED) DESC")
                ->first();
            
            $lastNumber = 0;
            if ($lastProduct) {
                // Ekstrak nomor dari kode terakhir
                $parts = explode('-', $lastProduct->kode_produk);
                $lastNumber = (int) end($parts);
            }
        }
        
        $lastNumber++;
        return $prefix . '-' . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
    }

    public function run(): void
    {
        $produkBaru = [
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Tas Kulit Elegan',
                'kategori' => ['Tas', 'Premium'],
                'warna' => ['Cokelat', 'Hitam'],
                'jenis_motif' => ['Polos', 'Batik'],
                'harga_jual' => 1250000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Tas kulit elegan dengan desain modern dan klasik, cocok untuk berbagai acara',
                'keterangan' => 'Produk baru - belum ada BOM',
                'stok' => 0,
                'stok_minimum' => 5,
                'panjang' => 35.00,
                'lebar' => 12.00,
                'tinggi' => 28.00,
                'detail_pola' => 'Pola tas elegan dengan 2 kompartemen utama, 1 saku depan, dan tali selempang adjustable',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Dompet Kulit Premium',
                'kategori' => ['Dompet', 'Premium'],
                'warna' => ['Merah', 'Navy', 'Cokelat'],
                'jenis_motif' => ['Polos', 'Tekstur'],
                'harga_jual' => 450000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Dompet kulit premium dengan banyak slot kartu dan ruang uang, sangat praktis',
                'keterangan' => 'Produk baru - belum ada BOM',
                'stok' => 0,
                'stok_minimum' => 10,
                'panjang' => 19.00,
                'lebar' => 2.50,
                'tinggi' => 10.00,
                'detail_pola' => 'Dompet lipat dengan 12 slot kartu, 2 ruang uang, dan kantong koin beresleting',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Sandal Kulit Casual',
                'kategori' => ['Sandal', 'Casual'],
                'warna' => ['Hitam', 'Cokelat', 'Krem'],
                'jenis_motif' => ['Polos', 'Tali'],
                'harga_jual' => 380000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Sandal kulit casual nyaman untuk dipakai sehari-hari, desain modern dan stylish',
                'keterangan' => 'Produk baru - belum ada BOM',
                'stok' => 0,
                'stok_minimum' => 8,
                'panjang' => 26.00,
                'lebar' => 10.50,
                'tinggi' => 8.50,
                'detail_pola' => 'Sandal casual dengan sol karet anti-slip, tali kulit adjustable, dan empuk di bagian tumit',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Tas Selempang Kulit',
                'kategori' => ['Tas', 'Casual'],
                'warna' => ['Hitam', 'Biru', 'Hijau'],
                'jenis_motif' => ['Polos', 'Crossbody'],
                'harga_jual' => 680000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Tas selempang kulit praktis untuk kegiatan sehari-hari, ringan dan tahan lama',
                'keterangan' => 'Produk baru - belum ada BOM',
                'stok' => 0,
                'stok_minimum' => 6,
                'panjang' => 28.00,
                'lebar' => 8.00,
                'tinggi' => 32.00,
                'detail_pola' => 'Tas selempang dengan 3 kompartemen, resleting utama, dan tali selempang adjustable',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Ikat Pinggang Kulit',
                'kategori' => ['Aksesoris', 'Premium'],
                'warna' => ['Hitam', 'Cokelat', 'Burgundy'],
                'jenis_motif' => ['Polos', 'Buckle'],
                'harga_jual' => 275000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Ikat pinggang kulit premium dengan buckle berkualitas, cocok untuk formal dan casual',
                'keterangan' => 'Produk baru - belum ada BOM',
                'stok' => 0,
                'stok_minimum' => 12,
                'panjang' => 120.00,
                'lebar' => 3.50,
                'tinggi' => 0.30,
                'detail_pola' => 'Ikat pinggang dengan 7 lubang, buckle stainless steel, dan ujung miring klasik',
                'sketsa' => [],
                'foto' => null,
            ],
        ];

        foreach ($produkBaru as $produk) {
            ProdukJadi::create($produk);
        }

        // Tambahkan 10 produk baru dengan bahan yang sudah ada
        $produkBaruDenganBahan = [
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Tas Ransel Kulit',
                'kategori' => ['Tas', 'Travel'],
                'warna' => ['Hitam', 'Cokelat'],
                'jenis_motif' => ['Polos', 'Backpack'],
                'harga_jual' => 1850000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Tas ransel kulit travel dengan kapasitas besar dan nyaman',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 3,
                'panjang' => 45.00,
                'lebar' => 20.00,
                'tinggi' => 35.00,
                'detail_pola' => 'Tas ransel dengan 2 kompartemen utama, saku laptop, dan tali bahu empuk',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Clutch Kulit Wanita',
                'kategori' => ['Dompet', 'Formal'],
                'warna' => ['Emas', 'Perak', 'Hitam'],
                'jenis_motif' => ['Polos', 'Metallic'],
                'harga_jual' => 750000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Clutch kulit elegan untuk acara formal dengan desain mewah',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 8,
                'panjang' => 25.00,
                'lebar' => 5.00,
                'tinggi' => 15.00,
                'detail_pola' => 'Clutch dengan resleting, tali rantai, dan 6 slot kartu',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Sepatu Kulit Formal',
                'kategori' => ['Sepatu', 'Formal'],
                'warna' => ['Hitam', 'Cokelat', 'Burgundy'],
                'jenis_motif' => ['Polos', 'Oxford'],
                'harga_jual' => 1200000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Sepatu kulit formal berkualitas tinggi untuk acara bisnis',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 5,
                'panjang' => 30.00,
                'lebar' => 11.00,
                'tinggi' => 12.00,
                'detail_pola' => 'Sepatu oxford dengan tali, sol karet anti-slip, dan insole nyaman',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Tas Tote Kulit',
                'kategori' => ['Tas', 'Shopping'],
                'warna' => ['Krem', 'Cokelat', 'Navy'],
                'jenis_motif' => ['Polos', 'Tote'],
                'harga_jual' => 950000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Tas tote kulit praktis untuk belanja dan kegiatan sehari-hari',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 6,
                'panjang' => 38.00,
                'lebar' => 15.00,
                'tinggi' => 32.00,
                'detail_pola' => 'Tas tote dengan 2 kompartemen, saku dalam, dan pegangan kulit',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Dompet Kartu Kulit',
                'kategori' => ['Dompet', 'Minimalis'],
                'warna' => ['Hitam', 'Cokelat', 'Navy'],
                'jenis_motif' => ['Polos', 'Slim'],
                'harga_jual' => 285000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Dompet kartu kulit minimalis tipis dan praktis',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 15,
                'panjang' => 10.00,
                'lebar' => 1.00,
                'tinggi' => 7.00,
                'detail_pola' => 'Dompet kartu dengan 8 slot, resleting koin, dan desain slim',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Sandal Jepit Kulit',
                'kategori' => ['Sandal', 'Casual'],
                'warna' => ['Cokelat', 'Hitam', 'Krem'],
                'jenis_motif' => ['Polos', 'Flip-flop'],
                'harga_jual' => 195000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Sandal jepit kulit nyaman untuk digunakan di rumah',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 20,
                'panjang' => 27.00,
                'lebar' => 10.00,
                'tinggi' => 3.50,
                'detail_pola' => 'Sandal jepit dengan tali kulit empuk dan sol karet ringan',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Tas Pinggang Kulit',
                'kategori' => ['Tas', 'Travel'],
                'warna' => ['Hitam', 'Cokelat', 'Hijia Tua'],
                'jenis_motif' => ['Polos', 'Waist Bag'],
                'harga_jual' => 485000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Tas pinggang kulit praktis untuk traveling dan kegiatan outdoor',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 10,
                'panjang' => 32.00,
                'lebar' => 8.00,
                'tinggi' => 18.00,
                'detail_pola' => 'Tas pinggang dengan 3 saku, resleting, dan sabuk adjustable',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Cangkir Kopi Kulit',
                'kategori' => ['Aksesoris', 'Unik'],
                'warna' => ['Cokelat', 'Hitam'],
                'jenis_motif' => ['Polos', 'Bentuk'],
                'harga_jual' => 165000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Cangkir kopi kulit unik untuk kopi dan teh',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 12,
                'panjang' => 9.00,
                'lebar' => 9.00,
                'tinggi' => 10.00,
                'detail_pola' => 'Cangkir kulit dengan pegangan dan alas karet',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Portofolio Kulit',
                'kategori' => ['Aksesoris', 'Profesional'],
                'warna' => ['Hitam', 'Cokelat', 'Burgundy'],
                'jenis_motif' => ['Polos', 'Executive'],
                'harga_jual' => 685000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Portofolio kulit profesional untuk dokumen dan laptop',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 4,
                'panjang' => 40.00,
                'lebar' => 3.00,
                'tinggi' => 30.00,
                'detail_pola' => 'Portofolio dengan resleting, 12 slot kartu, dan saku laptop',
                'sketsa' => [],
                'foto' => null,
            ],
            [
                'kode_produk' => $this->generateKodeProdukIncremental(),
                'nama_produk' => 'Gelang Kulit',
                'kategori' => ['Aksesoris', 'Fashion'],
                'warna' => ['Cokelat', 'Hitam', 'Merah'],
                'jenis_motif' => ['Polos', 'Braided'],
                'harga_jual' => 125000,
                'tipe_harga' => 'per_produk',
                'deskripsi' => 'Gelang kulit braided handmade untuk pria dan wanita',
                'keterangan' => 'Produk baru dengan BOM',
                'stok' => 0,
                'stok_minimum' => 25,
                'panjang' => 20.00,
                'lebar' => 1.50,
                'tinggi' => 0.50,
                'detail_pola' => 'Gelang braided dengan kancing kulit dan tali pengikat',
                'sketsa' => [],
                'foto' => null,
            ],
        ];

        foreach ($produkBaruDenganBahan as $produk) {
            ProdukJadi::create($produk);
        }

        // Buat BOM untuk 10 produk baru (hubungkan dengan bahan yang sudah ada)
        $this->buatBOMUntukProdukBaru();
    }

    /**
     * Buat BOM untuk produk baru dengan bahan yang sudah ada
     */
    private function buatBOMUntukProdukBaru(): void
    {
        // Ambil 10 produk terakhir yang baru saja dibuat
        $produkTerakhir = ProdukJadi::orderBy('id', 'desc')->limit(10)->get();
        
        // Ambil beberapa bahan yang tersedia (misal 5 bahan pertama)
        $bahanTersedia = DB::table('bahan_bakus')->limit(5)->get();
        
        foreach ($produkTerakhir as $produk) {
            // Buat BOM untuk setiap produk dengan 2-3 bahan
            $jumlahBahan = rand(2, 3);
            $bahanDipakai = $bahanTersedia->random($jumlahBahan);
            
            foreach ($bahanDipakai as $bahan) {
                DB::table('bom')->insert([
                    'id_produk' => $produk->id,
                    'id_bahan' => $bahan->id,
                    'jumlah_kebutuhan' => rand(1, 5),
                    'satuan' => $bahan->satuan ?? 'pcs',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}