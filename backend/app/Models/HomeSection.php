<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomeSection extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'home_sections';

    protected $fillable = [
        'section_key',
        'label',
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'image',
        'image_alt',
        'is_visible',
        'sort_order',
        'extra_data',
    ];

    protected $casts = [
        'is_visible'  => 'boolean',
        'sort_order'  => 'integer',
        'extra_data'  => 'array',
    ];

    // ── Cache key ──────────────────────────────────────────────────────
    const CACHE_KEY = 'home_sections_all';
    const CACHE_TTL = 60; // seconds

    // ── Scopes ──────────────────────────────────────────────────────────

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ── Static helpers ──────────────────────────────────────────────────

    /**
     * Get all sections keyed by section_key (cached).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function allKeyed(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::ordered()->get()->keyBy('section_key');
        });
    }

    /**
     * Get a single section by key. Returns null if not found.
     */
    public static function byKey(string $key): ?self
    {
        return static::allKeyed()->get($key);
    }

    /**
     * Flush the section cache — call after any update.
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ── Accessor ────────────────────────────────────────────────────────

    /**
     * Return a specific extra_data key with optional default.
     */
    public function extra(string $key, mixed $default = null): mixed
    {
        return data_get($this->extra_data, $key, $default);
    }
}
