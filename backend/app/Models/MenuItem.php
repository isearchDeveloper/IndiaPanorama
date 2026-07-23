<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * MenuItem — One item in a menu tree (root, dropdown child, or grandchild).
 *
 * Types (link resolution):
 *   custom         → raw URL (stored in `url`)
 *   page           → links to a CMS page   (linked_id = pages.id)
 *   package        → links to a package    (linked_id = packages.id)
 *   location       → links to a location   (linked_id = locations.id)
 *   category       → links to a category   (linked_id = categories.id)
 *   menu_reference → inlines another menu  (linked_id = menus.id)
 *
 * Content Types (dropdown behaviour — stored in mega_settings.content_type):
 *   normal         → standard link / child items
 *   mega_menu      → renders a full-width mega dropdown from location tree
 *
 * @property int         $id
 * @property int         $menu_id
 * @property int|null    $parent_id
 * @property string      $title
 * @property string      $type
 * @property int|null    $linked_id
 * @property string|null $url
 * @property string      $target     _self|_blank
 * @property int         $status     1 = active, 0 = hidden
 * @property int         $sort_order
 * @property array|null  $mega_settings
 */
class MenuItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'menu_items';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'type',
        'linked_id',
        'url',
        'target',
        'status',
        'sort_order',
        'mega_settings',
    ];

    protected $casts = [
        'status'        => 'integer',
        'sort_order'    => 'integer',
        'linked_id'     => 'integer',
        'parent_id'     => 'integer',
        'menu_id'       => 'integer',
        'mega_settings' => 'array',
    ];

    // ──────────────────────────────────────────────────────────────
    // CONSTANTS — link types
    // ──────────────────────────────────────────────────────────────

    public const TYPES = [
        'custom'         => 'Custom URL',
        'page'           => 'CMS Page',
        'package'        => 'Package',
        'location'       => 'City / Location',
        'region'         => 'Region',
        'state'          => 'State',
        'category'       => 'Category',
        'menu_reference' => 'Menu Reference',
    ];

    /** Types that require a linked_id pointing to the named table. */
    public const LINKED_TYPES = ['page', 'package', 'location', 'region', 'state', 'category'];

    public const MAX_DEPTH = 2;   // 0 = root, 1 = child, 2 = grandchild

    // ──────────────────────────────────────────────────────────────
    // CONSTANTS — content types (mega_settings.content_type)
    // ──────────────────────────────────────────────────────────────

    /** Standard link — uses direct children or no dropdown. */
    public const CONTENT_NORMAL   = 'normal';

    /** Mega dropdown — builds full-width tree from location DB or referenced menu. */
    public const CONTENT_MEGA     = 'mega_menu';

    // ──────────────────────────────────────────────────────────────
    // CONSTANTS — mega menu display sources
    // ──────────────────────────────────────────────────────────────

    /** Auto-generate tree from Regions / States / Cities DB. */
    public const MEGA_SOURCE_AUTO   = 'auto';

    /** Use a manually-built admin menu as content. */
    public const MEGA_SOURCE_CUSTOM = 'custom_menu';

    public const MEGA_SOURCES = [
        self::MEGA_SOURCE_AUTO   => 'Holiday Packages Tree (Auto)',
        self::MEGA_SOURCE_CUSTOM => 'Custom Menu Reference',
    ];

    // ──────────────────────────────────────────────────────────────
    // CONSTANTS — mega menu display modes (mirrors Menu constants)
    // ──────────────────────────────────────────────────────────────

    public const MEGA_DISPLAY_MODES = [
        'region_state_city' => 'Region → State → Cities',
        'region_state'      => 'Region → State',
        'state_city'        => 'State → Cities',
        'city_only'         => 'Cities Only',
    ];

    // ──────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────────────────────

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->orderBy('sort_order');
    }

    public function activeChildren(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
                    ->where('status', 1)
                    ->orderBy('sort_order');
    }

    // ──────────────────────────────────────────────────────────────
    // MEGA MENU HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Returns true when this item should render a mega-menu dropdown.
     */
    public function isMegaMenu(): bool
    {
        return ($this->mega_settings['content_type'] ?? self::CONTENT_NORMAL) === self::CONTENT_MEGA;
    }

    /**
     * Merged mega_settings with all defaults guaranteed to be present.
     */
    public function resolvedMegaSettings(): array
    {
        return array_merge([
            'content_type'   => self::CONTENT_NORMAL,
            'display_source' => self::MEGA_SOURCE_AUTO,
            'display_mode'   => 'region_state_city',
            'linked_menu_id' => null,
            'region_ids'     => [],
            'state_ids'      => [],
            'active_only'    => true,
            'package_only'   => false,
            'manage_city_only' => false,
            'banner'         => [
                'image'       => '',
                'alt'         => '',
                'title'       => '',
                'description' => '',
                'cta_text'    => '',
                'cta_url'     => '',
            ],
        ], $this->mega_settings ?? []);
    }

    // ──────────────────────────────────────────────────────────────
    // URL RESOLUTION
    // ──────────────────────────────────────────────────────────────

    public function resolveUrl(): string
    {
        if ($this->type === 'menu_reference') {
            return '#';
        }

        if ($this->type === 'custom' || ! $this->linked_id) {
            return $this->url ?: '#';
        }

        return match ($this->type) {
            'page'     => $this->resolveSlugUrl('pages',      '/pages/'),
            'package'  => $this->resolveSlugUrl('packages',   '/packages/'),
            'location' => $this->resolveSlugUrl('locations',  '/location/'),
            'region'   => $this->resolveSlugUrl('regions',    '/holidays/'),
            'state'    => $this->resolveSlugUrl('states',     '/holidays/'),
            'category' => $this->resolveSlugUrl('categories', '/tours/'),
            default    => $this->url ?: '#',
        };
    }

    private function resolveSlugUrl(string $table, string $prefix): string
    {
        $slug = DB::table($table)->where('id', $this->linked_id)->value('slug');
        return $slug ? url($prefix . $slug) : ($this->url ?: '#');
    }

    // ──────────────────────────────────────────────────────────────
    // DISPLAY HELPERS
    // ──────────────────────────────────────────────────────────────

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function typeBadgeClass(): string
    {
        return match ($this->type) {
            'custom'         => 'bg-secondary',
            'page'           => 'bg-info text-dark',
            'package'        => 'bg-success',
            'location'       => 'bg-warning text-dark',
            'region'         => 'bg-orange text-white',
            'state'          => 'bg-teal text-white',
            'category'       => 'bg-primary',
            'menu_reference' => 'bg-purple text-white',
            default          => 'bg-secondary',
        };
    }

    public function typeIcon(): string
    {
        return match ($this->type) {
            'custom'         => 'fa-link',
            'page'           => 'fa-file-alt',
            'package'        => 'fa-suitcase-rolling',
            'location'       => 'fa-map-marker-alt',
            'region'         => 'fa-globe-asia',
            'state'          => 'fa-map',
            'category'       => 'fa-tags',
            'menu_reference' => 'fa-layer-group',
            default          => 'fa-circle',
        };
    }

    public function isActive(): bool   { return $this->status === 1; }
    public function isRoot(): bool     { return is_null($this->parent_id); }
    public function isMenuReference(): bool { return $this->type === 'menu_reference'; }

    // ──────────────────────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForMenu(Builder $query, int $menuId): Builder
    {
        return $query->where('menu_id', $menuId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
