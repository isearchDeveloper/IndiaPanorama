<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsSection extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'cms_page_sections';

    protected $fillable = [
        'cms_page_id', 'type',
        'label', 'content', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'content'   => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }

    public function getResolvedContentAttribute(): array
    {
        return $this->content ?? [];
    }
}
