<?php
// Remove profile routes from settings

$file = 'routes/web.php';
$content = file_get_contents($file);

// Remove profile-akun route
$content = str_replace(
    "Route::post('/pengaturan/profil-akun', [PengaturanController::class, 'updateProfilAkun'])->name('pengaturan.profil.update');\n",
    "",
    $content
);

// Remove profil route
$content = str_replace(
    "Route::post('/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');\n",
    "",
    $content
);

file_put_contents($file, $content);

echo "Profile routes removed from settings!\n";
