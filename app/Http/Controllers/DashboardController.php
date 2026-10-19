<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsArticle; // Menggunakan nama model dari gambarmu
use App\Models\Stock;       // Menggunakan nama model dari gambarmu
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data menggunakan model berbahasa Inggris
        $daftar_berita = NewsArticle::latest()->get(); 
        $daftar_saham = Stock::all();

        // 2. Minta Python (AI) menganalisis setiap berita
        foreach ($daftar_berita as $berita) {
            try {
                // Catatan: Ganti 'isi_berita' jika kolom di databasemu berbahasa Inggris (misal: 'content' atau 'text')
                $teks = $berita->isi_berita ?? $berita->title ?? ''; 
                
                $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                    'teks' => $teks
                ]);
                
                if ($response->successful()) {
                    $berita->label_ai = $response->json()['sentimen'];
                } else {
                    $berita->label_ai = 'Netral';
                }
            } catch (\Exception $e) {
                $berita->label_ai = 'Offline';
            }
        }

        return view('dashboard', compact('daftar_berita', 'daftar_saham'));
    }
}