<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Produksi - Operator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Style untuk langkah progress bar */
        .step-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin-top: 20px;
        }
        .step-progress::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 4px;
            background: #2A2A2A;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
        }
        .step-progress .progress-line {
            position: absolute;
            height: 4px;
            background: #00C853; /* Warna Hijau/Biru */
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            transition: width 0.5s ease-in-out; /* Animasi progress */
        }
        .step-progress .step {
            width: 24px;
            height: 24px;
            background: #2A2A2A;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 3;
            font-size: 10px;
            border: 2px solid #000;
            transition: background 0.3s;
        }
        .step-progress .step.active {
            background: #FF9800; /* Orange untuk aktif */
        }
        .step-progress .step.done {
            background: #00C853; /* Hijau untuk selesai */
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen pb-20">

    <div class="max-w-md mx-auto min-h-screen relative bg-black">
        
        <header class="flex justify-between items-center p-6 sticky top-0 bg-black/90 backdrop-blur-sm z-50 border-b border-white/5">
            <a href="{{ route('operator.tugas.detail') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold">Status Produksi</h1>
            <span class="text-gray-400 text-xs">SPK #0123</span>
        </header>

        <div id="status-container" class="p-6">
            
            <div class="bg-[#1A1A1A] p-4 rounded-2xl border border-white/5 relative mb-6">
                <h4 class="text-sm font-bold text-white mb-1">Sling Bag Premium</h4>
                <p class="text-[10px] text-gray-500 font-mono mb-3">Kode: PRO-T-001 • Maroon</p>
                <div class="grid grid-cols-2 text-xs text-gray-400">
                    <div>
                        <p>Target</p>
                        <p class="font-bold text-white">20 Pcs</p>
                    </div>
                    <div class="text-right">
                        <p>Deadline</p>
                        <p class="font-bold text-red-500">16:00 WIB</p>
                    </div>
                </div>
            </div>

            <div id="content-area">
                </div>

            <div id="action-button-area" class="mt-12">
                </div>
            
        </div>
        
        @include('components.bottom-nav-operator')

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentArea = document.getElementById('content-area');
            const actionButtonArea = document.getElementById('action-button-area');
            
            // Logika sederhana untuk mendapatkan status dari URL (e.g., ?status=proses)
            const urlParams = new URLSearchParams(window.location.search);
            let currentStatus = urlParams.get('status') || 'siap'; 
            
            // Fungsi untuk merender tampilan sesuai status
            function renderStatus(status) {
                let contentHTML = '';
                let actionHTML = '';

                // --- LOGIKA STATUS SIAP ---
                if (status === 'siap') {
                    contentHTML = `
                        <h4 class="text-sm font-bold text-gray-400 uppercase mb-4">Tahapan Pekerjaan</h4>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Mulai Pekerjaan</p>
                                    <p class="text-xs text-gray-500">Klik tombol di bawah untuk memulai sesi.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 opacity-50">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] border border-white/10 flex items-center justify-center text-gray-500 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m5.632 6.368a8.001 8.001 0 0010.582-1.474M13 13v5h.01"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500">Proses Pengerjaan</p>
                                    <p class="text-xs text-gray-600">Menunggu inisiasi.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 opacity-50">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] border border-white/10 flex items-center justify-center text-gray-500 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500">Selesaikan Tugas</p>
                                    <p class="text-xs text-gray-600">Finalisasi hasil produksi.</p>
                                </div>
                            </div>
                        </div>
                    `;
                    actionHTML = `
                        <p class="text-center text-gray-500 text-xs mb-3">Pastikan alokasi bahan sudah memulai sebelum memulai.</p>
                        <a href="?status=proses" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-4 rounded-xl flex justify-center transition active:scale-95 shadow-xl shadow-orange-900/20">
                            Mulai Produksi
                        </a>
                        <button class="w-full bg-black/10 text-gray-500 font-bold py-2 mt-2 rounded-xl text-xs hover:bg-black/20">
                            Lapor Masalah
                        </button>
                    `;
                } 
                
                // --- LOGIKA STATUS PROSES ---
                else if (status === 'proses') {
                    contentHTML = `
                        <h4 class="text-sm font-bold text-gray-400 uppercase mb-4">Tahapan Produksi</h4>
                        
                        <div class="px-2 mb-6">
                            <div class="step-progress relative">
                                <div class="progress-line" style="width: 50%;"></div>
                                <div class="step done">✓</div>
                                <div class="step active">2</div>
                                <div class="step">3</div>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500 mt-2">
                                <span>Mulai</span>
                                <span>Proses</span>
                                <span>Selesai</span>
                            </div>
                        </div>
                        
                        <div class="mt-8 space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Persiapan Bahan</p>
                                    <p class="text-xs text-gray-500">Selesai: 08:30 WIB</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m5.632 6.368a8.001 8.001 0 0010.582-1.474M13 13v5h.01"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Proses Penjahitan</p>
                                    <p class="text-xs text-gray-500">Gabungkan pola utama dengan lining di dalam.</p>
                                    <span class="bg-blue-600/20 text-blue-500 text-[10px] font-bold px-2 py-0.5 rounded mt-1 inline-block">SEDANG BERJALAN</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 opacity-50">
                                <div class="w-8 h-8 rounded-full bg-[#1A1A1A] border border-white/10 flex items-center justify-center text-gray-500 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500">Finishing & QC</p>
                                    <p class="text-xs text-gray-600">Bersihkan sisa benang & lem.</p>
                                </div>
                            </div>
                        </div>
                    `;
                    actionHTML = `
                        <a href="?status=selesai" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl flex justify-center transition active:scale-95 shadow-xl shadow-blue-900/20">
                            Tandai Selesai & Lapor →
                        </a>
                        <button class="w-full bg-black/10 text-gray-500 font-bold py-2 mt-2 rounded-xl text-xs hover:bg-black/20">
                            Lapor Masalah
                        </button>
                    `;
                } 
                
                // --- LOGIKA STATUS SELESAI ---
                else if (status === 'selesai') {
                    contentHTML = `
                        <div class="bg-green-600/20 border border-green-500/50 p-4 rounded-xl mb-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="font-bold text-green-500">Produksi Selesai!</p>
                                    <p class="text-xs text-gray-300">Semua tahapan telah dikerjakan. Silakan buat laporan akhir.</p>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-sm font-bold text-gray-400 uppercase mb-4">Ringkasan Waktu</h4>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Persiapan Bahan</p>
                                    <p class="text-xs text-gray-500">Selesai: 08:30 WIB</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Proses Penjahitan</p>
                                    <p class="text-xs text-gray-500">Selesai: 12:45 WIB</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Finishing & QC</p>
                                    <p class="text-xs text-gray-500">Selesai: 15:30 WIB</p>
                                </div>
                            </div>
                        </div>
                    `;
                    actionHTML = `
                        <a href="#" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-4 rounded-xl flex justify-center transition active:scale-95 shadow-xl shadow-green-900/20">
                            Buat Laporan Produksi
                        </a>
                    `;
                }

                contentArea.innerHTML = contentHTML;
                actionButtonArea.innerHTML = actionHTML;
                
                // Panggil ulang renderStatus jika ada perubahan status dari tombol di dalam konten
                // Ini memastikan transisi antar status berfungsi.
                actionButtonArea.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        const targetStatus = new URLSearchParams(link.search).get('status');
                        if (targetStatus) {
                            e.preventDefault();
                            history.pushState(null, '', `?status=${targetStatus}`); // Update URL
                            renderStatus(targetStatus); // Render status baru
                        }
                    });
                });
            }

            // Panggil fungsi render pertama kali saat halaman dimuat
            renderStatus(currentStatus);
        });
    </script>
</body>
</html>