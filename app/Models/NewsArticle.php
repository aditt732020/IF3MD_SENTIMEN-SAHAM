<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = ['title', 'content', 'source_url', 'published_at'];
    
    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Satu artikel berita bisa menghasilkan banyak skor sentimen (jika menyebut lebih dari 1 emiten)
    public function newsSentiments()
    {
        return $this->hasMany(NewsSentiment::class);
    }
}