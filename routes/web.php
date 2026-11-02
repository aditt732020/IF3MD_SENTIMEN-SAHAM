<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ApiLogController;

// =============================================
// Halaman Utama -> arahkan ke Dashboard (atau Login jika belum autentikasi)
// =============================================
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =============================================
// Autentikasi - FR-01
// =============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// =============================================
// Seluruh fitur wajib login terlebih dahulu (middleware 'auth')
// =============================================
Route::middleware('auth')->group(function () {
    // Bisa diakses Admin & Visitor yang sudah login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/emiten', [StockController::class, 'emitenDetail'])->name('emiten.detail');

    // Khusus Administrator (FR-02, FR-03, FR-04)
    Route::post('/dashboard/sync', [DashboardController::class, 'syncNow'])->name('dashboard.sync');

    Route::get('/dashboard/master-emiten', [StockController::class, 'index'])->name('master.emiten');
    Route::post('/dashboard/master-emiten', [StockController::class, 'store'])->name('master.emiten.store');
    Route::put('/dashboard/master-emiten/{stock}', [StockController::class, 'update'])->name('master.emiten.update');
    Route::delete('/dashboard/master-emiten/{stock}', [StockController::class, 'destroy'])->name('master.emiten.destroy');

    Route::get('/dashboard/logs', [ApiLogController::class, 'index'])->name('api.logs');
    Route::post('/dashboard/logs/trigger', [ApiLogController::class, 'trigger'])->name('api.logs.trigger');
});

// =============================================
// Test AI (dev only)
// =============================================
Route::get('/test-ai', function () {
    $berita = "Penjualan perusahaan anjlok parah akibat krisis global.";

    $response = Http::post('http://127.0.0.1:5000/predict', [
        'teks' => $berita
    ]);

    return $response->json();
});
