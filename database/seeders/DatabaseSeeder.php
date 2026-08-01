<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Tambahkan 5 produk baru untuk halaman BOM
        $this->call(ProdukBaruBOMSeeder::class);

        $this->call(UserSeeder::class);

        // Tambahkan testimonial untuk produk e-commerce
        $this->call(TestimonialSeeder::class);
    }
}