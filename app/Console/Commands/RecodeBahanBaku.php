<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BahanBaku;
use App\Models\Pengaturan;

class RecodeBahanBaku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bahan:recode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbarui semua kode material bahan baku agar sesuai dengan prefix global dari pengaturan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses perbaikan kode material...');

        // Ambil prefix dari tabel pengaturan, dengan fallback 'BHN-'
        $prefix = Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
        $this->info("Menggunakan prefix: [{$prefix}]");

        $bahanBakus = BahanBaku::all();
        $progressBar = $this->output->createProgressBar($bahanBakus->count());
        $updatedCount = 0;

        foreach ($bahanBakus as $bahan) {
            // Cek apakah kode sudah sesuai format (PREFIX + 4 digit angka)
            if (!preg_match('/^' . preg_quote($prefix, '/') . '\d{4}$/', $bahan->kode_material)) {
                $newKode = $this->generateNewUniqueCode($prefix);
                $bahan->kode_material = $newKode;
                $bahan->save();
                $updatedCount++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nProses selesai. Total {$updatedCount} kode material telah diperbarui.");
        return 0;
    }

    /**
     * Generate a new unique code with the given prefix.
     */
    private function generateNewUniqueCode(string $prefix): string
    {
        do {
            $number = rand(1000, 9999);
            $kode = $prefix . $number;
        } while (BahanBaku::where('kode_material', $kode)->exists());

        return $kode;
    }
}