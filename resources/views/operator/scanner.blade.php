@extends('layouts.operator_app')

@section('title', 'Smart Scanner HD - Berliano Leather')

@section('content')
<div class="relative w-full h-screen overflow-hidden bg-black">
    <!-- Video Kamera HD Full Screen -->
    <video id="videoElement" class="absolute inset-0 w-full h-full object-cover" autoplay playsinline></video>
    
    <!-- Canvas untuk Processing (Hidden) -->
    <canvas id="processingCanvas" class="hidden"></canvas>
    
    <!-- Overlay KTP Style dengan Box Shadow Raksasa -->
    <div class="absolute inset-0 z-50 pointer-events-none">
        <!-- Area gelap di luar kotak (60% opacity) -->
        <div class="absolute inset-0" style="box-shadow: inset 0 0 0 9999px rgba(0, 0, 0, 0.6);"></div>
        
        <!-- Kotak bening di tengah (Viewport) -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 
                    w-[85%] max-w-md h-[65%] max-h-[500px] rounded-2xl overflow-hidden
                    border-4 transition-all duration-500"
             id="scanBox"
             style="border-color: #ef4444; border-width: 4px;">
            
            <!-- Corner Brackets (Lebih Besar) -->
            <div class="absolute top-0 left-0 w-14 h-14 border-t-4 border-l-4 rounded-tl-2xl border-red-500 corner-tl transition-colors duration-300"></div>
            <div class="absolute top-0 right-0 w-14 h-14 border-t-4 border-r-4 rounded-tr-2xl border-red-500 corner-tr transition-colors duration-300"></div>
            <div class="absolute bottom-0 left-0 w-14 h-14 border-b-4 border-l-4 rounded-bl-2xl border-red-500 corner-bl transition-colors duration-300"></div>
            <div class="absolute bottom-0 right-0 w-14 h-14 border-b-4 border-r-4 rounded-br-2xl border-red-500 corner-br transition-colors duration-300"></div>
            
            <!-- Scanning Line -->
            <div id="scanLine" class="absolute left-0 w-full h-1 bg-gradient-to-r from-transparent via-green-400 to-transparent opacity-0"></div>
        </div>
        
        <!-- Instruction Text -->
        <div class="absolute top-16 left-1/2 transform -translate-x-1/2 text-center z-50">
            <h2 class="text-white text-2xl font-bold drop-shadow-2xl" id="mainText">Smart Scanner HD</h2>
            <p class="text-gray-200 text-sm mt-2 drop-shadow-lg" id="subText">Posisikan nota di dalam bingkai</p>
        </div>
        
        <!-- Status Indicator -->
        <div class="absolute bottom-52 left-1/2 transform -translate-x-1/2 z-50">
            <div class="flex items-center gap-3 bg-black/70 backdrop-blur-md px-6 py-3 rounded-full border border-white/10">
                <div id="statusDot" class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
                <span id="statusText" class="text-white text-sm font-medium">Mendeteksi...</span>
            </div>
        </div>
    </div>
    
    <!-- Control Buttons -->
    <div class="absolute bottom-32 left-1/2 transform -translate-x-1/2 z-50 flex flex-col items-center gap-6">
        <!-- Capture Button (Besar & Responsif) -->
        <button id="captureBtn" class="group relative disabled:opacity-50 disabled:cursor-not-allowed" disabled>
            <div class="w-24 h-24 rounded-full bg-white border-4 border-gray-300 flex items-center justify-center 
                        transition-all duration-300 group-hover:scale-110 group-disabled:scale-100 shadow-2xl">
                <div class="w-20 h-20 rounded-full bg-red-500 transition-all duration-300 
                            group-disabled:bg-gray-400 group-hover:bg-red-600"></div>
            </div>
            <span class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-white text-sm font-medium 
                         whitespace-nowrap drop-shadow-lg">Ambil Foto</span>
        </button>
        
        <!-- Toggle Camera -->
        <button id="toggleBtn" class="flex items-center gap-2 text-white/80 hover:text-white transition-colors text-sm bg-black/50 px-4 py-2 rounded-full backdrop-blur-sm border border-white/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Ganti Kamera
        </button>
        
        <!-- Back Button -->
        <a href="{{ route('operator.scan.index') }}" class="flex items-center gap-2 text-white/60 hover:text-white transition-colors text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>
    
    <!-- Flash Effect -->
    <div id="flashOverlay" class="absolute inset-0 bg-white opacity-0 pointer-events-none z-40 transition-opacity duration-100"></div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="absolute inset-0 bg-black/80 flex items-center justify-center z-50 hidden">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-white font-bold text-lg" id="loadingText">Memproses...</p>
        </div>
    </div>
    
    <!-- Error Message -->
    <div id="errorMessage" class="absolute top-24 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden">
        <p id="errorText"></p>
    </div>
