<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi StockSense AI
|--------------------------------------------------------------------------
*/

return [

    // Microservice Python (ai_service/app.py)
    'ai_service' => [
        'url'     => env('AI_SERVICE_URL', 'http://127.0.0.1:5000'),
        'timeout' => (int) env('AI_SERVICE_TIMEOUT', 60),
    ],

    // Sumber berita RSS (FR-05). Feed yang sudah dicek aktif per November 2026.
    'news_feeds' => [
        ['name' => 'CNBC Indonesia', 'url' => 'https://www.cnbcindonesia.com/market/rss'],
        ['name' => 'Antara',         'url' => 'https://www.antaranews.com/rss/ekonomi-bursa.xml'],
        ['name' => 'CNN Indonesia',  'url' => 'https://www.cnnindonesia.com/ekonomi/rss'],
        ['name' => 'Okezone',        'url' => 'https://sindikasi.okezone.com/index.php/rss/11/RSS2.0'],
    ],

    // Jadwal cron (ditampilkan juga di dashboard: Batch 1/2/3)
    'schedule' => [
        'news'   => env('NEWS_FETCH_CRON', '0 8,12,18 * * *'),
        'prices' => env('PRICE_FETCH_CRON', '30 16 * * 1-5'), // setelah bursa tutup, Senin-Jumat
    ],
];

