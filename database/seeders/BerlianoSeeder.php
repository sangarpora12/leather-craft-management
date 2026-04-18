<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Produksi;
use App\Models\Bom;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BerlianoSeeder extends Seeder
{
    public function run()
    {
        // =====================================================
        // USER DUMMY (ADMIN & OPERATOR)
        // =====================================================
        $admin = User::updateOrCreate(
            ['email' => 'admin@berliano.test'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $operator = User::updateOrCreate(
            ['email' => 'zulkifli@berliano.test'],
            [
                'name' => 'Zulkifli',
                'password' => bcrypt('password'),
                'role' => 'operator',
                'email_verified_at' => now(),
            ]
        );

        // =====================================================
        // BAHAN BAKU (Sesuai skenario)
        // =====================================================
        $kulit = BahanBaku::updateOrCreate(
            ['kode_material' => 'BB-001'],
            [
                'nama_material' => 'Kulit Sapi (Grade A)',
                'kategori' => 'Kulit',
                // BAB 1: masuk 100 lembar
                'stok' => 100,
                'satuan' => 'Lembar',
                'harga_satuan' => 25000,
                'stok_minimum' => 10,
                'status' => 'Aman',
                'supplier' => 'CV. Kulit Jaya',
                'ref_no' => 'NOTA-DEMO-001',
            ]
        );

        $resleting = BahanBaku::updateOrCreate(
            ['kode_material' => 'BB-002'],
            [
                'nama_material' => 'Resleting',
                'kategori' => 'Aksesoris',
                'stok' => 200,
                'satuan' => 'Pcs',
                'harga_satuan' => 2000,
                'stok_minimum' => 10,
                'status' => 'Aman',
                'supplier' => 'CV. Aksesoris Tas',
                'ref_no' => 'NOTA-DEMO-002',
            ]
        );

        // Tambah bahan dengan stok rendah untuk demo procurement
        $kulitRendah = BahanBaku::updateOrCreate(
            ['kode_material' => 'BB-003'],
            [
                'nama_material' => 'Kulit Sapi (Grade B)',
                'kategori' => 'Kulit',
                'stok' => 5, // Stok rendah
                'satuan' => 'Lembar',
                'harga_satuan' => 20000,
                'stok_minimum' => 10,
                'status' => 'Menipis',
                'supplier' => 'CV. Kulit Murah',
                'ref_no' => 'NOTA-DEMO-003',
            ]
        );

        $benang = BahanBaku::updateOrCreate(
            ['kode_material' => 'BB-004'],
            [
                'nama_material' => 'Benang Jahit',
                'kategori' => 'Aksesoris',
                'stok' => 8, // Stok rendah
                'satuan' => 'Roll',
                'harga_satuan' => 15000,
                'stok_minimum' => 10,
                'status' => 'Menipis',
                'supplier' => 'PT. Benang Indonesia',
                'ref_no' => 'NOTA-DEMO-004',
            ]
        );

        // =====================================================
        // PRODUK JADI (Rome Bag)
        // =====================================================
        $romeBag = ProdukJadi::updateOrCreate(
            ['kode_produk' => 'PRO-001'],
            [
                'nama_produk' => 'Rome Bag',
                'harga_jual' => 250000,
                'stok' => 0,
                'deskripsi' => 'Produk contoh untuk demo Produksi/BOM',
                'warna' => 'Coklat',
                'jenis_motif' => 'Polos',
                'stok_minimum' => 10,
            ]
        );

        // =====================================================
        // BOM / RESEP
        // 1 Rome Bag: 2 Lembar Kulit + 1 Resleting
        // =====================================================
        // Bersihkan BOM existing agar tidak dobel jika skenario berubah
        Bom::where('id_produk', $romeBag->id)->delete();
        Bom::insert([
            [
                'id_produk' => $romeBag->id,
                'id_bahan' => $kulit->id,
                'jumlah_kebutuhan' => 2,
                'satuan' => 'Lembar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_produk' => $romeBag->id,
                'id_bahan' => $resleting->id,
                'jumlah_kebutuhan' => 1,
                'satuan' => 'Pcs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =====================================================
        // PRODUKSI / SPK
        // =====================================================
        // BAB 3: SPK-001 target 10 pcs (lusa)
        Produksi::updateOrCreate(
            ['kode_spk' => 'SPK-001'],
            [
                'produk_jadi_id' => $romeBag->id,
                'jumlah_produksi' => 10,
                'operator_id' => $operator->id,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_target' => now()->addDays(2)->toDateString(),
                'status' => 'PENDING',
                'catatan' => 'Demo: SPK pertama',
            ]
        );

        // BAB 6: SPK-002 target 50 pcs (butuh 100 kulit, stok sisa harus 80)
        // Catatan: stok kulit akan jadi 80 setelah SPK-001 diproses.
        Produksi::updateOrCreate(
            ['kode_spk' => 'SPK-002'],
            [
                'produk_jadi_id' => $romeBag->id,
                'jumlah_produksi' => 50,
                'operator_id' => $operator->id,
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_target' => now()->addDays(4)->toDateString(),
                'status' => 'PENDING',
                'catatan' => 'Demo: SPK kedua (untuk skenario stok kurang)',
            ]
        );

        // =====================================================
        // NOTIFICATIONS
        // =====================================================
        $operator->notify(new SystemNotification(
            'SPK Baru Tersedia',
            'SPK-001 untuk Rome Bag telah dibuat dan menunggu konfirmasi Anda.',
            'info',
            '/operator/tugas/1',
            'operator'
        ));

        $operator->notify(new SystemNotification(
            'Pengingat Deadline',
            'SPK-001 Rome Bag deadline dalam 2 hari lagi.',
            'warning',
            '/operator/tugas/1',
            'operator'
        ));

        $operator->notify(new SystemNotification(
            'Stok Bahan Menipis',
            'Kulit Sapi Grade A stok tersisa 80 lembar, segera lakukan pengadaan.',
            'warning',
            '/operator/pengadaan',
            'operator'
        ));

        $this->command?->info('Seeder Berliano: Admin/Operator + Bahan + Produk + BOM + 2 SPK (PENDING) + Notifications sudah dibuat.');
    }
}