</div>

<style>
@keyframes scanAnim {
    0% { top: 5%; opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { top: 95%; opacity: 0; }
}

.detected #scanBox {
    border-color: #22c55e !important;
    border-width: 4px !important;
}

.detected .corner-tl, .detected .corner-tr,
.detected .corner-bl, .detected .corner-br {
    border-color: #22c55e !important;
}

.detected #scanLine {
    opacity: 1;
    animation: scanAnim 2s linear infinite;
}

.detected #statusDot {
    background-color: #22c55e;
    animation: none;
}
</style>

<script>
// ==========================================
// SMART SCANNER HD - BERLIANO LEATHER
// Kualitas Tinggi + KTP Style Overlay
// ==========================================

const video = document.getElementById('videoElement');
const processingCanvas = document.getElementById('processingCanvas');
const captureBtn = document.getElementById('captureBtn');
const toggleBtn = document.getElementById('toggleBtn');
const scanBox = document.getElementById('scanBox');
const mainText = document.getElementById('mainText');
const subText = document.getElementById('subText');
const statusDot = document.getElementById('statusDot');
const statusText = document.getElementById('statusText');
const flashOverlay = document.getElementById('flashOverlay');
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingText = document.getElementById('loadingText');
const errorMessage = document.getElementById('errorMessage');
const errorText = document.getElementById('errorText');

let stream = null;
let facingMode = 'environment';
let isDetected = false;
let detectionStreak = 0;
let stableCount = 0;
let cvReady = false;
let detectionTimer = null;

// Inisialisasi Kamera HD
async function initCamera() {
    try {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        
        // Cek browser support
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Browser tidak mendukung kamera. Gunakan Chrome/Firefox terbaru di HP Anda.');
            console.error('getUserMedia not supported');
            return;
        }
        
        // HIGH DEFINITION CONSTRAINTS
        const constraints = {
            video: {
                facingMode: facingMode,
                width: { ideal: 1280 },  // HD Quality (fallback to 720p if 1080p not available)
                height: { ideal: 720 },
                frameRate: { ideal: 30 }
            },
            audio: false
        };
        
        console.log('Requesting camera access...');
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        console.log('Camera access granted!');
        
        video.srcObject = stream;
        video.play();
        
        // Set canvas size sesuai video
        video.addEventListener('loadedmetadata', () => {
            processingCanvas.width = video.videoWidth || 1280;
            processingCanvas.height = video.videoHeight || 720;
            console.log('Video loaded, starting detection...');
            startDetection();
        });
        
    } catch (error) {
        const errorMsg = 'Gagal akses kamera: ' + error.message + '. Pastikan izin kamera diberikan di pengaturan browser.';
        showError(errorMsg);
        console.error('Camera error:', error);
        
        // Show additional help
        setTimeout(() => {
            showError('Tips: Gunakan Chrome terbaru, beri izin kamera di Settings > Site Permissions');
        }, 3000);
    }
}

// Mulai deteksi (simplified version tanpa OpenCV untuk performa)
function startDetection() {
    detectionTimer = setInterval(() => {
        detectMotion();
    }, 100);
}

// Simple motion detection untuk stabilitas
function detectMotion() {
    try {
        const ctx = processingCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0, processingCanvas.width, processingCanvas.height);
        
        const imageData = ctx.getImageData(0, 0, processingCanvas.width, processingCanvas.height);
        const data = imageData.data;
        
        // Simple stability check (variance calculation)
        let variance = 0;
        const step = 20; // Check every 20th pixel for performance
        
        for (let i = 0; i < data.length; i += 4 * step) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const brightness = (r + g + b) / 3;
            
            if (brightness > 50 && brightness < 200) { // Mid-range brightness
                variance++;
            }
        }
        
        // If enough mid-range pixels, consider document detected
        if (variance > 100) {
            detectionStreak++;
            
            if (detectionStreak >= 3) {
                stableCount++;
                
                if (stableCount >= 15) {
                    if (!isDetected) {
                        setDetected(true);
                    }
                    
                    if (stableCount >= 20) {
                        captureImage();
                    }
                }
            }
        } else {
            detectionStreak = 0;
            stableCount = 0;
            
            if (isDetected) {
                setDetected(false);
            }
        }
        
    } catch (error) {
        console.error('Detection error:', error);
    }
}

