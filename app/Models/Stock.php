<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['ticker_code', 'company_name', 'sector'];

    // Satu emiten saham memiliki banyak riwayat harga
    public function stockPrices()
    {
        return $this->hasMany(StockPrice::class);
    }

    // Satu emiten saham memiliki banyak hasil sentimen berita
    public function newsSentiments()
    {
        return $this->hasMany(NewsSentiment::class);
    }
}