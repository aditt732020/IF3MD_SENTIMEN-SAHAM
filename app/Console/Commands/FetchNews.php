<?php

namespace App\Console\Commands;

use App\Models\ApiFetchLog;
use App\Models\NewsArticle;
use App\Models\NewsSentiment;
use App\Models\Stock;
use App\Services\AiServiceClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * FR-05: Menarik berita dari RSS secara terjadwal.
 * FR-07: Mengklasifikasikan sentimen via AI service.
 * FR-08: Menyimpan relasi berita - emiten - sentimen - waktu rilis.
 *
 * Hanya berita yang menyebut minimal satu emiten yang dipantau yang disimpan.
 *
 * Contoh: php artisan news:fetch
 */
class FetchNews extends Command
{
    protected $signature = 'news:fetch {--dry-run : Tampilkan hasil tanpa menyimpan ke database}';

    protected $description = 'Menarik berita pasar dari RSS, mencocokkan emiten, lalu analisis sentimen';

    public function handle(AiServiceClient $ai): int
    {
        $stocks = Stock::all();
        if ($stocks->isEmpty()) {
            $this->warn('Belum ada emiten. Jalankan: php artisan db:seed --class=StockSeeder');
            return self::FAILURE;
        }

        if (! $ai->health()) {
            $this->error('AI service tidak aktif di ' . config('stocksense.ai_service.url') . '. Jalankan: python ai_service/app.py');
            ApiFetchLog::start('news', 'RSS Aggregator')->finish('failed', 0, 'AI service tidak dapat dihubungi');
            return self::FAILURE;
        }

        $matchers = $this->buildMatchers($stocks);
        $log = ApiFetchLog::start('news', 'RSS Aggregator');
        $failedFeeds = [];
        $candidates = collect();

        // 1. Ambil semua feed
        foreach (config('stocksense.news_feeds') as $feed) {
            try {
                $items = $this->fetchFeed($feed['url']);
                $this->line(sprintf('  <info>✔</info> %-15s %d item', $feed['name'], count($items)));

                foreach ($items as $item) {
                    $tickers = $this->matchStocks($item['title'] . ' ' . $item['content'], $matchers);
                    if ($tickers) {
                        $candidates->push($item + ['source_name' => $feed['name'], 'stock_ids' => $tickers]);
                    }
                }
            } catch (\Throwable $e) {
                $failedFeeds[] = $feed['name'];
                $this->line(sprintf('  <error>✘</error> %-15s %s', $feed['name'], Str::limit($e->getMessage(), 80)));
            }
        }

        // 2. Buang berita yang sudah pernah disimpan
        $candidates = $candidates->unique('source_url');
        $existing = NewsArticle::whereIn('source_url', $candidates->pluck('source_url'))->pluck('source_url')->all();
        $newItems = $candidates->reject(fn ($i) => in_array($i['source_url'], $existing, true))->values();

        $this->info(sprintf('%d berita relevan, %d baru.', $candidates->count(), $newItems->count()));

        // 3. Klasifikasi sentimen + simpan
        $saved = 0;
        if ($newItems->isNotEmpty()) {
            try {
                $predictions = $ai->predictBatch($newItems->pluck('title')->all());
                $saved = $this->store($newItems, $predictions, $stocks);
            } catch (\Throwable $e) {
                $log->finish('failed', 0, 'Analisis sentimen gagal: ' . $e->getMessage());
                $this->error($e->getMessage());
                return self::FAILURE;
            }
        }

        $feedCount = count(config('stocksense.news_feeds'));
        $status = match (true) {
            empty($failedFeeds)                 => 'success',
            count($failedFeeds) < $feedCount    => 'partial',
            default                             => 'failed',
        };
        $message = sprintf('%d/%d feed OK, %d berita relevan, %d berita baru disimpan',
            $feedCount - count($failedFeeds), $feedCount, $candidates->count(), $saved);
        if ($failedFeeds) {
            $message .= '. Feed gagal: ' . implode(', ', $failedFeeds);
        }
        if ($this->option('dry-run')) {
            $message = '[DRY RUN] ' . $message;
        }

        $log->finish($status, $saved, $message);
        $this->info("Selesai [{$status}] — {$message}");

        return $status === 'failed' ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Ambil & parse RSS menjadi array item.
     */
    private function fetchFeed(string $url): array
    {
        $res = Http::timeout(20)
            ->withUserAgent('Mozilla/5.0 (compatible; StockSenseBot/1.0)')
            ->get($url);

        if (! $res->successful()) {
            throw new \RuntimeException("HTTP {$res->status()}");
        }

        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($res->body(), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);
        libxml_use_internal_errors($previous);

        if ($xml === false || ! isset($xml->channel->item)) {
            throw new \RuntimeException('Format RSS tidak valid');
        }

        $items = [];
        foreach ($xml->channel->item as $item) {
            $title = $this->cleanText((string) $item->title);
            $link = trim((string) $item->link);
            if ($title === '' || ! filter_var($link, FILTER_VALIDATE_URL)) {
                continue;
            }

            try {
                $published = Carbon::parse((string) $item->pubDate)->setTimezone(config('app.timezone'));
            } catch (\Throwable) {
                $published = now();
            }

            $content = $this->cleanText((string) $item->description);

            $items[] = [
                'title'        => Str::limit($title, 250, ''),
                'content'      => $content !== '' ? $content : $title,
                'source_url'   => Str::limit($link, 250, ''),
                'published_at' => $published,
            ];
        }

        return $items;
    }

    private function cleanText(string $text): string
    {
        // Decode dua kali karena beberapa RSS memakai double-encoded entity (&amp;nbsp;)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * Buat regex per emiten dari kode saham (case-sensitive) + keywords (case-insensitive).
     *
     * @return array<int, array{0: string, 1: ?string}>  [stock_id => [regexKode, regexKeyword]]
     */
    private function buildMatchers(Collection $stocks): array
    {
        $matchers = [];
        foreach ($stocks as $stock) {
            $keywords = collect(explode(',', (string) $stock->keywords))
                ->map(fn ($k) => trim($k))
                ->filter()
                ->map(fn ($k) => preg_quote($k, '/'));

            $matchers[$stock->id] = [
                '/\b' . preg_quote($stock->ticker_code, '/') . '\b/u',
                $keywords->isNotEmpty() ? '/\b(' . $keywords->implode('|') . ')\b/iu' : null,
            ];
        }

        return $matchers;
    }

    /** @return int[] stock_id yang disebut dalam teks */
    private function matchStocks(string $text, array $matchers): array
    {
        $ids = [];
        foreach ($matchers as $stockId => [$codeRegex, $keywordRegex]) {
            if (preg_match($codeRegex, $text) || ($keywordRegex && preg_match($keywordRegex, $text))) {
                $ids[] = $stockId;
            }
        }

        return $ids;
    }

    private function store(Collection $items, array $predictions, Collection $stocks): int
    {
        $tickerById = $stocks->pluck('ticker_code', 'id');

        foreach ($items as $i => $item) {
            $pred = $predictions[$i];
            $tickers = collect($item['stock_ids'])->map(fn ($id) => $tickerById[$id])->implode(',');
            $this->line(sprintf('  [%-8s %.2f] %-14s %s',
                $pred['label'], $pred['confidence'], $tickers, Str::limit($item['title'], 70)));
        }

        if ($this->option('dry-run')) {
            return 0;
        }

        return DB::transaction(function () use ($items, $predictions) {
            foreach ($items as $i => $item) {
                $article = NewsArticle::create([
                    'title'        => $item['title'],
                    'content'      => $item['content'],
                    'source_url'   => $item['source_url'],
                    'source_name'  => $item['source_name'],
                    'published_at' => $item['published_at'],
                ]);

                foreach ($item['stock_ids'] as $stockId) {
                    NewsSentiment::create([
                        'news_article_id'  => $article->id,
                        'stock_id'         => $stockId,
                        'sentiment_label'  => $predictions[$i]['label'],
                        'confidence_score' => $predictions[$i]['confidence'],
                    ]);
                }
            }

            return $items->count();
        });
    }
}

