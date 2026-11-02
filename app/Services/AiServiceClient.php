<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien HTTP untuk microservice Python (ai_service/app.py).
 * Semua komunikasi Laravel <-> Python lewat kelas ini.
 */
class AiServiceClient
{
    private function client(): PendingRequest
    {
        return Http::baseUrl(config('stocksense.ai_service.url'))
            ->timeout(config('stocksense.ai_service.timeout'))
            ->acceptJson()
            ->retry(2, 500, throw: false);
    }

    public function health(): ?array
    {
        try {
            $res = Http::baseUrl(config('stocksense.ai_service.url'))->timeout(3)->get('/health');
            return $res->successful() ? $res->json() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Klasifikasi sentimen banyak teks sekaligus (FR-07).
     *
     * @param  string[] $texts
     * @return array<int, array{label: string, confidence: float}>
     */
    public function predictBatch(array $texts): array
    {
        $results = [];

        foreach (array_chunk(array_values($texts), 100) as $chunk) {
            $res = $this->client()->post('/predict', ['texts' => $chunk]);

            if (! $res->successful()) {
                throw new RuntimeException('AI service /predict gagal: ' . ($res->json('message') ?? $res->status()));
            }

            array_push($results, ...$res->json('results'));
        }

        return $results;
    }

    /**
     * Data OHLCV harian dari Yahoo Finance via Python (FR-06).
     */
    public function prices(string $ticker, string $period = '5d'): array
    {
        $res = $this->client()->get('/prices/' . rawurlencode($ticker), ['period' => $period]);

        if (! $res->successful()) {
            throw new RuntimeException($res->json('message') ?? "HTTP {$res->status()}");
        }

        return $res->json('prices') ?? [];
    }

    /**
     * Harga terakhir beberapa emiten (live ticker dashboard).
     */
    public function quotes(array $tickers): array
    {
        $res = $this->client()->timeout(20)->get('/quotes', ['tickers' => implode(',', $tickers)]);

        return $res->successful() ? ($res->json('quotes') ?? []) : [];
    }
}

