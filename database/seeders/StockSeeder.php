<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Seeder;

/**
 * Daftar emiten awal yang dipantau (bisa diubah Admin lewat CRUD FR-03).
 * Aman dijalankan berulang kali karena memakai updateOrCreate.
 *
 * 'keywords' = alias nama yang sering muncul di judul berita, dipisah koma.
 * Hindari kata umum (contoh: "mandiri" saja) agar tidak salah cocok.
 *
 * Jalankan: php artisan db:seed --class=StockSeeder
 */
class StockSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = [
            ['ticker_code' => 'BBCA', 'company_name' => 'PT Bank Central Asia Tbk',               'sector' => 'Finance',                'keywords' => 'BCA, Bank Central Asia'],
            ['ticker_code' => 'BBRI', 'company_name' => 'PT Bank Rakyat Indonesia (Persero) Tbk', 'sector' => 'Finance',                'keywords' => 'BRI, Bank Rakyat Indonesia'],
            ['ticker_code' => 'BMRI', 'company_name' => 'PT Bank Mandiri (Persero) Tbk',          'sector' => 'Finance',                'keywords' => 'Bank Mandiri'],
            ['ticker_code' => 'TLKM', 'company_name' => 'PT Telkom Indonesia (Persero) Tbk',      'sector' => 'Infrastructure',         'keywords' => 'Telkom, Telkom Indonesia, Telkomsel'],
            ['ticker_code' => 'ASII', 'company_name' => 'PT Astra International Tbk',             'sector' => 'Industrials',            'keywords' => 'Astra International'],
            ['ticker_code' => 'GOTO', 'company_name' => 'PT GoTo Gojek Tokopedia Tbk',            'sector' => 'Technology',             'keywords' => 'GoTo, Gojek, Tokopedia'],
            ['ticker_code' => 'UNVR', 'company_name' => 'PT Unilever Indonesia Tbk',              'sector' => 'Consumer Non-Cyclicals', 'keywords' => 'Unilever'],
            ['ticker_code' => 'ANTM', 'company_name' => 'PT Aneka Tambang Tbk',                   'sector' => 'Basic Materials',        'keywords' => 'Antam, Aneka Tambang'],
            ['ticker_code' => 'ADRO', 'company_name' => 'PT Alamtri Resources Indonesia Tbk',     'sector' => 'Energy',                 'keywords' => 'Adaro, Alamtri'],
            ['ticker_code' => 'ICBP', 'company_name' => 'PT Indofood CBP Sukses Makmur Tbk',      'sector' => 'Consumer Non-Cyclicals', 'keywords' => 'Indofood CBP'],
        ];

        foreach ($stocks as $stock) {
            Stock::updateOrCreate(
                ['ticker_code' => $stock['ticker_code']],
                $stock
            );
        }
    }
}
