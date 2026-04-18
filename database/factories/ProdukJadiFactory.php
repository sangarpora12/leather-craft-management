<?php

namespace Database\Factories;

use App\Models\ProdukJadi;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdukJadiFactory extends Factory
{
    protected $model = ProdukJadi::class;

    public function definition()
    {
        return [
            'nama_produk' => $this->faker->words(3, true),
            'kode_produk' => $this->faker->unique()->ean13,
            'warna' => $this->faker->colorName,
            'jenis_motif' => $this->faker->randomElement(['polos', 'bunga', 'garis', 'kotak']),
            'stok' => $this->faker->numberBetween(0, 100),
            'max_production' => $this->faker->numberBetween(1, 50),
            'is_ready' => $this->faker->boolean(70),
            'reason' => $this->faker->sentence,
            'keterangan' => $this->faker->sentence,
            'foto' => null,
        ];
    }
}