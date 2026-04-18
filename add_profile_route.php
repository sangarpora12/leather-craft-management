<?php
// Add ProfileController route to web.php

$file = 'routes/web.php';
$content = file_get_contents($file);

// Add import
if (strpos($content, 'use App\Http\Controllers\Admin\ProfileController;') === false) {
    $content = str_replace(
        'use App\Http\Controllers\Admin\WorkerMasterController;',
        "use App\Http\Controllers\Admin\WorkerMasterController;\nuse App\Http\Controllers\Admin\ProfileController;",
        $content
    );
}

// Add route
$search = "Route::get('/hak-akses/permissions', [HakAksesController::class, 'permissions'])->name('hak-akses.permissions');
        
    });";

$replace = "Route::get('/hak-akses/permissions', [HakAksesController::class, 'permissions'])->name('hak-akses.permissions');

        // --- PROFILE (Exclusive Admin Profile Page) ---
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        
    });";

$content = str_replace($search, $replace, $content);

file_put_contents($file, $content);

echo "Route added successfully!\n";
