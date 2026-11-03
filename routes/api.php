<?php

use App\Http\Controllers\API\RestController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [RestController::class, 'user']);
    Route::post('/gantipassword', [RestController::class, 'gantipassword']);
    Route::post('/komoditi', [RestController::class, 'komoditi']);
    Route::post('/komoditi/update', [RestController::class, 'updateKomoditi']);
});

Route::post('/login', [RestController::class, 'login']);

// Info Harga API - Public endpoints
Route::get('/info-harga/pasar', [RestController::class, 'listPasar']);
Route::get('/info-harga/bahan', [RestController::class, 'infoHargaBahan']);
Route::get('/info-harga/tanggal', [RestController::class, 'infoHargaTanggal']);
Route::get('/info-harga/periode', [RestController::class, 'infoHargaTanggal']);
Route::get('/info-harga/riwayat', [RestController::class, 'riwayatHarga'])->name('api.info-harga.riwayat');
Route::get('/info-harga', [RestController::class, 'infoHarga']);

// Riwayat Perubahan Harga API - Public endpoint alias
Route::get('/riwayat-harga', [RestController::class, 'riwayatHarga'])->name('api.riwayat-harga');

// Pasar API - Public endpoint
Route::get('/pasar', [RestController::class, 'pasar']);

// Bahan API - Public endpoint
Route::get('/info-bahan', [RestController::class, 'infoBahan']);
Route::get('/bahan', [RestController::class, 'infoBahan']);
