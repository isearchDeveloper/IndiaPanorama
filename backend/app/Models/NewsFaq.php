<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsFaq extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'news_faqs';

    protected $fillable = ['news_id', 'question', 'answer'];

    public function news()
    {
        return $this->belongsTo(news::class);
    }
}
