<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Bahan - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen pb-20">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-xl relative">
        
        <div class="bg-black text-white p-6 rounded-b-[2rem] shadow-xl relative z-10">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">Input Stok Masuk 📦</h2>
                    <p class="text-xs text-gray-400 mt-1">Catat bahan baku baru dari supplier.</p>
                </div>
                <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center font-bold text-sm border border-gray-700">OP</div>
            </div>
        </div>

        <div class="p-6 -mt-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('operator.bahan.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Kode Material</label>
                        <input type="text" name="kode_material" placeholder="Scan / Ketik Kode..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none transition font-mono" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Nama Bahan</label>
                        <input type="text" name="nama_material" placeholder="Contoh: Kulit Sapi Premium" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-black focus:border-black outline-none transition" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Kategori</label>
                            <div class="relative">
                                <select name="kategori" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm appearance-none outline-none focus:ring-2 focus:ring-black">
                                    <option>Bahan Utama</option>
                                    <option>Aksesoris</option>
                                    <option>Kimia</option>
                                    <option>Packaging</option>
                                </select>
                                <div class="absolute right-3 top-4 pointer-events-none text-gray-400">▼</div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Satuan</label>
                            <div class="relative">
                                <select name="satuan" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm appearance-none outline-none focus:ring-2 focus:ring-black">
                                    <option>Pcs</option>
                                    <option>Lembar</option>
                                    <option>Rol</option>
                                    <option>Kg</option>
                                    <option>Kaleng</option>
                                </select>
                                <div class="absolute right-3 top-4 pointer-events-none text-gray-400">▼</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Jumlah Masuk</label>
                            <input type="number" name="stok" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-bold text-center focus:ring-2 focus:ring-black outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Harga Beli</label>
                            <input type="number" name="harga_satuan" placeholder="Rp" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-bold text-center focus:ring-2 focus:ring-black outline-none transition" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Supplier <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="supplier" placeholder="Nama Supplier" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-black outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 ml-1">Ref / Nota <span class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="ref_no" placeholder="Nomor Referensi" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-black outline-none transition">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-black text-white font-bold text-sm py-4 rounded-xl shadow-xl hover:bg-gray-800 transition transform active:scale-95 flex justify-center items-center gap-2 mt-4">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Data
                    </button>

                </form>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 p-3 max-w-md mx-auto flex justify-around items-center z-50 shadow-[0_-5px_10px_rgba(0,0,0,0.02)]">
            <a href="#" class="flex flex-col items-center gap-1 text-black">
                <div class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-[10px] font-bold">Input</span>
            </a>
            <a href="{{ route('operator.dashboard') }}" class="flex flex-col items-center gap-1 text-gray-400 hover:text-black transition">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] font-medium">Home</span>
            </a>
        </div>

    </div>

</body>
</html>