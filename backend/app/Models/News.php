<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'primary_img',
        'primary_img_alt',
        'type',
        'faq_title',
        'is_active',
        'author_name',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_active' => 'integer',
    ];

    public function faqs()
    {
        return $this->hasMany(NewsFaq::class,'news_id','id');
    }  

    public function meta()
    {
        return $this->hasOne(NewsMetaData::class, 'news_id', 'id');
    }
}
