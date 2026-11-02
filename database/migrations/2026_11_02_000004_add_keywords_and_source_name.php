<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - stocks.keywords      : alias nama emiten untuk mencocokkan berita (contoh "BCA, Bank Central Asia").
 *                          Bisa diubah Admin lewat CRUD Master Data Emiten (FR-03).
 * - news_articles.source_name : nama portal berita, ditampilkan di tabel detail berita (FR-11).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('keywords')->nullable()->after('sector');
        });

        Schema::table('news_articles', function (Blueprint $table) {
            $table->string('source_name', 100)->nullable()->after('source_url');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('news_articles', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
            $table->dropColumn('source_name');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};

