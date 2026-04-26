<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProdukJadi;
use App\Models\Kategori;
use App\Models\Warna;
use App\Models\Motif;

class FixEmptyProductData extends Command
{
    protected $signature = 'app:fix-empty-product-data';
    protected $description = 'Force check and fix empty Kategori, Warna, and Jenis Motif for all products.';

    public function handle()
    {
        $this->info('Starting to FORCE fix empty product data...');

        // 1. Get all master data
        $kategories = Kategori::where('tipe', 'produk')->pluck('nama_kategori')->toArray();
        $warnas = Warna::pluck('nama_warna')->toArray();
        $motifs = Motif::pluck('nama_motif')->toArray();

        if (empty($kategories) || empty($warnas) || empty($motifs)) {
            $this->error('Master data for Kategori, Warna, or Motif is empty. Cannot proceed.');
            return 1;
        }

        // 2. Get ALL products
        $allProducts = ProdukJadi::all();

        if ($allProducts->isEmpty()) {
            $this->info('No products found in the database.');
            return 0;
        }

        $updatedCount = 0;

        $this->getOutput()->progressStart($allProducts->count());

        foreach ($allProducts as $product) {
            $isUpdated = false;

            // Check and fix Kategori
            if ($this->isFieldEmpty($product->kategori)) {
                $product->kategori = [$kategories[array_rand($kategories)]];
                $isUpdated = true;
            }

            // Check and fix Warna
            if ($this->isFieldEmpty($product->warna)) {
                $product->warna = [$warnas[array_rand($warnas)]];
                $isUpdated = true;
            }

            // Check and fix Jenis Motif
            if ($this->isFieldEmpty($product->jenis_motif)) {
                $product->jenis_motif = [$motifs[array_rand($motifs)]];
                $isUpdated = true;
            }

            if ($isUpdated) {
                $product->save();
                $updatedCount++;
            }
            
            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();

        $this->info("\n-----------------------------------------");
        $this->info("Finished fixing data.");
        $this->info("Total products updated: " . $updatedCount);

        return 0;
    }

    private function isFieldEmpty($field)
    {
        // Case 1: Null
        if (is_null($field)) {
            return true;
        }
        // Case 2: Empty Array
        if (is_array($field) && empty($field)) {
            return true;
        }
        // Case 3: Empty JSON string '[]' or ''
        if (is_string($field) && (trim($field) === '' || $field === '[]')) {
            return true;
        }
        return false;
    }
}