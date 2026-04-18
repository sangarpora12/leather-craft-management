<?php
// Fix the dropdown link in admin.blade.php

$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

// Replace the old link with the new one
$content = str_replace(
    "route('admin.laporan.pengaturan.index') }}?menu=profil\"",
    "route('admin.profile') }}\"",
    $content
);

file_put_contents($file, $content);

echo "Fixed successfully!\n";
