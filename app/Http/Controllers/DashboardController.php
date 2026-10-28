<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\Stock;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data berita dan saham dari database
        $daftar_berita = NewsArticle::latest()->get();
        $daftar_saham = Stock::all();

        // Menganalisis sentimen setiap berita menggunakan layanan Python
        foreach ($daftar_berita as $berita) {
            try {
                $teks = $berita->isi_berita ?? $berita->title ?? '';

                $response = Http::timeout(5)->post(
                    'http://127.0.0.1:5000/predict',
                    ['teks' => $teks]
                );

                if ($response->successful()) {
                    $berita->label_ai = $response->json()['sentimen'] ?? 'Netral';
                } else {
                    $berita->label_ai = 'Netral';
                }
            } catch (\Exception $e) {
                $berita->label_ai = 'Offline';
            }
        }

        // Menampilkan data pada dashboard admin
        return view('user.dashboard', compact('daftar_berita', 'daftar_saham'));
    }
}
