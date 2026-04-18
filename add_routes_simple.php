<?php
// Add admin profile routes

$file = 'routes/web.php';
$content = file_get_contents($file);

// Add after hak-akses.permissions
$search = "Route::get('/hak-akses/permissions', [HakAksesController::class, 'permissions'])->name('hak-akses.permissions');";
$replace = $search . "

        // --- PROFILE (Admin Profile Page) ---
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');";

$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);

echo "Done!\n";
