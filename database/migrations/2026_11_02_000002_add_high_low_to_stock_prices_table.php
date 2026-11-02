<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FR-06: Sistem harus menarik data Open, High, Low, Close (OHLC) dan Volume.
 * Tabel awal belum punya kolom high & low, jadi ditambahkan di sini.
 * Unique index mencegah harga di tanggal yang sama tersimpan dobel
 * saat cron job dijalankan berulang kali.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_prices', function (Blueprint $table) {
            $table->decimal('high_price', 15, 2)->nullable()->after('open_price');
            $table->decimal('low_price', 15, 2)->nullable()->after('high_price');
            $table->unique(['stock_id', 'price_date'], 'stock_prices_stock_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('stock_prices', function (Blueprint $table) {
            $table->dropUnique('stock_prices_stock_date_unique');
            $table->dropColumn(['high_price', 'low_price']);
        });
    }
};