// Set status detected
function setDetected(detected) {
    isDetected = detected;
    const container = document.querySelector('.z-50');
    
    if (detected) {
        container.classList.add('detected');
        captureBtn.disabled = false;
        mainText.textContent = 'Posisi Sempurna!';
        mainText.className = 'text-green-400 text-2xl font-bold drop-shadow-2xl';
        subText.textContent = 'Siap mengambil foto...';
        statusText.textContent = 'Nota Terdeteksi ✓';
    } else {
        container.classList.remove('detected');
        captureBtn.disabled = true;
        mainText.textContent = 'Smart Scanner HD';
        mainText.className = 'text-white text-2xl font-bold drop-shadow-2xl';
        subText.textContent = 'Posisikan nota di dalam bingkai';
        statusText.textContent = 'Mendeteksi...';
    }
}

// Ambil foto
function captureImage() {
    if (!isDetected) return;
    
    // Flash effect
    flashOverlay.style.opacity = '1';
    setTimeout(() => flashOverlay.style.opacity = '0', 100);
    
    // Stop detection
    if (detectionTimer) {
        clearInterval(detectionTimer);
    }
    
    // Process image
    processCapturedImage();
}

// Process gambar HD
async function processCapturedImage() {
    loadingOverlay.classList.remove('hidden');
    loadingText.textContent = 'Memproses gambar HD...';
    
    try {
        // 1. Crop ke area kotak
        const croppedCanvas = cropToFrame();
        
        // 2. Apply image processing HD
        const processedCanvas = await applyImageProcessingHD(croppedCanvas);
        
        // 3. Convert to base64
        const base64Data = processedCanvas.toDataURL('image/jpeg', 0.95);
        
        loadingText.textContent = 'Mengirim ke server OCR...';
        
        // 4. Send to OCR
        const response = await fetch("{{ route('ocr.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                image_base64: base64Data
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadingText.textContent = 'Selesai! Mengalihkan...';
            setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 1000);
        } else {
            loadingOverlay.classList.add('hidden');
            showError(result.message || 'Gagal memproses OCR');
            restartScanner();
        }
    } catch (error) {
        console.error('Processing error:', error);
        loadingOverlay.classList.add('hidden');
        showError('Terjadi kesalahan: ' + error.message);
        restartScanner();
    }
}

// Crop ke area kotak
function cropToFrame() {
    const frameWidth = processingCanvas.width * 0.85;
    const frameHeight = processingCanvas.height * 0.65;
    const startX = (processingCanvas.width - frameWidth) / 2;
    const startY = (processingCanvas.height - frameHeight) / 2;
    
    const cropCanvas = document.createElement('canvas');
    cropCanvas.width = frameWidth;
    cropCanvas.height = frameHeight;
    const cropCtx = cropCanvas.getContext('2d');
    
    cropCtx.drawImage(processingCanvas, 
        startX, startY, frameWidth, frameHeight,
        0, 0, frameWidth, frameHeight
    );
    
    return cropCanvas;
}

// Image Processing HD - Grayscale + Contrast Enhancement
async function applyImageProcessingHD(sourceCanvas) {
    const processCanvas = document.createElement('canvas');
    processCanvas.width = sourceCanvas.width;
    processCanvas.height = sourceCanvas.height;
    const processCtx = processCanvas.getContext('2d');
    
    processCtx.drawImage(sourceCanvas, 0, 0);
    
    const imageData = processCtx.getImageData(0, 0, processCanvas.width, processCanvas.height);
    const data = imageData.data;
    
    // 1. Grayscale Conversion
    for (let i = 0; i < data.length; i += 4) {
        const avg = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
        data[i] = avg;
        data[i + 1] = avg;
        data[i + 2] = avg;
    }
    
    // 2. Increase contrast (35%)
    const factor = (259 * (255 + 35)) / (255 * (259 - 35));
    for (let i = 0; i < data.length; i += 4) {
        data[i] = Math.min(255, Math.max(0, factor * (data[i] - 128) + 128));
        data[i + 1] = Math.min(255, Math.max(0, factor * (data[i + 1] - 128) + 128));
        data[i + 2] = Math.min(255, Math.max(0, factor * (data[i + 2] - 128) + 128));
    }
    
    // 3. Slight brightness boost (10%)
    for (let i = 0; i < data.length; i += 4) {
        data[i] = Math.min(255, data[i] + 10);
        data[i + 1] = Math.min(255, data[i + 1] + 10);
        data[i + 2] = Math.min(255, data[i + 2] + 10);
    }
    
    processCtx.putImageData(imageData, 0, 0);
    
    return processCanvas;
}

// Toggle camera
toggleBtn.addEventListener('click', () => {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    restartScanner();
});

// Capture button
captureBtn.addEventListener('click', captureImage);

// Restart scanner
function restartScanner() {
    if (detectionTimer) {
        clearInterval(detectionTimer);
    }
    initCamera();
}

// Show error
function showError(message) {
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    setTimeout(() => {
        errorMessage.classList.add('hidden');
    }, 5000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initCamera();
});
</script>
@endsection