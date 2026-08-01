<?php

namespace Database\Seeders;

use App\Models\ProdukJadi;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $products = ProdukJadi::where('stok', '>', 0)->get();
        if ($products->isEmpty()) {
            $this->command->info('Tidak ada produk dengan stok > 0.');
            return;
        }

        $data = [
            ['Budi Santoso', 'Kualitas kulitnya sangat premium! Jahitan rapi dan nyaman dipakai.', 5],
            ['Siti Rahayu', 'Produk sesuai deskripsi. Bahan kulit asli dan tahan lama.', 5],
            ['Andi Wijaya', 'Bagus banget! Detailnya halus dan warnanya cantik.', 4],
            ['Dewi Lestari', 'Saya beli untuk hadiah dan penerimanya sangat senang.', 5],
            ['Rudi Hartono', 'Harga sebanding dengan kualitas. Kulitnya tebal dan kuat.', 4],
        ];

        foreach ($products as $product) {
            $count = rand(2, 3);
            for ($i = 0; $i < $count; $i++) {
                $t = $data[$i];
                Testimonial::create([
                    'produk_jadi_id' => $product->id,
                    'user_id' => null,
                    'nama_pelanggan' => $t[0],
                    'ulasan' => $t[1],
                    'rating' => $t[2],
                    'is_approved' => true,
                ]);
            }
        }

        $this->command->info('Testimonial dibuat untuk ' . $products->count() . ' produk.');
    }
}