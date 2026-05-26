<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
|
| Routes untuk sistem notifikasi admin
|
*/

Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/admin/api/notifications', [NotificationController::class, 'getNotifications'])->name('admin.notifications.api');
    // AJAX mark one notification as read
    Route::post('/admin/notifications/mark-read', [NotificationController::class, 'markRead'])->name('admin.notifications.mark-read');

    // Mark a notification as read then show detail page
    Route::get('/admin/notifications/{id}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('admin.notifications.read');

    // Detail notifikasi (halaman detail dengan info lengkap)
    Route::get('/admin/notifications/{id}', [NotificationController::class, 'show'])->name('admin.notifications.show');

    // Bulk actions
    Route::post('/admin/notifications/mark-all', [NotificationController::class, 'markAllRead'])->name('admin.notifications.mark-all');
    Route::delete('/admin/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('admin.notifications.delete-all');

    // Delete single notification
    Route::delete('/admin/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
});
