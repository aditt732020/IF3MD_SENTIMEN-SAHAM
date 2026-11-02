<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    protected $fillable = [
        'stock_id', 'price_date',
        'open_price', 'high_price', 'low_price', 'close_price', // OHLC (FR-06)
        'volume',
    ];

    protected $casts = [
        'price_date' => 'date',
    ];

    // Riwayat harga dimiliki oleh satu emiten saham
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}