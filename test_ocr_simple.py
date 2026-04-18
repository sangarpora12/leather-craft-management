import sys
import json
from paddleocr import PaddleOCR

# Disable unnecessary logs
import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
os.environ['PPOCR_LOG_LEVEL'] = 'ERROR'

def test_ocr(image_path):
    try:
        # Load PaddleOCR
        ocr = PaddleOCR(lang='en')

        # OCR - ignore deprecation warning, this is the correct method
        import warnings
        with warnings.catch_warnings():
            warnings.simplefilter("ignore", DeprecationWarning)
            result = ocr.ocr(image_path)

        if result and result[0]:
            texts = [line[1][0] for line in result[0]]
            print("Detected texts:")
            for text in texts:
                print(f"  - {text}")
            return texts
        else:
            print("No text detected")
            return []

    except Exception as e:
        print(f"Error: {e}")
        return []

if __name__ == "__main__":
    if len(sys.argv) > 1:
        test_ocr(sys.argv[1])
    else:
        print("Usage: python test_ocr_simple.py <image_path>")
