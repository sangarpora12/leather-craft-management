<?php
$file = 'resources/views/admin/laporan/index.blade.php';
$content = file_get_contents($file);
// Fix incomplete ternary operator - add : 'N/A' 
$content = str_replace(
    "{{ \$prod->created_at ? \$prod->created_at->format('d/m H:i') }}",
    "{{ \$prod->created_at ? \$prod->created_at->format('d/m H:i') : 'N/A' }}",
    $content
);
file_put_contents($file, $content);
echo "Fixed incomplete ternary!";
