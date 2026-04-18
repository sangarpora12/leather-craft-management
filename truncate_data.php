<?php
/**
 * Simple script to truncate all test data
 * Run via: php truncate_data.php
 */

// Database configuration (XAMPP default)
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'berliano_leather';

// Connect to database
$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Connected to database successfully.\n";

// Disable foreign key checks
$mysqli->query("SET FOREIGN_KEY_CHECKS=0;");

// Truncate tables (only include tables that exist)
$tables = [
    'bahan_stok_layers',
    'bahan_bakus', 
    'suppliers',
    'produk_jadis',
    'produksis',
    'produk_keluars',
    'pengadaans',
    'notifications',
    'activity_logs'
];

foreach ($tables as $table) {
    // Check if table exists first
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        // Table exists, truncate it
        $truncateResult = $mysqli->query("TRUNCATE TABLE $table");
        if ($truncateResult) {
            echo "✓ Table '$table' truncated successfully.\n";
        } else {
            echo "✗ Error truncating table '$table': " . $mysqli->error . "\n";
        }
    } else {
        echo "○ Table '$table' does not exist, skipping.\n";
    }
}

// Re-enable foreign key checks
$mysqli->query("SET FOREIGN_KEY_CHECKS=1;");

echo "\n✅ All data has been truncated successfully!\n";
echo "Database is now clean and ready for fresh data.\n";

$mysqli->close();
