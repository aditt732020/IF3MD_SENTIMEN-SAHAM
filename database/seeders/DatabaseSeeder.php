<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\StockPrice;
use App\Models\NewsArticle;
use App\Models\NewsSentiment;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Data Emiten (Saham)
        $bbca = Stock::create([
            'ticker_code' => 'BBCA',
            'company_name' => 'PT Bank Central Asia Tbk',
            'sector' => 'Finance'
        ]);

        $goto = Stock::create([
            'ticker_code' => 'GOTO',
            'company_name' => 'PT GoTo Gojek Tokopedia Tbk',
            'sector' => 'Technology'
        ]);

        // 2. Buat Data Riwayat Harga Saham (5 Hari Terakhir)
        $today = Carbon::now();
        for ($i = 4; $i >= 0; $i--) {
            StockPrice::create([
                'stock_id' => $bbca->id,
                'price_date' => $today->copy()->subDays($i)->format('Y-m-d'),
                'open_price' => rand(9500, 9800),
                'close_price' => rand(9600, 10000),
                'volume' => rand(10000000, 50000000)
            ]);

            StockPrice::create([
                'stock_id' => $goto->id,
                'price_date' => $today->copy()->subDays($i)->format('Y-m-d'),
                'open_price' => rand(50, 60),
                'close_price' => rand(55, 70),
                'volume' => rand(100000000, 500000000)
            ]);
        }

        // 3. Buat Data Artikel Berita
        $news1 = NewsArticle::create([
            'title' => 'Laba Bersih BBCA Kuartal Ini Tembus Rekor Baru',
            'content' => 'Bank Central Asia berhasil mencetak laba bersih yang melampaui ekspektasi pasar, didorong oleh pertumbuhan kredit yang solid.',
            'source_url' => 'https://finance.example.com/bbca-laba-rekor',
            'published_at' => $today->copy()->subDays(2)
        ]);

        $news2 = NewsArticle::create([
            'title' => 'GOTO Rilis Fitur Baru, Namun Bakar Uang Masih Jadi Sorotan',
            'content' => 'GOTO terus berinovasi dengan layanan baru di ekosistemnya, namun investor masih khawatir dengan profitabilitas jangka panjang.',
            'source_url' => 'https://finance.example.com/goto-fitur-bakar-uang',
            'published_at' => $today->copy()->subDays(1)
        ]);

        $news3 = NewsArticle::create([
            'title' => 'IHSG Ditutup Menguat Tipis, Sektor Perbankan Jadi Penopang',
            'content' => 'Pergerakan pasar hari ini diwarnai oleh aksi beli asing pada saham perbankan besar seperti BBCA, sementara sektor teknologi bergerak variatif.',
            'source_url' => 'https://finance.example.com/ihsg-menguat-perbankan',
            'published_at' => $today->copy()
        ]);

        // 4. Buat Data Sentimen AI (Relasi Berita & Saham)
        NewsSentiment::create([
            'news_article_id' => $news1->id,
            'stock_id' => $bbca->id,
            'sentiment_label' => 'positive',
            'confidence_score' => 0.985
        ]);

        NewsSentiment::create([
            'news_article_id' => $news2->id,
            'stock_id' => $goto->id,
            'sentiment_label' => 'neutral',
            'confidence_score' => 0.650
        ]);

        NewsSentiment::create([
            'news_article_id' => $news3->id,
            'stock_id' => $bbca->id,
            'sentiment_label' => 'positive',
            'confidence_score' => 0.890
        ]);
        
        NewsSentiment::create([
            'news_article_id' => $news3->id,
            'stock_id' => $goto->id,
            'sentiment_label' => 'neutral',
            'confidence_score' => 0.550
        ]);
    }
}