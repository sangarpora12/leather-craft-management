<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                if (Schema::hasColumn('carts', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->change();
                }
                if (!Schema::hasColumn('carts', 'session_id')) {
                    $table->string('session_id')->nullable()->index()->after('user_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->change();
                }
                if (!Schema::hasColumn('orders', 'guest_email')) {
                    $table->string('guest_email')->nullable()->after('user_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                if (Schema::hasColumn('carts', 'session_id')) {
                    $table->dropColumn('session_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'guest_email')) {
                    $table->dropColumn('guest_email');
                }
            });
        }
    }
};