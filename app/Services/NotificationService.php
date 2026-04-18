<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification
     */
    public static function create(array $data)
    {
        try {
            // Get the target role (admin, operator, or all)
            $target = isset($data['target']) ? $data['target'] : 'all';
            
            $notificationData = [
                'type' => isset($data['type']) ? $data['type'] : 'info',
                'message' => isset($data['message']) ? $data['message'] : '',
                'role_target' => $target, // Store in the new column for easier filtering
                'data' => json_encode([
                    'title' => isset($data['title']) ? $data['title'] : '',
                    'message' => isset($data['message']) ? $data['message'] : '',
                    'type' => isset($data['type']) ? $data['type'] : 'info',
                    'url' => isset($data['url']) ? $data['url'] : '#',
                    'target' => $target,
                    'metadata' => isset($data['metadata']) ? $data['metadata'] : []
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ];

            $id = DB::table('notifications')->insertGetId($notificationData);

            Log::info('Notification created', ['id' => $id, 'type' => $data['type'], 'target' => $target]);

            return $id;
        } catch (\Exception $e) {
            Log::error('Failed to create notification', ['error' => $e->getMessage(), 'data' => $data]);
            return false;
        }
    }
    
    /**
     * Get notifications filtered by role
     * Checks both the new role_target column AND the old JSON data->target for backwards compatibility
     */
    public static function getByRole(string $role, bool $unreadOnly = false)
    {
        $query = DB::table('notifications')
            ->where(function($q) use ($role) {
                // Check new role_target column
                $q->where('role_target', $role)
                  ->orWhere('role_target', 'all');
            })
            ->orWhere(function($q) use ($role) {
                // Also check old JSON data->target for backwards compatibility
                // when role_target is NULL
                $q->whereNull('role_target')
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.target')) = ?", [$role]);
            })
            ->orWhere(function($q) use ($role) {
                // Check for 'all' target in old JSON data
                $q->whereNull('role_target')
                  ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.target')) = 'all'");
            })
            ->orderBy('created_at', 'desc');
            
        if ($unreadOnly) {
            $query->whereNull('read_at');
        }
        
        return $query->get();
    }
    
    /**
     * Get unread count filtered by role
     * FIXED: Properly groups the conditions to ensure read_at check is applied to all
     */
    public static function getUnreadCountByRole(string $role): int
    {
        return DB::table('notifications')
            ->where(function($q) use ($role) {
                // Group all conditions properly with OR between them
                $q->where(function($q2) use ($role) {
                    // Check new role_target column (unread only)
                    $q2->where('role_target', $role)
                       ->orWhere('role_target', 'all');
                })->whereNull('read_at')
                  ->orWhere(function($q2) use ($role) {
                    // Check old JSON data->target for backwards compatibility (unread only)
                    // when role_target is NULL
                    $q2->whereNull('role_target')
                       ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.target')) = ?", [$role])
                       ->whereNull('read_at');
                })->orWhere(function($q2) use ($role) {
                    // Check for 'all' target in old JSON data (unread only)
                    $q2->whereNull('role_target')
                       ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.target')) = 'all'")
                       ->whereNull('read_at');
                });
            })
            ->count();
    }
    
    /**
     * Mark notification as read
     */
    public static function markAsRead(int $id): bool
    {
        try {
            DB::table('notifications')
                ->where('id', $id)
                ->update([
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', ['error' => $e->getMessage(), 'id' => $id]);
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for a role
     * FIXED: Properly groups the conditions
     */
    public static function markAllAsReadByRole(string $role): int
    {
        try {
            return DB::table('notifications')
                ->where(function($q) use ($role) {
                    // Group all conditions properly
                    $q->where(function($q2) use ($role) {
                        // Check new role_target column
                        $q2->where('role_target', $role)
                           ->orWhere('role_target', 'all');
                    })->whereNull('read_at')
                      ->orWhere(function($q2) use ($role) {
                        // Check old JSON data->target for backwards compatibility
                        $q2->whereNull('role_target')
                           ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.target')) = ?", [$role])
                           ->whereNull('read_at');
                    })->orWhere(function($q2) use ($role) {
                        // Check for 'all' target in old JSON data
                        $q2->whereNull('role_target')
                           ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.target')) = 'all'")
                           ->whereNull('read_at');
                    });
                })
                ->update([
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', ['error' => $e->getMessage(), 'role' => $role]);
            return 0;
        }
    }

    /**
     * Create notification for Admin
     */
    public static function notifyAdmin(string $type, string $title, string $message, string $url = '#', array $metadata = [])
    {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'target' => 'admin',
            'metadata' => $metadata
        ]);
    }

    /**
     * Create notification for Operator
     */
    public static function notifyOperator(string $type, string $title, string $message, string $url = '#', array $metadata = [])
    {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'target' => 'operator',
            'metadata' => $metadata
        ]);
    }

    /**
     * Create notification for All users
     */
    public static function notifyAll(string $type, string $title, string $message, string $url = '#', array $metadata = [])
    {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'target' => 'all',
            'metadata' => $metadata
        ]);
    }

    // ===== ADMIN NOTIFICATIONS =====

    /**
     * Notify admin about approval requests (additional materials or overtime)
     */
    public static function approvalRequest($pengadaan)
    {
        $message = "Permintaan persetujuan: {$pengadaan->nama_bahan} - {$pengadaan->jumlah} {$pengadaan->satuan}";
        return self::notifyAdmin('warning', 'Permintaan Persetujuan', $message, route('admin.pengadaan.index'), [
            'pengadaan_id' => $pengadaan->id,
            'type' => 'approval_request'
        ]);
    }

    /**
     * Notify admin about low stock alerts
     */
    public static function lowStockAlert($bahanBaku)
    {
        $message = "Stok {$bahanBaku->nama_bahan} tersisa {$bahanBaku->stok} {$bahanBaku->satuan}. Segera pesan!";
        return self::notifyAdmin('warning', 'Stok Kritis', $message, route('admin.bahan.index'), [
            'bahan_id' => $bahanBaku->id,
            'type' => 'low_stock'
        ]);
    }

    /**
     * Notify admin about production delays
     */
    public static function productionDelay($produksi)
    {
        $message = "SPK {$produksi->kode_spk} terlambat deadline. Status: {$produksi->status}";
        return self::notifyAdmin('danger', 'Keterlambatan Produksi', $message, route('admin.produksi.show', $produksi->id), [
            'produksi_id' => $produksi->id,
            'type' => 'production_delay'
        ]);
    }

    /**
     * Notify admin about QC rejects
     */
    public static function qcRejectAlert($produksi, $rejectCount)
    {
        $message = "SPK {$produksi->kode_spk} memiliki {$rejectCount} barang reject QC.";
        return self::notifyAdmin('warning', 'Laporan QC Reject', $message, route('admin.produksi.show', $produksi->id), [
            'produksi_id' => $produksi->id,
            'type' => 'qc_reject',
            'reject_count' => $rejectCount
        ]);
    }

    /**
     * Notify admin about new orders
     */
    public static function newOrder($produksi)
    {
        $productName = $produksi->product ? $produksi->product->nama_produk : 'Produk';
        $message = "Order baru: {$productName} - {$produksi->jumlah_produksi} pcs";
        return self::notifyAdmin('info', 'Pesanan Baru Masuk', $message, route('admin.produksi.show', $produksi->id), [
            'produksi_id' => $produksi->id,
            'type' => 'new_order'
        ]);
    }

    // ===== OPERATOR NOTIFICATIONS =====

    /**
     * Notify operator about new task assignment
     */
    public static function newTaskAssignment($produksi, $operator)
    {
        $productName = $produksi->product ? $produksi->product->nama_produk : 'Produk';
        $message = "Tugas baru: {$productName} - {$produksi->jumlah_produksi} pcs. Mulai sekarang!";
        return self::notifyOperator('info', 'Tugas Baru', $message, route('operator.tugas.detail', $produksi->id), [
            'produksi_id' => $produksi->id,
            'operator_id' => $operator->id,
            'type' => 'new_task'
        ]);
    }

    /**
     * Notify operator about material availability
     */
    public static function materialAvailable($produksi, $operator)
    {
        $message = "Bahan untuk tugas {$produksi->kode_spk} sudah tersedia. Silakan ambil!";
        return self::notifyOperator('success', 'Bahan Tersedia', $message, route('operator.tugas.detail', $produksi->id), [
            'produksi_id' => $produksi->id,
            'operator_id' => $operator->id,
            'type' => 'material_available'
        ]);
    }

    /**
     * Notify operator about QC feedback
     */
    public static function qcFeedback($produksi, $operator, $status, $feedback = '')
    {
        $type = $status === 'PASS' ? 'success' : 'warning';
        $message = "Hasil QC untuk {$produksi->kode_spk}: {$status}";
        if ($feedback) {
            $message .= " - {$feedback}";
        }
        return self::notifyOperator($type, 'Hasil QC', $message, route('operator.tugas.detail', $produksi->id), [
            'produksi_id' => $produksi->id,
            'operator_id' => $operator->id,
            'type' => 'qc_feedback',
            'status' => $status
        ]);
    }

    /**
     * Notify operator about shift reminder
     */
    public static function shiftReminder($operator)
    {
        $message = "Pengingat: Segera input laporan harian sebelum log out.";
        return self::notifyOperator('warning', 'Pengingat Shift', $message, route('operator.dashboard'), [
            'operator_id' => $operator->id,
            'type' => 'shift_reminder'
        ]);
    }

    /**
     * Notify operator about priority changes
     */
    public static function priorityChange($produksi, $operator, $newPriority)
    {
        $message = "Prioritas tugas {$produksi->kode_spk} berubah menjadi: {$newPriority}";
        return self::notifyOperator('warning', 'Perubahan Prioritas', $message, route('operator.tugas.detail', $produksi->id), [
            'produksi_id' => $produksi->id,
            'operator_id' => $operator->id,
            'type' => 'priority_change',
            'new_priority' => $newPriority
        ]);
    }
}
