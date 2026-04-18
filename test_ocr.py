import easyocr
import cv2

print("Testing EasyOCR...")

# Load image
img = cv2.imread('tes.jpeg')
if img is None:
    print("ERROR: Cannot load tes.jpeg")
    exit(1)

print("Image loaded, shape:", img.shape)

# Create reader
reader = easyocr.Reader(['id', 'en'])
print("Reader created")

# Run OCR
result = reader.readtext(img)
print("OCR completed, results:", len(result))

for i, (bbox, text, conf) in enumerate(result[:5]):  # Show first 5 results
    print(f"{i+1}. Text: '{text}' Confidence: {conf:.2f}")

if not result:
    print("No text detected")
else:
    print("SUCCESS: OCR working!")
