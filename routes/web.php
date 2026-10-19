<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\DashboardController; // <-- Pastikan Controller diimpor di sini

Route::get('/', function () {
    return view('welcome');
});

// GANTI BAGIAN INI: 
// Sebelumnya pakai function(), sekarang kita arahkan ke DashboardController
Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/test-ai', function () {
    $berita = "Penjualan perusahaan anjlok parah akibat krisis global.";
    
    $response = Http::post('http://127.0.0.1:5000/predict', [
        'teks' => $berita
    ]);

    return $response->json();
});