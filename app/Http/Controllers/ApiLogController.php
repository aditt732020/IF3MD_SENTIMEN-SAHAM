<?php

namespace App\Http\Controllers;

use App\Models\ApiFetchLog;
use App\Services\AiServiceClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/**
 * FR-04: Halaman Log Status Penarikan API & Cron Sync.
 */
class ApiLogController extends Controller
{
    public function index(Request $request, AiServiceClient $ai)
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $type   = $request->input('type');
        $status = $request->input('status');

        $query = ApiFetchLog::orderByDesc('id');
        if (in_array($type, ['news', 'price', 'sentiment'], true)) {
            $query->where('source_type', $type);
        }
        if (in_array($status, ['success', 'partial', 'failed', 'running'], true)) {
            $query->where('status', $status);
        }

        $logs         = $query->limit(50)->get();
        $totalLogs    = ApiFetchLog::count();
        $successLogs  = ApiFetchLog::where('status', 'success')->count();
        $failedLogs   = ApiFetchLog::where('status', 'failed')->count();
        $aiHealth     = $ai->health();
        $saham_ticker = StockController::buildTickerBar();
        $feeds        = config('stocksense.news_feeds');

        return view('api_logs', compact(
            'logs',
            'totalLogs',
            'successLogs',
            'failedLogs',
            'aiHealth',
            'saham_ticker',
            'feeds',
            'type',
            'status'
        ));
    }

    /**
     * Eksekusi manual oleh Admin dari halaman Log API.
     */
    public function trigger(Request $request): RedirectResponse
    {
        if (! Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Administrator yang dapat menjalankan penarikan manual.');
        }

        $target = $request->input('target', 'all');

        if ($target === 'prices' || $target === 'all') {
            Artisan::call('stocks:fetch-prices', ['--period' => '5d']);
        }
        if ($target === 'news' || $target === 'all') {
            Artisan::call('news:fetch');
        }

        return redirect()->route('api.logs')
            ->with('success', 'Penarikan API (' . strtoupper($target) . ') selesai dijalankan. Lihat baris teratas log.');
    }
}

