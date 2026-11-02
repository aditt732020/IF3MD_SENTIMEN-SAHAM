<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk:
 * - Halaman Emiten & Berita Terkait + Grafik OHLC (FR-06, FR-10, FR-11)
 * - Halaman Master Data Emiten CRUD khusus Admin (FR-02, FR-03, NFR-04)
 */
class StockController extends Controller
{
    /**
     * Helper data live ticker di topbar agar konsisten di semua halaman.
     */
    public static function buildTickerBar(): \Illuminate\Support\Collection
    {
        return Stock::with([
            'stockPrices' => fn ($q) => $q->orderByDesc('price_date')->limit(2),
            'newsSentiments',
        ])->orderBy('ticker_code')->get()->map(function (Stock $stock) {
            $prices    = $stock->stockPrices;
            $latest    = $prices->first();
            $prev      = $prices->skip(1)->first() ?? $latest;
            $close     = $latest ? (float) $latest->close_price : null;
            $prevClose = $prev ? (float) $prev->close_price : null;
            $changePct = ($close !== null && $prevClose)
                ? round((($close - $prevClose) / $prevClose) * 100, 2)
                : 0.0;

            $pos  = $stock->newsSentiments->where('sentiment_label', 'positive')->count();
            $neg  = $stock->newsSentiments->where('sentiment_label', 'negative')->count();
            $mood = $pos > $neg ? 'bullish' : ($neg > $pos ? 'bearish' : 'neutral');

            return [
                'ticker_code' => $stock->ticker_code,
                'close_price' => $close,
                'change_pct'  => $changePct,
                'mood'        => $mood,
            ];
        });
    }

    /**
     * FR-06, FR-10, FR-11: Halaman Detail Emiten, Grafik OHLC, dan Tabel Detail Berita.
     */
    public function emitenDetail(Request $request)
    {
        $rawTicker    = (string) $request->input('ticker', '');
        $searchTicker = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($rawTicker, 0, 10)));

        $stocks = Stock::withCount('newsSentiments')->orderBy('ticker_code')->get();
        $selectedStock = $searchTicker !== ''
            ? $stocks->firstWhere('ticker_code', $searchTicker) ?? $stocks->first(fn ($s) => str_contains($s->ticker_code, $searchTicker))
            : $stocks->first();

        $prices   = collect();
        $articles = collect();
        $sentimentSummary = ['positive' => 0, 'negative' => 0, 'neutral' => 0];

        if ($selectedStock) {
            $prices = $selectedStock->stockPrices()
                ->orderBy('price_date')
                ->take(30)
                ->get();

            $articles = NewsArticle::with(['sentiments.stock'])
                ->whereHas('sentiments', fn ($q) => $q->where('stock_id', $selectedStock->id))
                ->orderByDesc('published_at')
                ->get();

            foreach ($selectedStock->newsSentiments as $s) {
                $sentimentSummary[$s->sentiment_label] = ($sentimentSummary[$s->sentiment_label] ?? 0) + 1;
            }
        }

        $saham_ticker = self::buildTickerBar();

        return view('emiten', compact(
            'stocks',
            'selectedStock',
            'prices',
            'articles',
            'sentimentSummary',
            'searchTicker',
            'saham_ticker'
        ));
    }

    /**
     * FR-03: Halaman Master Data Emiten (CRUD) — khusus Administrator (FR-02).
     */
    public function index()
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $stocks = Stock::withCount(['stockPrices', 'newsSentiments'])
            ->with(['stockPrices' => fn ($q) => $q->orderByDesc('price_date')->limit(1)])
            ->orderBy('ticker_code')
            ->get();

        $saham_ticker = self::buildTickerBar();

        return view('master_emiten', compact('stocks', 'saham_ticker'));
    }

    /**
     * Pastikan hanya Admin yang bisa mengubah master data (FR-02).
     */
    private function authorizeAdmin(): void
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Administrator yang dapat mengubah Master Data Emiten.');
        }
    }

    /**
     * FR-03: Tambah emiten baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'ticker_code'  => ['required', 'string', 'regex:/^[A-Za-z]{4}$/', 'unique:stocks,ticker_code'],
            'company_name' => ['required', 'string', 'max:150'],
            'sector'       => ['nullable', 'string', 'max:100'],
            'keywords'     => ['nullable', 'string', 'max:255'],
        ], [
            'ticker_code.regex'  => 'Kode emiten harus 4 huruf (contoh: BBCA).',
            'ticker_code.unique' => 'Kode emiten sudah terdaftar.',
        ]);

        $stock = Stock::create([
            'ticker_code'  => strtoupper(trim($data['ticker_code'])),
            'company_name' => strip_tags(trim($data['company_name'])),
            'sector'       => $data['sector'] ? strip_tags(trim($data['sector'])) : null,
            'keywords'     => $data['keywords'] ? strip_tags(trim($data['keywords'])) : null,
        ]);

        // Coba tarik harga awal secara otomatis
        try {
            Artisan::call('stocks:fetch-prices', ['--ticker' => $stock->ticker_code, '--period' => '1mo']);
        } catch (\Throwable) {
            // abaikan jika AI service sedang mati
        }

        return redirect()->route('master.emiten')
            ->with('success', "Emiten {$stock->ticker_code} ({$stock->company_name}) berhasil ditambahkan.");
    }

    /**
     * FR-03: Ubah data emiten.
     */
    public function update(Request $request, Stock $stock): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'ticker_code'  => ['required', 'string', 'regex:/^[A-Za-z]{4}$/', 'unique:stocks,ticker_code,' . $stock->id],
            'company_name' => ['required', 'string', 'max:150'],
            'sector'       => ['nullable', 'string', 'max:100'],
            'keywords'     => ['nullable', 'string', 'max:255'],
        ]);

        $stock->update([
            'ticker_code'  => strtoupper(trim($data['ticker_code'])),
            'company_name' => strip_tags(trim($data['company_name'])),
            'sector'       => $data['sector'] ? strip_tags(trim($data['sector'])) : null,
            'keywords'     => $data['keywords'] ? strip_tags(trim($data['keywords'])) : null,
        ]);

        return redirect()->route('master.emiten')
            ->with('success', "Emiten {$stock->ticker_code} berhasil diperbarui.");
    }

    /**
     * FR-03: Hapus emiten.
     */
    public function destroy(Stock $stock): RedirectResponse
    {
        $this->authorizeAdmin();

        $code = $stock->ticker_code;
        $stock->delete();

        return redirect()->route('master.emiten')
            ->with('success', "Emiten {$code} beserta riwayat harga & sentimennya berhasil dihapus.");
    }
}

