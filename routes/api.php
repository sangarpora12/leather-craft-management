<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\Api\SatuanController;
use App\Http\Controllers\Api\BahanController;
use App\Http\Controllers\Api\KategoriController;

// OCR Route
Route::post('/ocr/scan', [OcrController::class, 'scan'])->name('api.ocr.scan');

// Satuan API Route
Route::get('/satuans', [SatuanController::class, 'index']);

// Kategori API Route
Route::get('/kategoris', [KategoriController::class, 'index']);

// Bahan API Routes
Route::prefix('bahan')->group(function () {
    Route::get('search', [BahanController::class, 'search']);
    Route::post('generate-kode-bahan', [BahanController::class, 'generateKodeBahan']);
    Route::post('manual', [BahanController::class, 'manualInput']);
});