<?php

namespace App\Console\Commands;

use App\Models\ApiFetchLog;
use App\Models\Stock;
use App\Models\StockPrice;
use App\Services\AiServiceClient;
use Illuminate\Console\Command;

/**
 * FR-06: Menarik data OHLC + Volume harian semua emiten yang dipantau.
 *
 * Contoh:
 *   php artisan stocks:fetch-prices
 *   php artisan stocks:fetch-prices --ticker=BBCA --period=1mo
 */
class FetchStockPrices extends Command
{
    protected $signature = 'stocks:fetch-prices
                            {--ticker= : Hanya ambil satu kode emiten}
                            {--period=5d : Rentang data (1d,5d,1mo,3mo,6mo,1y)}';

    protected $description = 'Menarik data harga saham harian (OHLCV) dari Yahoo Finance via AI service';

    public function handle(AiServiceClient $ai): int
    {
        $period = (string) $this->option('period');
        $query = Stock::orderBy('ticker_code');

        if ($ticker = $this->option('ticker')) {
            $query->where('ticker_code', strtoupper(trim($ticker)));
        }

        $stocks = $query->get();
        if ($stocks->isEmpty()) {
            $this->warn('Tidak ada emiten yang cocok. Jalankan: php artisan db:seed --class=StockSeeder');
            return self::FAILURE;
        }

        $log = ApiFetchLog::start('price', 'Yahoo Finance');
        $saved = 0;
        $failed = [];

        foreach ($stocks as $stock) {
            try {
                $rows = collect($ai->prices($stock->ticker_code, $period))->map(fn ($p) => [
                    'stock_id'    => $stock->id,
                    'price_date'  => $p['date'],
                    'open_price'  => $p['open'],
                    'high_price'  => $p['high'],
                    'low_price'   => $p['low'],
                    'close_price' => $p['close'],
                    'volume'      => $p['volume'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ])->all();

                // Upsert: harga di tanggal yang sama diperbarui, bukan diduplikasi (NFR-03)
                StockPrice::upsert(
                    $rows,
                    ['stock_id', 'price_date'],
                    ['open_price', 'high_price', 'low_price', 'close_price', 'volume', 'updated_at']
                );

                $saved += count($rows);
                $this->line(sprintf('  <info>✔</info> %-5s %d hari', $stock->ticker_code, count($rows)));
            } catch (\Throwable $e) {
                $failed[] = $stock->ticker_code;
                $this->line(sprintf('  <error>✘</error> %-5s %s', $stock->ticker_code, $e->getMessage()));
            }
        }

        $status = match (true) {
            empty($failed)                    => 'success',
            count($failed) < $stocks->count() => 'partial',
            default                           => 'failed',
        };

        $message = sprintf('%d/%d emiten berhasil', $stocks->count() - count($failed), $stocks->count());
        if ($failed) {
            $message .= '. Gagal: ' . implode(', ', $failed);
        }

        $log->finish($status, $saved, $message);
        $this->info("Selesai [{$status}] — {$saved} baris harga disimpan. {$message}");

        return $status === 'failed' ? self::FAILURE : self::SUCCESS;
    }
}

