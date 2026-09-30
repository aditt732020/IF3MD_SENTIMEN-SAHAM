<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    protected $fillable = ['stock_id', 'price_date', 'open_price', 'close_price', 'volume'];

    // Riwayat harga dimiliki oleh satu emiten saham
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}