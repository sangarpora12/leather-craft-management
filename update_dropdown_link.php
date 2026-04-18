<?php
// Update dropdown link in admin.blade.php

$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

// Update the "Kelola Profil & Akun" link
$content = str_replace(
    "route('admin.laporan.pengaturan.index') . '?menu=profil'",
    "route('admin.profile')",
    $content
);

file_put_contents($file, $content);

echo "Dropdown link updated successfully!\n";
