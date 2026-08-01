<?php
// Debug route untuk cek produk
Route::get('/debug-produk', function () {
    $total = \App\Models\ProdukJadi::count();
    $stokAda = \App\Models\ProdukJadi::where('stok', '>', 0)->count();
    $stokKosong = \App\Models\ProdukJadi::where('stok', '=', 0)->count();
    
    echo "Total Produk: $total<br>";
    echo "Stok > 0: $stokAda<br>";
    echo "Stok = 0: $stokKosong<br><br>";
    
    echo "Sample produk:<br>";
    foreach (\App\Models\ProdukJadi::limit(5)->get() as $p) {
        echo "- {$p->nama_produk} | Stok: {$p->stok} | Harga: {$p->harga_jual}<br>";
    }
});