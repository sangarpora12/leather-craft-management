<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { background-color: #00000080; } /* Simulasi background overlay */ </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-[#111827] w-full max-w-5xl rounded-2xl border border-gray-800 shadow-2xl flex overflow-hidden max-h-[90vh]">
        
        <div class="w-1/3 border-r border-gray-800 p-8 flex flex-col overflow-y-auto">
            <div class="flex items-center justify-between mb-8">
                <h3 class="font-bold text-xl text-white">Edit Profil Operator</h3>
                <button class="text-gray-500 hover:text-white">✕</button>
            </div>

            <div class="flex justify-center mb-8 relative">
                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-700">
                    <img src="https://ui-avatars.com/api/?name=Budi+Santoso" class="w-full h-full object-cover">
                </div>
                <button class="absolute bottom-0 bg-orange-500 text-white p-1 rounded-full border-2 border-[#111827]">+</button>
            </div>

            <form class="space-y-5">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Nama Lengkap</label>
                    <input type="text" value="Budi Santoso" class="w-full bg-[#1F2937] border border-gray-700 rounded-lg px-4 py-3 text-sm text-white focus:border-orange-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">ID Operator</label>
                        <input type="text" value="GDG-001" disabled class="w-full bg-[#0F172A] border border-gray-800 rounded-lg px-4 py-3 text-sm text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Role</label>
                        <input type="text" value="Operator Gudang" disabled class="w-full bg-[#0F172A] border border-gray-800 rounded-lg px-4 py-3 text-sm text-gray-500">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-800">
                    <p class="text-xs text-blue-400 mb-4 font-bold">Akun Login</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Username</label>
                            <input type="text" value="budi_gudang01" class="w-full bg-[#1F2937] border border-gray-700 rounded-lg px-4 py-3 text-sm text-white">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Password</label>
                            <input type="password" value="password123" class="w-full bg-[#1F2937] border border-gray-700 rounded-lg px-4 py-3 text-sm text-white">
                            <p class="text-[10px] text-gray-500 mt-1">*Biarkan tetap berbintang jika tidak ingin mengubah.</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" class="flex-1 border border-gray-600 text-gray-300 py-3 rounded-lg font-bold hover:bg-gray-800">Batal</button>
                    <button type="button" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-lg font-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        <div class="w-2/3 bg-[#0F172A] p-8 overflow-y-auto">
            
            <div class="bg-[#111827] rounded-xl p-6 mb-6 flex items-center justify-between border border-gray-800">
                <div class="flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name=Zulkifli+Syahputra&background=random" class="w-12 h-12 rounded-lg">
                    <div>
                        <h2 class="font-bold text-lg text-white">Zulkifli Syahputra</h2>
                        <p class="text-blue-400 text-xs">Operator Gudang</p>
                    </div>
                </div>
                <button class="text-gray-400">✕</button>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-[#1F2937] p-4 rounded-xl border border-gray-700 text-center">
                    <p class="text-xs text-gray-400 mb-1">Total Input</p>
                    <h4 class="text-2xl font-bold text-white">1,250</h4>
                    <span class="text-[10px] text-gray-500">Unit</span>
                </div>
                <div class="bg-[#1F2937] p-4 rounded-xl border border-gray-700 text-center">
                    <p class="text-xs text-gray-400 mb-1">Scan OCR</p>
                    <h4 class="text-2xl font-bold text-green-400">1,100</h4>
                    <span class="text-[10px] text-gray-500">Nota</span>
                </div>
                <div class="bg-[#1F2937] p-4 rounded-xl border border-gray-700 text-center">
                    <p class="text-xs text-gray-400 mb-1">Produk</p>
                    <h4 class="text-2xl font-bold text-purple-400">850</h4>
                    <span class="text-[10px] text-gray-500">Produksi</span>
                </div>
            </div>

            <div class="bg-[#111827] p-5 rounded-xl border border-gray-800 mb-6">
                <div class="flex justify-between text-xs mb-2">
                    <span class="text-blue-400">Scan OCR: 1,100</span>
                    <span class="text-orange-400">Manual: 150</span>
                </div>
                <div class="w-full h-3 bg-gray-800 rounded-full overflow-hidden flex">
                    <div class="bg-blue-500 h-full" style="width: 85%"></div>
                    <div class="bg-orange-500 h-full" style="width: 15%"></div>
                </div>
            </div>

            <h4 class="text-sm font-bold text-gray-400 mb-4">Bahan Masuk (Bulan Ini)</h4>
            <div class="space-y-3">
                <div class="bg-[#1F2937] p-4 rounded-lg flex justify-between items-center border border-gray-700 hover:border-gray-500 transition">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-700 p-2 rounded text-lg">🧵</div>
                        <div>
                            <p class="font-bold text-sm text-white">Kulit Sapi</p>
                            <p class="text-[10px] text-gray-500">Bahan Utama</p>
                        </div>
                    </div>
                    <span class="text-green-400 font-bold text-sm">500 Lbr</span>
                </div>
                
                 <div class="bg-[#1F2937] p-4 rounded-lg flex justify-between items-center border border-gray-700 hover:border-gray-500 transition">
                    <div class="flex items-center gap-3">
                        <div class="bg-gray-700 p-2 rounded text-lg">🔗</div>
                        <div>
                            <p class="font-bold text-sm text-white">Resleting YKK</p>
                            <p class="text-[10px] text-gray-500">Aksesoris</p>
                        </div>
                    </div>
                    <span class="text-blue-400 font-bold text-sm">3,500 Pcs</span>
                </div>
            </div>

        </div>
    </div>

</body>
</html>