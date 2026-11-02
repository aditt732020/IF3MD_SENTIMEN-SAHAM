<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FR-04: Admin dapat melihat log status penarikan API
 * (apakah penarikan berita / harga hari ini berhasil atau gagal).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_fetch_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('source_type', ['news', 'price', 'sentiment']); // jenis penarikan
            $table->string('source_name');                               // contoh: "Yahoo Finance", "CNBC RSS"
            $table->enum('status', ['running', 'success', 'partial', 'failed'])->default('running');
            $table->unsignedInteger('records_fetched')->default(0);      // jumlah data yang berhasil disimpan
            $table->text('message')->nullable();                         // ringkasan / pesan error
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_fetch_logs');
    }
};
