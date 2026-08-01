<?php
$file = __DIR__ . '/routes/web.php';
$content = file_get_contents($file);

// Fix merged lines caused by preg_replace collapsing blank lines
// Pattern: comment line merged with next line
$fixes = [
    '// ===================================// 1. AREA PUBLIC (BELUM LOGIN)' => "// ==========================================\n// 1. AREA PUBLIC (BELUM LOGIN)",
    "// ===================================Route::get('/', function () {" => "// ==========================================\nRoute::get('/', function () {",
    '// ===================================// CONSUMER ROUTES (E-commerce for customers)' => "// ==========================================\n// CONSUMER ROUTES (E-commerce for customers)",
    "// ===================================Route::prefix('consumer')->name('consumer.')->group(function () {" => "// ==========================================\nRoute::prefix('consumer')->name('consumer.')->group(function () {",
    '// ===================================    // 3. ADMIN & OPERATOR' => "// ==========================================\n    // 3. ADMIN & OPERATOR",
    "// ===================================    Route::middleware(['role:admin,operator'])->group(function () {" => "// ==========================================\n    Route::middleware(['role:admin,operator'])->group(function () {",
    '// =====================================================        // ALIAS ROUTES UNTUK KOMPATIBILITAS VIEW BLADE (PREFIX admin.*)' => "// ============================================================\n        // ALIAS ROUTES UNTUK KOMPATIBILITAS VIEW BLADE (PREFIX admin.*)",
    '// =====================================================        // Banyak view admin memanggil route' => "// ============================================================\n        // Banyak view admin memanggil route",
];

foreach ($fixes as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

file_put_contents($file, $content);

// Verify
$content = file_get_contents($file);
if (strpos($content, '>>>>>>>') !== false || strpos($content, '<<<<<<<') !== false || strpos($content, '=======') !== false) {
    echo "WARNING: Merge conflict markers still present!" . PHP_EOL;
} else {
    echo "Verification: No merge conflict markers found." . PHP_EOL;
}

// Check PHP syntax
$output = shell_exec("php -l " . escapeshellarg($file) . " 2>&1");
echo "PHP syntax check: " . $output;
