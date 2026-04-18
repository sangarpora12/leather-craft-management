<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check if the column exists
$columns = DB::getSchemaBuilder()->getColumnListing('produk_keluars');

echo "Current columns in produk_keluars table:\n";
print_r($columns);

// Check if user_id column exists
if (!in_array('user_id', $columns)) {
    echo "\nAdding user_id column to produk_keluars table...\n";
    
    DB::statement('ALTER TABLE produk_keluars ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER catatan');
    DB::statement('ALTER TABLE produk_keluars ADD CONSTRAINT produk_keluars_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
    
    echo "Column added successfully!\n";
} else {
    echo "\nColumn user_id already exists!\n";
}

// Verify the column now
$columns = DB::getSchemaBuilder()->getColumnListing('produk_keluars');
echo "\nUpdated columns in produk_keluars table:\n";
print_r($columns);
