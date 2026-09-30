<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsSentiment extends Model
{
    protected $fillable = ['news_article_id', 'stock_id', 'sentiment_label', 'confidence_score'];

    public function newsArticle()
    {
        return $this->belongsTo(NewsArticle::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}