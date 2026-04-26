<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProdukJadi;
use Illuminate\Support\Facades\DB;

class FixProductJsonFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:fix-json-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure Kategori, Warna, and Jenis Motif fields are valid JSON arrays, not null or empty strings.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to fix product JSON fields...');

        // Find products where fields are NULL or empty string ''
        $productsToFix = ProdukJadi::whereNull('kategori')
            ->orWhere('kategori', '')
            ->orWhereNull('warna')
            ->orWhere('warna', '')
            ->orWhereNull('jenis_motif')
            ->orWhere('jenis_motif', '')
            ->get();

        if ($productsToFix->isEmpty()) {
            $this->info('No products needed fixing. All JSON fields are correctly formatted.');
            return 0;
        }

        $this->info("Found " . $productsToFix->count() . " products to fix.");
        $progressBar = $this->output->createProgressBar($productsToFix->count());
        $progressBar->start();

        $fixedCount = 0;
        $defaultJsonValue = json_encode(["Lain-lain"]);

        foreach ($productsToFix as $product) {
            $updateData = [];

            // Check and fix 'kategori'
            if (empty($product->kategori) || !is_array(json_decode($product->kategori, true))) {
                $updateData['kategori'] = $defaultJsonValue;
            }

            // Check and fix 'warna'
            if (empty($product->warna) || !is_array(json_decode($product->warna, true))) {
                $updateData['warna'] = $defaultJsonValue;
            }

            // Check and fix 'jenis_motif'
            if (empty($product->jenis_motif) || !is_array(json_decode($product->jenis_motif, true))) {
                $updateData['jenis_motif'] = $defaultJsonValue;
            }

            if (!empty($updateData)) {
                // Use DB::table to bypass model's mutators/casts during this raw fix
                DB::table('produk_jadis')->where('id', $product->id)->update($updateData);
                $fixedCount++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nSuccessfully fixed " . $fixedCount . " products.");

        return 0;
    }
}