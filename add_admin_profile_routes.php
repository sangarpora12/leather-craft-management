<?php
// Add admin profile routes to web.php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Check if routes already exist
if (strpos($content, "Route::get('/profile', [ProfileController::class, 'index'])->name('profile')") !== false) {
    echo "Routes already exist!\n";
    exit;
}

// Find the location to add the routes (after hak-akses.permissions)
$search = "Route::get('/hak-akses/permissions', [HakAksesController::class, 'permissions'])->name('hak-akses.permissions');
        
    });";

$replace = "Route::get('/hak-akses/permissions', [HakAksesController::class, 'permissions'])->name('hak-akses.permissions');

        // --- PROFILE (Admin Profile Page) ---
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        
    });";

$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);

echo "Admin profile routes added successfully!\n";
