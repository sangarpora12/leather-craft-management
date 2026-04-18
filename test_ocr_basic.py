#!/usr/bin/env python3
"""
Simple test script to verify Python execution from Laravel
"""
import sys
import json

if __name__ == "__main__":
    # Return basic JSON response
    response = {
        "data": [{"Item": "Test Item", "Qty": "1", "Harga": "10000", "Total": "10000"}],
        "total_items": 1,
        "method": "test_mode",
        "status": "success",
        "message": "Python execution successful"
    }
    print(json.dumps(response))
