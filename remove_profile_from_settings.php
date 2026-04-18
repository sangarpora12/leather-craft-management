<?php
// Remove profile section from settings view

$file = 'resources/views/admin/pengaturan/index.blade.php';
$content = file_get_contents($file);

// Remove the Profil & Akun card (the clickable card that opens the form)
$content = preg_replace(
    '/<!-- Card 1: Profil \& Akun -->.*?<!-- End Card 1 -->/s',
    '',
    $content
);

// Also try to remove the simpler card version
$content = preg_replace(
    '/<div onclick="bukaMenu\(\'profil\'\)".*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<!-- =/s',
    '<!-- =',
    $content
);

// Remove the profile form section
$content = preg_replace(
    '/<div id="form-profil".*?<\/div>\s*<\/div>\s*<\/div>\s*<!-- End Form Profil -->/s',
    '',
    $content
);

file_put_contents($file, $content);

echo "Profile section removed from settings view!\n";
