<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengadaans')) {
            return;
        }

        Schema::table('pengadaans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengadaans', 'no_referensi')) {
                $table->string('no_referensi')->nullable()->after('id');
                $table->index('no_referensi');
            }

            if (!Schema::hasColumn('pengadaans', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('operator_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('pengadaans')) {
            return;
        }

        Schema::table('pengadaans', function (Blueprint $table) {
            if (Schema::hasColumn('pengadaans', 'admin_id')) {
                try { $table->dropForeign(['admin_id']); } catch (\Throwable $e) {}
                $table->dropColumn('admin_id');
            }
            if (Schema::hasColumn('pengadaans', 'no_referensi')) {
                try { $table->dropIndex(['no_referensi']); } catch (\Throwable $e) {}
                $table->dropColumn('no_referensi');
            }
        });
    }
};
