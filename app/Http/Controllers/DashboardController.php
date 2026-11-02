<?php

namespace App\Http\Controllers;

use App\Models\ApiFetchLog;
use App\Models\NewsArticle;
use App\Models\NewsSentiment;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // FR-10 & NFR-04: Sanitasi input pencarian kode emiten (hanya huruf/angka, maks 10 karakter)
        $rawTicker = (string) $request->input('ticker', '');
        $searchTicker = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($rawTicker, 0, 10)));

        // FR-09: Metrik Harian
        $totalBeritaHariIni = NewsArticle::whereDate('created_at', today())->count();
        $totalBeritaAll     = NewsArticle::count();
        $totalPositif       = NewsSentiment::where('sentiment_label', 'positive')->count();
        $totalNegatif       = NewsSentiment::where('sentiment_label', 'negative')->count();
        $totalNetral        = NewsSentiment::where('sentiment_label', 'neutral')->count();
        $totalSentimen      = max(1, $totalPositif + $totalNegatif + $totalNetral);

        $pctPositif = (int) round(($totalPositif / $totalSentimen) * 100);
        $pctNegatif = (int) round(($totalNegatif / $totalSentimen) * 100);
        $pctNetral  = max(0, 100 - $pctPositif - $pctNegatif);

        // FR-11: Berita terkait emiten + sentimen
        $query = NewsArticle::with(['sentiments.stock'])->orderByDesc('published_at');
        if ($searchTicker !== '') {
            $query->whereHas('sentiments.stock', function ($q) use ($searchTicker) {
                $q->where('ticker_code', 'LIKE', '%' . $searchTicker . '%');
            });
        }
        $daftar_berita = $query->take(30)->get();

        // Daftar saham + 2 harga terakhir & sentimen dominan untuk live ticker header (FR-06)
        $daftar_saham = Stock::with([
            'stockPrices' => fn ($q) => $q->orderByDesc('price_date')->limit(2),
            'newsSentiments',
        ])->orderBy('ticker_code')->get();

        $saham_ticker = $daftar_saham->map(function (Stock $stock) {
            $prices = $stock->stockPrices;
            $latest = $prices->first();
            $prev   = $prices->skip(1)->first() ?? $latest;

            $close = $latest ? (float) $latest->close_price : null;
            $prevClose = $prev ? (float) $prev->close_price : null;
            $changePct = ($close !== null && $prevClose)
                ? round((($close - $prevClose) / $prevClose) * 100, 2)
                : 0.0;

            $pos = $stock->newsSentiments->where('sentiment_label', 'positive')->count();
            $neg = $stock->newsSentiments->where('sentiment_label', 'negative')->count();
            $mood = $pos > $neg ? 'bullish' : ($neg > $pos ? 'bearish' : 'neutral');

            return [
                'ticker_code' => $stock->ticker_code,
                'close_price' => $close,
                'change_pct'  => $changePct,
                'mood'        => $mood,
            ];
        });

        // FR-04: Log penarikan API & ringkasan eksekusi hari ini
        $recentLogs   = ApiFetchLog::orderByDesc('id')->take(6)->get();
        $lastSuccess  = ApiFetchLog::whereIn('status', ['success', 'partial'])->orderByDesc('finished_at')->first();
        $jobsToday    = ApiFetchLog::whereDate('created_at', today())->count();
        $jobsOkToday  = ApiFetchLog::whereDate('created_at', today())->whereIn('status', ['success', 'partial'])->count();

        return view('dashboard', compact(
            'daftar_berita',
            'daftar_saham',
            'saham_ticker',
            'totalBeritaHariIni',
            'totalBeritaAll',
            'pctPositif',
            'pctNegatif',
            'pctNetral',
            'searchTicker',
            'recentLogs',
            'lastSuccess',
            'jobsToday',
            'jobsOkToday'
        ));
    }

    /**
     * Tombol "Tarik Data Manual Sekarang" di dashboard (khusus Admin).
     */
    public function syncNow(): JsonResponse
    {
        if (! \Illuminate\Support\Facades\Auth::check() || \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Administrator yang dapat menjalankan sinkronisasi manual.');
        }

        $priceExit = Artisan::call('stocks:fetch-prices', ['--period' => '5d']);
        $newsExit  = Artisan::call('news:fetch');

        return response()->json([
            'status'  => ($priceExit === 0 && $newsExit === 0) ? 'success' : 'partial',
            'message' => 'Sinkronisasi harga & berita selesai.',
        ]);
    }
}
