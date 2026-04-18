<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Pengadaan - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50">
            <a href="{{ route('operator.dashboard') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Tugas Pengadaan</h1>
            <span class="bg-blue-600/20 text-blue-400 text-xs font-bold px-3 py-1 rounded-full">#REQ-2024-088</span>
        </header>

        <div class="px-6 mb-6">
            <h2 class="text-xl font-bold mb-2">Daftar Belanja Material</h2>
            <p class="text-sm text-gray-400">Mohon segera proses pembelian untuk item berikut:</p>
        </div>

        <div class="px-6 space-y-4">

            <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/5 relative">
                <h4 class="font-bold text-white text-base">Benang Nilon Waxed</h4>
                <div class="flex justify-between items-end mt-2">
                    <p class="text-xs text-gray-500 font-mono">Kode: BAH-N-001</p>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Jumlah</p>
                        <p class="text-xl font-bold text-white">50 <span class="text-sm font-normal">Rol</span></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/5 relative">
                <h4 class="font-bold text-white text-base">Lem Aica Bond</h4>
                <div class="flex justify-between items-end mt-2">
                    <p class="text-xs text-gray-500 font-mono">Kode: CHM-A-022</p>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Jumlah</p>
                        <p class="text-xl font-bold text-white">25 <span class="text-sm font-normal">Kg</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/5 relative">
                <h4 class="font-bold text-white text-base">Kulit Sapi Nabati</h4>
                <div class="flex justify-between items-end mt-2">
                    <p class="text-xs text-gray-500 font-mono">Kode: BAH-K-005</p>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Jumlah</p>
                        <p class="text-xl font-bold text-white">20 <span class="text-sm font-normal">Lbr</span></p>
                    </div>
                </div>
            </div>

        </div>

        <div class="px-6 mt-6">
            <h3 class="font-bold text-sm text-gray-400 mb-2">Keterangan & Spesifikasi</h3>
            <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/5 flex gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">AD</div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    "Benang warna hitam Grade A tanpa sambungan. Lem kemasan kaleng besar, cek expired min. 1 tahun. Kulit Sapi ketebalan 1.8–2.0mm permukaan halus."
                </p>
            </div>
        </div>
        
    </div>

    <div class="fixed bottom-20 left-0 right-0 bg-black p-4 max-w-md mx-auto z-50">
        <a href="{{ route('operator.tugas.pengadaan.process') }}" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-900/20 transition active:scale-95 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Konfirmasi / Terima Tugas
        </a>
        <p class="text-center text-[10px] text-gray-500 mt-2">Klik untuk mengkonfirmasi proses pengadaan.</p>
    </div>

    @include('components.bottom-nav-operator')

</body>
</html>