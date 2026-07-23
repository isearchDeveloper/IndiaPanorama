<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsMetaData extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

   protected $table = 'news_meta_data';

    protected $fillable = [
        'news_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'h1_heading',
        'meta_details'
    ];
    
    public function news(){
         return $this->belongsTo(News::class, 'news_id');
    }
    
}
