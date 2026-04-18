<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Supplier extends Model
{
    protected $table = 'suppliers';
    
    // Use id as primary key (not default 'id')
    protected $primaryKey = 'id_supplier';
    
    public $timestamps = true;
    
    // Mass assignable
    protected $guarded = [];
    
    /**
     * Get all unique stores with their material counts
     */
    public static function getAllStores()
    {
        return self::select('nama_toko', 'kontak', DB::raw('COUNT(*) as total_materials'), DB::raw('MIN(harga) as min_price'))
            ->whereNotNull('nama_toko')
            ->whereNotNull('nama_bahan')
            ->groupBy('nama_toko', 'kontak')
            ->orderBy('nama_toko')
            ->get();
    }
    
    /**
     * Get all unique store names
     */
    public static function getUniqueStoreNames()
    {
        return self::whereNotNull('nama_toko')
            ->where('nama_toko', '!=', '')
            ->distinct()
            ->pluck('nama_toko')
            ->sort()
            ->values();
    }
    
    /**
     * Get materials for a specific store
     */
    public static function getMaterialsForStore($namaToko)
    {
        return self::where('nama_toko', $namaToko)
            ->whereNotNull('nama_bahan')
            ->orderBy('nama_bahan')
            ->get();
    }
    
    /**
     * Get the cheapest price for each material name (for recommendations)
     * Includes price comparison (difference from cheapest)
     */
    public static function getPriceComparison()
    {
        // Get cheapest price per material
        $cheapest = self::select('nama_bahan', DB::raw('MIN(harga) as cheapest_price'), 'satuan')
            ->whereNotNull('harga')
            ->whereNotNull('nama_bahan')
            ->where('harga', '>', 0)
            ->groupBy('nama_bahan', 'satuan')
            ->get()
            ->keyBy('nama_bahan');
        
        // Get all prices for each material
        $allPrices = self::select('nama_bahan', 'nama_toko', 'harga', 'satuan')
            ->whereNotNull('harga')
            ->whereNotNull('nama_bahan')
            ->where('harga', '>', 0)
            ->orderBy('nama_bahan')
            ->orderBy('harga')
            ->get()
            ->groupBy('nama_bahan');
        
        $result = [];
        
        foreach ($allPrices as $namaBahan => $materials) {
            $cheapestData = $cheapest->get($namaBahan);
            if (!$cheapestData) continue;
            
            $cheapestPrice = $cheapestData->cheapest_price;
            $cheapestToko = $materials->firstWhere('harga', $cheapestPrice)->nama_toko ?? '';
            
            $materialList = $materials->map(function ($item) use ($cheapestPrice) {
                $difference = $item->harga - $cheapestPrice;
                return [
                    'nama_toko' => $item->nama_toko,
                    'harga' => $item->harga,
                    'satuan' => $item->satuan,
                    'difference' => $difference,
                    'is_cheapest' => $difference == 0,
                    'difference_text' => $difference == 0 ? 'Termurah' : 'lebih mahal ' . number_format($difference, 0, ',', '.')
                ];
            })->values();
            
            $result[] = [
                'nama_bahan' => $namaBahan,
                'cheapest_price' => $cheapestPrice,
                'cheapest_toko' => $cheapestToko,
                'satuan' => $cheapestData->satuan,
                'materials' => $materialList
            ];
        }
        
        return collect($result)->sortBy('nama_bahan')->values();
    }
    
    /**
     * Get the cheapest price for each material name (simple version for cards)
     */
    public static function getCheapestPrices()
    {
        return self::select('nama_bahan', \DB::raw('MIN(harga) as cheapest_price'), 'nama_toko', 'satuan')
            ->whereNotNull('harga')
            ->whereNotNull('nama_bahan')
            ->groupBy('nama_bahan', 'nama_toko', 'satuan')
            ->orderBy('nama_bahan')
            ->orderBy('cheapest_price')
            ->get()
            ->groupBy('nama_bahan')
            ->map(function ($items) {
                return $items->sortBy('cheapest_price')->first();
            });
    }
    
    /**
     * Get all prices for a specific material
     */
    public static function getPricesForMaterial($namaBahan)
    {
        return self::where('nama_bahan', 'like', "%{$namaBahan}%")
            ->orderBy('harga', 'asc')
            ->get();
    }
    
    /**
     * Find or update material price for a store
     * If material exists for the store, update the price
     * Otherwise, create new record
     * 
     * @param string $namaToko
     * @param string $namaBahan
     * @param float $harga
     * @param string $satuan
     * @param string|null $kontak
     * @return Supplier
     */
    public static function upsertMaterial($namaToko, $namaBahan, $harga, $satuan, $kontak = null)
    {
        // Normalize the material name for comparison
        $normalizedBahan = trim(strtolower($namaBahan));
        
        // Check if this material already exists for this store
        $existing = self::whereRaw('LOWER(TRIM(nama_toko)) = ?', [strtolower(trim($namaToko))])
            ->whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$normalizedBahan])
            ->first();
        
        if ($existing) {
            // Update existing record
            $existing->harga = $harga;
            $existing->satuan = $satuan;
            if ($kontak) {
                $existing->kontak = $kontak;
            }
            $existing->save();
            return $existing;
        } else {
            // Create new record
            return self::create([
                'nama_toko' => trim($namaToko),
                'nama_bahan' => trim($namaBahan),
                'harga' => $harga,
                'satuan' => $satuan,
                'kontak' => $kontak
            ]);
        }
    }
}
