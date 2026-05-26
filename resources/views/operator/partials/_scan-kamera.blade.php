<div id="contentScan" class="flex flex-col w-full" style="height: 75vh;">
    
    <div id="cameraContainer" class="relative w-full flex-1 bg-black rounded-2xl overflow-hidden shadow-xl border border-white/10 z-10">
        <video id="cameraVideo" autoplay playsinline class="absolute inset-0 w-full h-full object-cover"></video>
        
        <div id="overlay" class="absolute inset-0 pointer-events-none" style="background: rgba(0,0,0,0.6);">
            <div id="guideline" class="absolute border-2 border-dashed border-green-400 rounded-xl" 
                 style="top: 10%; left: 5%; width: 90%; height: 75%; box-shadow: 0 0 0 9999px rgba(0,0,0,0.6); background: transparent;">
            </div>
        </div>

        <div class="absolute top-4 left-0 right-0 text-center pointer-events-none z-10">
            <span class="bg-black/60 text-white/90 text-sm px-4 py-2 rounded-full backdrop-blur-sm border border-white/20">
                Posisikan nota dalam bingkai
            </span>
        </div>

        <div id="scanLine" class="absolute left-[5%] right-[5%] h-0.5 bg-green-400 opacity-60 shadow-[0_0_8px_2px_rgba(74,222,128,0.5)] z-10" 
             style="top: 10%; display: none; animation: scanMove 2s ease-in-out infinite;">
        </div>

        <div id="flashIndicator" class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150 z-30"></div>
    </div>

    <div style="width: 100%; padding-top: 12px; padding-bottom: 80px; margin-top: -5px; position: relative; z-index: 20;">
        <button id="captureBtn" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white py-4 rounded-2xl font-semibold text-base flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" style="box-shadow: 0 8px 20px -5px rgba(16,185,129,0.5);">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <span>Scan Sekarang</span>
        </button>
    </div>

    <input type="hidden" id="imageBase64" name="image_base64" value="">
    <canvas id="captureCanvas" class="hidden"></canvas>
</div>

<style>
@keyframes scanMove {
    0%, 100% { top: 10%; }
    50% { top: calc(85% - 2px); }
}
</style>

<script>
(function() {
    const video = document.getElementById('cameraVideo');
    const captureBtn = document.getElementById('captureBtn');
    const canvas = document.getElementById('captureCanvas');
    const imageInput = document.getElementById('imageBase64');
    const scanLine = document.getElementById('scanLine');
    const flashIndicator = document.getElementById('flashIndicator');

    let stream = null;
    let isCapturing = false;

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { exact: 'environment' }, width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false
            });
            video.srcObject = stream;
            await video.play();
            captureBtn.disabled = false;
            scanLine.style.display = 'block';
        } catch (err) {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                video.srcObject = stream;
                await video.play();
                captureBtn.disabled = false;
                scanLine.style.display = 'block';
            } catch (err2) {
                alert('Izin kamera ditolak atau kamera tidak ditemukan.');
                captureBtn.disabled = true;
            }
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        captureBtn.disabled = true;
        scanLine.style.display = 'none';
    }

    function capturePhoto() {
        if (isCapturing || !video.videoWidth) return;
        isCapturing = true;

        flashIndicator.style.opacity = '0.8';
        setTimeout(() => { flashIndicator.style.opacity = '0'; }, 150);

        const vw = video.videoWidth;
        const vh = video.videoHeight;
        canvas.width = vw;
        canvas.height = vh;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, vw, vh);

        const base64 = canvas.toDataURL('image/jpeg', 0.9);
        imageInput.value = base64;

        stopCamera();

        if (typeof processOCR === 'function') {
            processOCR(base64);
        } else {
            document.getElementById('confirmBtn')?.click();
        }

        isCapturing = false;
    }

    captureBtn.addEventListener('click', capturePhoto);

    const tabScan = document.getElementById('tabScan');
    if (tabScan) {
        new MutationObserver(() => {
            if (!document.getElementById('contentScan').classList.contains('hidden')) startCamera();
            else stopCamera();
        }).observe(document.getElementById('contentScan'), { attributes: true, attributeFilter: ['class'] });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (!document.getElementById('contentScan').classList.contains('hidden')) setTimeout(startCamera, 500);
    });

    document.getElementById('retakeBtn')?.addEventListener('click', () => setTimeout(startCamera, 300));
    document.getElementById('cancelBtn')?.addEventListener('click', stopCamera);
    window.addEventListener('beforeunload', stopCamera);
})();
</script>