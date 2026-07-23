<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBlogItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'home_blog_items';

    protected $fillable = ['title', 'image', 'image_alt', 'link', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
