<?php

namespace App\Traits;

use App\Models\LogAktivitas;

trait CatatLogAktivitas
{
    /**
     * Boot the trait - register event listeners
     */
    protected static function bootCatatLogAktivitas()
    {
        // Event: Created
        static::created(function ($model) {
            static::catatAktivitas($model, 'created');
        });

        // Event: Updated
        static::updated(function ($model) {
            static::catatAktivitas($model, 'updated');
        });

        // Event: Deleted
        static::deleted(function ($model) {
            static::catatAktivitas($model, 'deleted');
        });
    }

    /**
     * Catat aktivitas ke database
     */
    protected static function catatAktivitas($model, string $event)
    {
        $modul = static::getNamaModul();
        $namaData = static::getNamaData($model);
        $aktivitas = static::buatDeskripsiAktivitas($model, $event, $namaData);
        
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'modul' => $modul,
            'aktivitas' => $aktivitas,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Dapatkan nama modul dari class name
     * Contoh: BahanBaku -> 'Bahan Baku'
     */
    protected static function getNamaModul(): string
    {
        $className = class_basename(static::class);
        
        // Ubah CamelCase menjadi Title Case dengan spasi
        $modul = preg_replace('/(?<!^)[A-Z]/', ' $0', $className);
        
        return trim($modul);
    }

    /**
     * Dapatkan nama data yang berubah
     */
    protected static function getNamaData($model): string
    {
        // Coba ambil dari kolom 'nama_produk', 'nama_material', 'nama', dll
        $namaKolom = ['nama_produk', 'nama_material', 'nama', 'nama_pengaturan', 'kode_produk', 'kode_material'];
        
        foreach ($namaKolom as $kolom) {
            if (isset($model->$kolom)) {
                return $model->$kolom;
            }
        }
        
        // Fallback ke ID
        return 'ID: ' . $model->id;
    }

    /**
     * Buat deskripsi aktivitas
     */
    protected static function buatDeskripsiAktivitas($model, string $event, string $namaData): string
    {
        switch ($event) {
            case 'created':
                return "Menambahkan data baru: {$namaData}";
                
            case 'updated':
                $perubahan = static::dapatkanPerubahan($model);
                if (!empty($perubahan)) {
                    return "Mengubah data: {$namaData} | Kolom yang diubah: " . implode(', ', $perubahan);
                }
                return "Mengubah data: {$namaData}";
                
            case 'deleted':
                return "Menghapus data: {$namaData}";
                
            default:
                return "Aktivitas {$event} pada data: {$namaData}";
        }
    }

    /**
     * Dapatkan daftar kolom yang berubah
     */
    protected static function dapatkanPerubahan($model): array
    {
        if (method_exists($model, 'getDirty')) {
            $dirty = $model->getDirty();
            return array_keys($dirty);
        }
        return [];
    }
}