<?php
$file = 'resources/views/admin/laporan/index.blade.php';
$content = file_get_contents($file);

// Remove export PDF button
$content = preg_replace('/<a href="\{\{ route\(\'admin\.laporan\.exportPdf\'[^}]+\)\}\}"[^>]*>.*?Export PDF.*?<\/a>/s', '', $content);

file_put_contents($file, $content);
echo "Fixed! Export button removed.";
