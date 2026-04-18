<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Bahan Baku Manual - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> 
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50 border-b border-white/10">
            <a href="{{ route('operator.scan.index') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Input Stok Manual</h1>
            <div class="w-6 h-6"></div>
        </header>

        <div class="p-6">
            <div x-data="{ 
                stok: {{ request('scanned_qty') ?? 'null' }}, 
                hargaRaw: {{ request('scanned_price') ?? 0 }}, 
                
                // Fungsi Format Rupiah & Update Data Sekaligus
                formatRupiah(e) {
                    let val = e.target.value.replace(/\D/g, ''); // Hapus semua kecuali angka
                    this.hargaRaw = val === '' ? 0 : parseInt(val); // Simpan angka murni
                    e.target.value = this.hargaRaw === 0 ? '' : this.hargaRaw.toLocaleString('id-ID'); // Update tampilan dengan titik
                },

                // Hitung Total Otomatis
                get totalHarga() {
                    return (this.stok || 0) * this.hargaRaw;
                }
            }">

                <form action="{{ route('operator.bahan.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nama Material</label>
                        <input type="text" name="nama_material" required value="{{ request('scanned_text') }}" autofocus
                               class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 focus:border-blue-500 transition"
                               placeholder="Contoh: Kulit Sapi Krom">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nomor Nota / Ref</label>
                        <input type="text" name="ref_no" value="{{ request('scanned_ref') }}"
                               class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 focus:border-blue-500 transition"
                               placeholder="No. Nota / Invoice">
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Stok Masuk</label>
                            <input type="number" name="stok" required min="1" x-model.number="stok"
                                   class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 focus:border-blue-500 transition"
                                   placeholder="0">
                        </div>
                        <div class="w-24">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Satuan</label>
                            <select name="satuan" required
                                    class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 focus:border-blue-500 transition">
                                <option value="PCS">PCS</option>
                                <option value="M2">M²</option>
                                <option value="KG">KG</option>
                                <option value="MTR">MTR</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Kategori</label>
                        <select name="kategori" required
                                class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 focus:border-blue-500 transition">
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="Bahan Baku Utama">Bahan Baku Utama</option>
                            <option value="Bahan Kimia">Bahan Kimia</option>
                            <option value="Aksesoris">Aksesoris / Hardware</option>
                            <option value="Benang & Jahit">Benang & Perlengkapan Jahit</option>
                            <option value="Packaging">Packaging</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Harga Satuan (Rp)</label>
                        
                        <input type="text" @input="formatRupiah" value="{{ request('scanned_price') ? number_format(request('scanned_price'), 0, ',', '.') : '' }}"
                               class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 focus:border-blue-500 transition"
                               placeholder="0">
                               
                        <input type="hidden" name="harga_satuan" :value="hargaRaw">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Supplier</label>
                        <input type="text" name="supplier" value="{{ request('scanned_supplier') }}"
                               class="w-full bg-[#1A1A1A] text-white border border-white/10 rounded-xl p-3 transition"
                               placeholder="Nama PT/Toko">
                    </div>

                    <div class="border-t border-white/20 pt-4 mt-6">
                        <label class="block text-lg font-bold text-white mb-2">TOTAL TRANSAKSI</label>
                        <div class="text-3xl font-extrabold text-green-400">
                            Rp <span x-text="totalHarga.toLocaleString('id-ID')">0</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 transition active:scale-95 shadow-lg">
                        Simpan Stok Masuk
                    </button>
                </form>
            </div>
        </div>
        
        @include('components.bottom-nav-operator')
    </div>

    @include('components.flash-notification')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentUserId = '{{ Auth::id() ?? 0 }}'; 
            if (typeof Echo !== 'undefined' && currentUserId !== '0') {
                Echo.private(`operator.${currentUserId}`)
                    .listen('BahanBakuAdded', (e) => {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: { message: `Sukses! Kode ${e.kode_material} tersimpan.`, type: 'success' }
                        }));
                    });
            }
        });
    </script>
</body>
</html>