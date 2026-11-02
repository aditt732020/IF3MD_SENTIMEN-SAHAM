<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Penjadwalan otomatis (FR-05 & FR-06)
|--------------------------------------------------------------------------
| Jalankan scheduler saat development:  php artisan schedule:work
| Lihat daftar jadwal:                 php artisan schedule:list
*/

// Berita: Batch 1/2/3 -> 08:00, 12:00, 18:00 WIB
Schedule::command('news:fetch')
    ->cron(config('stocksense.schedule.news'))
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Harga saham: setelah bursa tutup, Senin-Jumat 16:30 WIB
Schedule::command('stocks:fetch-prices')
    ->cron(config('stocksense.schedule.prices'))
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
