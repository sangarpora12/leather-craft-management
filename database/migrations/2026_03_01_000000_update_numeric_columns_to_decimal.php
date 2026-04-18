 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah kolom numerik dari integer ke decimal(10,2)
     * agar mendukung angka pecahan seperti 0.25, 1.5, dll
     */
    public function up(): void
    {
        // 1. Update tabel bahan_bakus
        Schema::table('bahan_bakus', function (Blueprint $table) {
            if (Schema::hasColumn('bahan_bakus', 'stok')) {
                $table->decimal('stok', 10, 2)->default(0)->change();
            }
            if (Schema::hasColumn('bahan_bakus', 'stok_minimum')) {
                $table->decimal('stok_minimum', 10, 2)->default(10)->change();
            }
        });

        // 2. Update tabel bom (Bill of Materials)
        Schema::table('bom', function (Blueprint $table) {
            if (Schema::hasColumn('bom', 'jumlah_kebutuhan')) {
                $table->decimal('jumlah_kebutuhan', 10, 2)->change();
            }
        });

        // 3. Update tabel pengadaans (Bahan Masuk)
        Schema::table('pengadaans', function (Blueprint $table) {
            if (Schema::hasColumn('pengadaans', 'jumlah')) {
                $table->decimal('jumlah', 10, 2)->change();
            }
        });

        // 4. Update tabel produksis
        Schema::table('produksis', function (Blueprint $table) {
            if (Schema::hasColumn('produksis', 'jumlah_produksi')) {
                $table->decimal('jumlah_produksi', 10, 2)->default(1)->change();
            }
            if (Schema::hasColumn('produksis', 'jumlah_selesai')) {
                $table->decimal('jumlah_selesai', 10, 2)->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke integer (data desimal akan dibulatkan)
        Schema::table('bahan_bakus', function (Blueprint $table) {
            if (Schema::hasColumn('bahan_bakus', 'stok')) {
                $table->integer('stok')->default(0)->change();
            }
            if (Schema::hasColumn('bahan_bakus', 'stok_minimum')) {
                $table->integer('stok_minimum')->default(10)->change();
            }
        });

        Schema::table('bom', function (Blueprint $table) {
            if (Schema::hasColumn('bom', 'jumlah_kebutuhan')) {
                $table->integer('jumlah_kebutuhan')->change();
            }
        });

        Schema::table('pengadaans', function (Blueprint $table) {
            if (Schema::hasColumn('pengadaans', 'jumlah')) {
                $table->integer('jumlah')->change();
            }
        });

        Schema::table('produksis', function (Blueprint $table) {
            if (Schema::hasColumn('produksis', 'jumlah_produksi')) {
                $table->integer('jumlah_produksi')->default(1)->change();
            }
            if (Schema::hasColumn('produksis', 'jumlah_selesai')) {
                $table->integer('jumlah_selesai')->default(0)->change();
            }
        });
    }
};
