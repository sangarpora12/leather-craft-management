<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produksis')) {
            return;
        }

        Schema::table('produksis', function (Blueprint $table) {
            // Kolom-kolom inti untuk SPK/Produksi.
            // Dibuat idempotent karena beberapa DB sudah punya sebagian kolom.

            if (!Schema::hasColumn('produksis', 'kode_spk')) {
                $table->string('kode_spk', 50)->unique()->after('id');
            }

            if (!Schema::hasColumn('produksis', 'produk_jadi_id')) {
                $table->unsignedBigInteger('produk_jadi_id')->nullable()->after('kode_spk');
                $table->foreign('produk_jadi_id')->references('id')->on('produk_jadis')->onDelete('set null');
            }

            if (!Schema::hasColumn('produksis', 'jumlah_produksi')) {
                $table->integer('jumlah_produksi')->default(1);
            }

            if (!Schema::hasColumn('produksis', 'status')) {
                $table->string('status', 20)->default('PENDING');
            }

            if (!Schema::hasColumn('produksis', 'operator_id')) {
                $table->unsignedBigInteger('operator_id')->nullable();
                $table->foreign('operator_id')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('produksis', 'total_biaya_hpp')) {
                $table->decimal('total_biaya_hpp', 15, 2)->default(0);
            }

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

            if (!Schema::hasColumn('produksis', 'catatan')) {
                $table->text('catatan')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Tidak melakukan drop kolom untuk menghindari merusak instalasi yang sudah jalan.
    }
};
