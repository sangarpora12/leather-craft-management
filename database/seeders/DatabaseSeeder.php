<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ... (Biarkan kode user admin yang sudah ada)

        // BUAT USER OPERATOR
        DB::table('users')->updateOrInsert(
            ['email' => 'operator1@berliano.com'],
            [
                'name' => 'Operator Satu',
                'password' => Hash::make('password'), // Password-nya adalah 'password'
                'role' => 'operator',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Seed sample data produk + BOM agar menu Produksi punya data
        // (mengikuti struktur tabel `produk_jadis` dan `bom` di project ini)
        $this->call([
            ProdukJadiSeeder::class,
            GrafikSimulatorSeeder::class,
        ]);
    }
}