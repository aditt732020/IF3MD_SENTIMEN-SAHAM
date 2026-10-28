<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;

// Dashboard publik, bisa dibuka tanpa login
Route::get('/', [DashboardController::class, 'index'])
    ->name('public.dashboard');

// Mengarahkan /dashboard ke dashboard publik
Route::get('/dashboard', function () {
    return redirect()->route('public.dashboard');
});

// Halaman login admin
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');

// Memproses form login
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');

// Logout admin
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Dashboard admin hanya bisa dibuka setelah login
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware('auth')
    ->name('admin.dashboard');

// Tes koneksi ke layanan AI Python
Route::get('/test-ai', function () {
    $berita = "Penjualan perusahaan anjlok parah akibat krisis global.";

    $response = Http::post('http://127.0.0.1:5000/predict', [
        'teks' => $berita
    ]);

    return $response->json();
});
