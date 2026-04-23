<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Satuan;
use App\Models\Kategori;
use App\Models\Warna;
use App\Models\Motif;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Bom;
use App\Models\ProdukKeluar;
use App\Models\Pengadaan;
use App\Models\BahanStokLayer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class PengaturanController extends Controller
{
    /**
     * Tampilkan Halaman Pengaturan Utama
     */
    public function index()
    {
        $pengaturanCollection = Pengaturan::pluck('nilai', 'nama_pengaturan');
        $config = (object) $pengaturanCollection->toArray();
        
        $satuans = Satuan::all();
        $kategories = Kategori::all();
        $warnas = Warna::all();
        $motifs = Motif::all();
        $users = User::all();
        $user = Auth::user();
        
        return view('admin.pengaturan.index', compact('pengaturanCollection', 'config', 'satuans', 'kategories', 'warnas', 'motifs', 'users', 'user'));
    }

    /**
     * Simpan Pengaturan Umum & Update Kode Jika Prefix Berubah
     */
    public function store(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        // Backward compatibility (Pengaturan Gudang):
        // sebagian halaman lama menyimpan stok minimum global di key `stok_minimum`.
        // Sistem stok bahan sudah distandarkan ke `stok_minimum_global`.
        if (array_key_exists('stok_minimum', $inputs) && !array_key_exists('stok_minimum_global', $inputs)) {
            $inputs['stok_minimum_global'] = $inputs['stok_minimum'];
        }
        
        $oldPrefixBahan = Pengaturan::where('nama_pengaturan', 'prefix_bahan')->value('nilai') ?? 'BHN-';
        $oldPrefixProduk = Pengaturan::where('nama_pengaturan', 'prefix_produk')->value('nilai') ?? 'PR-';
        
        $marginChanged = false;
        $newMarginValue = null;

        foreach ($inputs as $key => $value) {
            if ($key === 'logo_perusahaan' && $request->hasFile('logo_perusahaan')) {
                $file = $request->file('logo_perusahaan');
                $pengaturanLama = Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first();
                if ($pengaturanLama && $pengaturanLama->nilai) {
                    Storage::delete('public/' . $pengaturanLama->nilai);
                }
                $value = $file->store('logos', 'public');
            }

            if ($value !== null && $value !== '') {
                Pengaturan::updateOrCreate(['nama_pengaturan' => $key], ['nilai' => $value]);
                
                if ($key === 'margin_keuntungan') {
                    $marginChanged = true;
                    $newMarginValue = (float) $value;
                }
            }
        }

        $newPrefixBahan = $request->prefix_bahan ?? $oldPrefixBahan;
        $newPrefixProduk = $request->prefix_produk ?? $oldPrefixProduk;
        
        $successMessages = [];

        if ($oldPrefixBahan !== $newPrefixBahan) {
            $updatedBahan = DB::update(
                "UPDATE bahan_bakus SET kode_material = CONCAT(?, SUBSTRING(kode_material, ?)) WHERE kode_material LIKE ?",
                [$newPrefixBahan, strlen($oldPrefixBahan) + 1, $oldPrefixBahan . '%']
            );
            if ($updatedBahan > 0) {
                $successMessages[] = "{$updatedBahan} kode bahan diperbarui (Prefix [{$oldPrefixBahan}] ke [{$newPrefixBahan}])";
            }
        }

        if ($oldPrefixProduk !== $newPrefixProduk) {
            $updatedProduk = DB::update(
                "UPDATE produk_jadis SET kode_produk = CONCAT(?, SUBSTRING(kode_produk, ?)) WHERE kode_produk LIKE ?",
                [$newPrefixProduk, strlen($oldPrefixProduk) + 1, $oldPrefixProduk . '%']
            );
            if ($updatedProduk > 0) {
                $successMessages[] = "{$updatedProduk} kode produk diperbarui (Prefix [{$oldPrefixProduk}] ke [{$newPrefixProduk}])";
            }
        }

        // ✅ GLOBAL PRICE SYNC: Update harga jual otomatis jika margin berubah
        $syncCount = 0;
        if ($marginChanged && $newMarginValue !== null) {
            $produkOtomatis = \App\Models\ProdukJadi::where('tipe_harga', 'otomatis')->get();
            foreach ($produkOtomatis as $produk) {
                if ($produk->updateAutomaticPrice($newMarginValue)) {
                    $syncCount++;
                }
            }
        }

        $msg = !empty($successMessages) ? 'Berhasil! ' . implode('. ', $successMessages) : 'Pengaturan Berhasil Disimpan!';
        
        if ($syncCount > 0) {
            $msg .= " ✅ Margin diperbarui dan {$syncCount} produk otomatis dihitung ulang harganya.";
        }
        
        return redirect()->route('admin.laporan.pengaturan.index')->with('success', $msg);
    }

    /**
     * Ganti Password Akun
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama tidak cocok!');
        }
        
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return back()->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Update Profil Admin
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Max 10MB
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('no_hp')) $user->no_hp = $request->no_hp;
        
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) Storage::delete('public/' . $user->foto_profil);
            $user->foto_profil = $request->file('foto_profil')->store('profile_photos', 'public');
        }
        
        $user->save();
        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Mega Configuration Center (Gudang & Stok)
     */
    public function gudangIndex()
    {
        $pengaturanCollection = Pengaturan::pluck('nilai', 'nama_pengaturan');
        return view('admin.laporan.pengaturan.gudang', compact('pengaturanCollection'));
    }
    
    public function gudangStore(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        // Backward compatibility:
        // sebagian UI/route lama pernah menyimpan stok minimum global ke key `stok_minimum`.
        // Di sistem ini kita standarkan ke `stok_minimum_global`.
        if (array_key_exists('stok_minimum', $inputs) && !array_key_exists('stok_minimum_global', $inputs)) {
            $inputs['stok_minimum_global'] = $inputs['stok_minimum'];
        }
        
        $marginChanged = false;
        $newMarginValue = null;

        foreach ($inputs as $key => $value) {
            if ($value !== null && $value !== '') {
                Pengaturan::updateOrCreate(['nama_pengaturan' => $key], ['nilai' => $value]);
                
                if ($key === 'margin_keuntungan') {
                    $marginChanged = true;
                    $newMarginValue = (float) $value;
                }
            }
        }
        
        // ✅ GLOBAL PRICE SYNC: Update harga jual otomatis jika margin berubah
        $syncCount = 0;
        if ($marginChanged && $newMarginValue !== null) {
            $produkOtomatis = \App\Models\ProdukJadi::where('tipe_harga', 'otomatis')->get();
            foreach ($produkOtomatis as $produk) {
                if ($produk->updateAutomaticPrice($newMarginValue)) {
                    $syncCount++;
                }
            }
        }
        
        $msg = 'Konfigurasi Gudang Berhasil Disimpan!';
        if ($syncCount > 0) {
            $msg .= " ✅ Margin diperbarui dan {$syncCount} produk otomatis dihitung ulang harganya.";
        }
        
        return redirect()->route('admin.laporan.pengaturan.gudang.index')->with('success', $msg);
    }

    /**
     * Sistem Maintenance (Cache & Optimization)
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            return back()->with('success', 'Cache Berhasil Dibersihkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function optimizeDatabase()
    {
        try {
            Artisan::call('optimize:clear');
            return back()->with('success', 'Sistem Berhasil Dioptimalkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Reset Data Pilihan
     */
    public function resetData(Request $request)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            if ($request->has('reset_transaksi')) {
                Produksi::truncate();
                ProdukKeluar::truncate();
                Pengadaan::truncate();
                BahanStokLayer::truncate();
                ActivityLog::truncate();
                DB::table('notifications')->truncate();
                BahanBaku::query()->update(['stok' => 0]);
                ProdukJadi::query()->update(['stok' => 0]);
            }
            
            if ($request->has('reset_bahan')) BahanBaku::truncate();
            if ($request->has('reset_produk')) {
                Bom::truncate();
                ProdukJadi::truncate();
            }
            if ($request->has('reset_supplier')) Supplier::truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('success', 'Data Pilihan Berhasil Direset!');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * TOMBOL KIAMAT (Super Reset)
     */
    public function superReset()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            BahanStokLayer::truncate();
            Produksi::truncate();
            ProdukKeluar::truncate();
            Pengadaan::truncate();
            DB::table('notifications')->truncate();
            ActivityLog::truncate();
            BahanBaku::truncate();
            Supplier::truncate();
            ProdukJadi::truncate();
            Bom::truncate();
            Satuan::truncate();
            Kategori::truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->route('admin.dashboard')->with('success', 'DATABASE BERHASIL DIKOSONGKAN TOTAL!');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}