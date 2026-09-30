<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Master Saham
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('ticker_code', 10)->unique();
            $table->string('company_name');
            $table->string('sector')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Riwayat Harga Saham
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->date('price_date');
            $table->decimal('open_price', 15, 2);
            $table->decimal('close_price', 15, 2);
            $table->bigInteger('volume');
            $table->timestamps();
        });

        // 3. Tabel Master Berita
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('source_url')->unique();
            $table->dateTime('published_at');
            $table->timestamps();
        });

        // 4. Tabel Pivot/Hasil Analisis Sentimen
        Schema::create('news_sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_article_id')->constrained('news_articles')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->enum('sentiment_label', ['positive', 'negative', 'neutral']);
            $table->decimal('confidence_score', 4, 3);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_sentiments');
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('stock_prices');
        Schema::dropIfExists('stocks');
    }
};