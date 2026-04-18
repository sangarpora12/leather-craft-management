<?php
$file = 'resources/views/admin/laporan/index.blade.php';
$content = file_get_contents($file);
$content = str_replace('$prod->last_update->format(', '$prod->created_at ? $prod->created_at->format(', $content);
file_put_contents($file, $content);
echo "Fixed!";
