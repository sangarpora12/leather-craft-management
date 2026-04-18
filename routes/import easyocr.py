import easyocr
import cv2
import numpy as np
import sys
import json
import os
from PIL import Image

# Matikan log yang mengganggu
os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"

def preprocess_image(image_path):
    """
    FITUR 1: PRE-PROCESSING CANGGIH
    Mengubah gambar jelek menjadi tajam, lurus, dan hitam-putih sempurna.
    """
    # Baca gambar
    img = cv2.imread(image_path)
    
    # --- FITUR A: AUTO-ROTATE (Berdasarkan rasio dimensi) ---
    # Jika tinggi > lebar (Potrait), kemungkinan besar nota miring 90 derajat
    # Kita putar paksa jadi Landscape
    h, w = img.shape[:2]
    if h > w:
        img = cv2.rotate(img, cv2.ROTATE_90_CLOCKWISE)
    
    # Ubah ke Grayscale (Abu-abu)
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # --- FITUR B: SHADOW REMOVER (Adaptive Threshold) ---
    # Ini ajaib buat nota lecek. Dia menghitung cahaya per area, bukan global.
    # Hasilnya: Background putih bersih, tulisan hitam.
    binary = cv2.adaptiveThreshold(
        gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 31, 15
    )

    # --- FITUR C: DOT-MATRIX CONNECTOR (Dilation & Erosion) ---
    # Khusus nota dot matrix, kita "tebalkan" titik-titiknya biar nyambung
    kernel = np.ones((1, 1), np.uint8) # Ukuran penebalan (bisa diubah jadi 2,2 jika perlu)
    processed = cv2.dilate(binary, kernel, iterations=1)
    processed = cv2.erode(processed, kernel, iterations=1)

    # Simpan gambar hasil olahan (untuk debug/cek mata)
    temp_processed_path = image_path.replace('.', '_proc.')
    cv2.imwrite(temp_processed_path, processed)
    
    return temp_processed_path

def scan_receipt(image_path):
    try:
        # 1. Jalankan Pre-processing dulu
        clean_image_path = preprocess_image(image_path)

        # 2. Inisialisasi OCR (English + Indo)
        reader = easyocr.Reader(['id', 'en'], gpu=False, verbose=False)

        # 3. Baca Teks dengan Mode Akurat (Paragraph=False)
        results = reader.readtext(clean_image_path, detail=0, paragraph=False, y_ths=0.3)
        
        # 4. Parsing Data Cerdas
        extracted = {
            "items": [],
            "supplier": "",
            "total_detected": 0,
            "raw_text": results
        }

        max_price = 0
        
        for line in results:
            # Bersihkan teks (Hanya huruf, angka, titik, koma, spasi)
            clean_line = ''.join(e for e in line if e.isalnum() or e in ['.', ',', ' ', '/', '-']).strip()
            
            # --- FITUR D: SMART NUMBER PARSER ---
            # Cari angka yang formatnya mirip uang (misal: 50.000 atau 50,000)
            # Regex manual: Cari angka yang diakhiri 3 digit (ribuan)
            words = clean_line.split()
            for word in words:
                # Hapus simbol mata uang & tanda baca di ujung
                clean_word = word.replace('Rp', '').replace('rp', '').replace(';', '').replace(':', '')
                # Cek apakah isinya angka
                numeric_part = clean_word.replace('.', '').replace(',', '')
                
                if numeric_part.isdigit():
                    val = int(numeric_part)
                    # Filter: Harga biasanya > 500 perak dan < 100 Juta
                    if 500 < val < 100000000:
                        # Cek apakah ini total? (Angka terbesar biasanya total)
                        if val > max_price:
                            max_price = val
                        
                        # Simpan sebagai item kandidat
                        # Asumsi: Teks di sebelah kiri angka adalah nama barang
                        extracted['items'].append({
                            "line_text": clean_line,
                            "value": val
                        })

        # Ambil harga terbesar sebagai total
        extracted['total_detected'] = max_price
        
        # Cari supplier (Baris paling atas yang panjang dan bukan angka)
        for line in results[:4]: # Cek 4 baris pertama
            if len(line) > 5 and not any(char.isdigit() for char in line):
                extracted['supplier'] = line
                break

        # Hapus file temp
        try:
            os.remove(clean_image_path)
        except:
            pass

        print(json.dumps(extracted))

    except Exception as e:
        print(json.dumps({"error": str(e)}))

if __name__ == "__main__":
    if len(sys.argv) > 1:
        scan_receipt(sys.argv[1])
    else:
        print(json.dumps({"error": "No image provided"}))