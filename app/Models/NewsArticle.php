<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = ['title', 'content', 'source_url', 'source_name', 'published_at'];
    
    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Satu artikel berita bisa menghasilkan banyak skor sentimen (jika menyebut lebih dari 1 emiten)
    public function newsSentiments()
    {
        return $this->hasMany(NewsSentiment::class);
    }

    // Alias pendek untuk eager loading di controller
    public function sentiments()
    {
        return $this->hasMany(NewsSentiment::class);
    }
}