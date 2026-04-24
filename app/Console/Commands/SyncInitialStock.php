<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncInitialStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:initial-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync initial stock from bahan_bakus to bahan_stok_layers if no layers exist.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting stock synchronization...');

        // Ambil semua bahan baku yang memiliki stok > 0
        $bahanBakus = BahanBaku::where('stok', '>', 0)->get();
        $syncedCount = 0;

        foreach ($bahanBakus as $bahan) {
            // Cek apakah sudah ada layer stok untuk bahan ini
            $hasLayers = BahanStokLayer::where('bahan_baku_id', $bahan->id)->exists();

            // Jika tidak ada layer, buat satu layer awal (dummy)
            if (!$hasLayers) {
                $this->line("Processing: {$bahan->nama_material} (ID: {$bahan->id}) - No layers found. Creating initial layer.");

                BahanStokLayer::create([
                    'bahan_baku_id' => $bahan->id,
                    'tanggal_masuk' => Carbon::now(),
                    'qty_masuk' => $bahan->stok,
                    'qty_sisa' => $bahan->stok,
                    'harga_beli' => $bahan->harga_satuan, // Menggunakan harga_satuan dari master bahan
                    'sumber_harga' => 'SYNC-INITIAL' // Penanda bahwa ini adalah data sinkronisasi
                ]);

                $syncedCount++;
                $this->info("  > Synced: {$bahan->nama_material}. Created initial stock layer with {$bahan->stok} {$bahan->satuan}.");
            } else {
                $this->line("Skipping: {$bahan->nama_material} (ID: {$bahan->id}) - Layers already exist.");
            }
        }

        $this->info("Synchronization complete. Total materials synced: {$syncedCount}.");
        return 0;
    }
}