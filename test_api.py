import requests
import json

# Test API endpoint dengan file gambar
url = 'http://127.0.0.1:8000/api/ocr/scan'
image_path = 'tes.jpeg'

print('Testing API endpoint dengan file gambar...')

try:
    with open(image_path, 'rb') as f:
        files = {'image': f}
        response = requests.post(url, files=files)

    print(f'Status: {response.status_code}')
    print(f'Response: {response.text}')

    if response.status_code == 200:
        result = response.json()
        print('\nHasil OCR:')
        print(f'Supplier: {result.get("data", {}).get("supplier")}')
        print(f'Total: {result.get("data", {}).get("total")}')
        print(f'Raw text: {result.get("data", {}).get("raw_read", [])}')

except Exception as e:
    print(f'Error: {e}')
