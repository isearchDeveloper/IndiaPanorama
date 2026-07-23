<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'pages';

    protected $fillable = [
        'id',
        'slug',
        'title',
        'description',
        'banner_image',
        'banner_image_alt',
        'faq_title'
    ];

    public function faqs()
    {
        return $this->hasMany(PageFaq::class, 'page_id', 'id');
    }

    public function meta()
    {
        return $this->hasOne(PageMetaData::class, 'page_id', 'id');
    }
}
