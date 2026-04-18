<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('produksis')) {
            Schema::table('produksis', function (Blueprint $table) {
                
                // Pastikan kolom 'kode_spk' sudah ditambahkan di migrasi sebelumnya.
                // Kolom-kolom ini akan ditambahkan setelah 'kode_spk'

                // 1. Foreign Key ke Produk Jadi (Penyebab error saat ini)
                if (!Schema::hasColumn('produksis', 'produk_jadi_id')) {
                    $table->unsignedBigInteger('produk_jadi_id')->nullable()->after('kode_spk');
                    // Tambahkan foreign key constraint
                    $table->foreign('produk_jadi_id')->references('id')->on('produk_jadis')->onDelete('set null');
                }
                
                // 2. Jumlah Produksi
                if (!Schema::hasColumn('produksis', 'jumlah_produksi')) {
                    $table->integer('jumlah_produksi')->default(1);
                }
                
                // 3. Status
                if (!Schema::hasColumn('produksis', 'status')) {
                    $table->string('status', 20)->default('PROSES');
                }

                // 4. Operator ID
                if (!Schema::hasColumn('produksis', 'operator_id')) {
                    $table->unsignedBigInteger('operator_id')->nullable();
                    $table->foreign('operator_id')->references('id')->on('users')->onDelete('set null');
                }
                
                // 5. Total Biaya HPP
                if (!Schema::hasColumn('produksis', 'total_biaya_hpp')) {
                    $table->decimal('total_biaya_hpp', 15, 2)->default(0);
                }

                // 6. Kolom Tanggal
                if (!Schema::hasColumn('produksis', 'tanggal_mulai')) {
                    $table->date('tanggal_mulai')->nullable();
                }
                if (!Schema::hasColumn('produksis', 'tanggal_target')) {
                    $table->date('tanggal_target')->nullable();
                }
                if (!Schema::hasColumn('produksis', 'tanggal_selesai')) {
                    $table->date('tanggal_selesai')->nullable();
                }
                if (!Schema::hasColumn('produksis', 'tanggal_mulai_proses')) {
                    $table->timestamp('tanggal_mulai_proses')->nullable();
                }
                
                // 7. Catatan
                if (!Schema::hasColumn('produksis', 'catatan')) {
                    $table->text('catatan')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('produksis')) {
            Schema::table('produksis', function (Blueprint $table) {
                // Hapus foreign keys terlebih dahulu
                if (Schema::hasColumn('produksis', 'produk_jadi_id') && Schema::hasIndex('produksis', 'produksis_produk_jadi_id_foreign')) {
                    $table->dropForeign('produksis_produk_jadi_id_foreign');
                }                

                if (Schema::hasColumn('produksis', 'operator_id') && Schema::hasIndex('produksis', 'produksis_operator_id_foreign')) {
                    $table->dropForeign('produksis_operator_id_foreign');
                }
                
                if (Schema::hasColumn('produksis', 'id_produk')) {
                    $table->dropForeign(['id_produk']);
                }

                if (Schema::hasColumn('produksis', 'id_user')) {
                    try {
                        $table->dropForeign('produksis_operator_id_foreign');
                    } catch (\Exception $e) {
                        // Handle the exception (e.g., log it or ignore it)
                        dump($e->getMessage());
                    }
                    }


                // Hapus kolom inti

                $table->dropColumn([
                    'produk_jadi_id',
                    'jumlah_produksi',
                    'status',
                    'operator_id', 
                    'total_biaya_hpp', 
                    'tanggal_mulai', 
                    'tanggal_target',
                    'tanggal_selesai', 
                    'tanggal_mulai_proses', 
                    'catatan'
                ]);
            });
        }
    }
};