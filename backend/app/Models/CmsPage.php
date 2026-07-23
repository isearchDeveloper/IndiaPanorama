<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    use HasFactory;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'cms_pages';

    protected $fillable = [
        'id',
        'slug',
        'title',
        'sub_title',
        'description',
        'banner_image',
        'banner_image_alt',
        // builder fields
        'is_published',
        'template',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image',
        'in_sitemap',
        'in_menu',
        'menu_label',
        'menu_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'in_sitemap'   => 'boolean',
        'in_menu'      => 'boolean',
    ];

    // ── Legacy relations (kept for backward compatibility) ─────────────────

    public function meta()
    {
        return $this->hasOne(CmsPageMetaData::class, 'page_id', 'id');
    }

    // ── Builder relations ──────────────────────────────────────────────────

    public function sections()
    {
        return $this->hasMany(CmsSection::class, 'cms_page_id')->orderBy('sort_order');
    }

    public function activeSections()
    {
        return $this->sections()->where('is_active', true);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeHasSections($q)
    {
        return $q->whereHas('sections');
    }
}
