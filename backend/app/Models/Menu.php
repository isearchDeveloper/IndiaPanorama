<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menu — A named navigation group.
 *
 * System menus (header, footer) are seeded and protected from deletion.
 * Admins can create unlimited custom menus (location = 'custom').
 *
 * @property int         $id
 * @property string      $name             e.g. "Header Menu", "Holiday Packages"
 * @property string      $slug             e.g. "header", "holiday-packages"
 * @property string      $location         "header" | "footer" | "custom"
 * @property bool        $is_active
 * @property bool        $is_system        true = cannot be deleted (header/footer)
 * @property int         $sort_order
 * @property string      $display_mode     "manual" | "region_state_city" | "region_state" | "state_city" | "city_only"
 * @property array|null  $display_settings JSON filter options
 */
class Menu extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    // ── Display mode constants ────────────────────────────────────────────────
    public const DISPLAY_MANUAL           = 'manual';
    public const DISPLAY_REGION_STATE_CITY = 'region_state_city';
    public const DISPLAY_REGION_STATE      = 'region_state';
    public const DISPLAY_STATE_CITY        = 'state_city';
    public const DISPLAY_CITY_ONLY         = 'city_only';

    public const DISPLAY_MODES = [
        self::DISPLAY_MANUAL            => 'Manual Menu Items',
        self::DISPLAY_REGION_STATE_CITY => 'Region → State → Cities',
        self::DISPLAY_REGION_STATE      => 'Region → State Only',
        self::DISPLAY_STATE_CITY        => 'State → Cities Only',
        self::DISPLAY_CITY_ONLY         => 'Cities Only',
    ];

    // Default settings when display_settings is null
    public const DEFAULT_DISPLAY_SETTINGS = [
        'region_ids'   => [],    // empty = all regions
        'state_ids'    => [],    // empty = all states
        'active_only'  => true,
        'package_only' => true,
        'manage_city_only' => false,
    ];

    protected $table = 'menus';

    protected $fillable = [
        'name',
        'slug',
        'location',
        'is_active',
        'sort_order',
        'display_mode',
        'display_settings',
        // is_system is NOT fillable — set only by seeder/migration, never by user input
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'is_system'        => 'boolean',
        'sort_order'       => 'integer',
        'display_settings' => 'array',
    ];

    // ──────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────────────────────

    /** All items belonging to this menu (flat, all depths). */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')
                    ->orderBy('sort_order');
    }

    /** Root items only (parent_id IS NULL). */
    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')
                    ->whereNull('parent_id')
                    ->orderBy('sort_order');
    }

    /** Root items that are active — used by public website. */
    public function activeRootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_id')
                    ->whereNull('parent_id')
                    ->where('status', 1)
                    ->orderBy('sort_order');
    }

    // ──────────────────────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────────────────────

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    // ──────────────────────────────────────────────────────────────
    // STATIC SHORTCUTS
    // ──────────────────────────────────────────────────────────────

    public static function header(): self
    {
        return static::where('slug', 'header')->firstOrFail();
    }

    public static function footer(): self
    {
        return static::where('slug', 'footer')->firstOrFail();
    }

    // ──────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * System menus (header/footer) cannot be deleted by admins.
     * Falls back to slug check when the is_system column does not yet exist
     * (migration 020001 may not have run yet).
     */
    public function isSystem(): bool
    {
        // Use DB column if it exists, otherwise fall back to slug heuristic
        return isset($this->attributes['is_system'])
            ? (bool) $this->attributes['is_system']
            : in_array($this->slug, ['header', 'footer']);
    }

    /** Custom menus created by admin (not header/footer). */
    public function isCustom(): bool
    {
        return $this->location === 'custom';
    }

    /** True when the menu uses an auto-generated location tree (not manual items). */
    public function isAutoDisplay(): bool
    {
        return $this->display_mode !== self::DISPLAY_MANUAL
            && $this->display_mode !== null;
    }

    /** Merged display settings (defaults filled in). */
    public function resolvedDisplaySettings(): array
    {
        return array_merge(self::DEFAULT_DISPLAY_SETTINGS, $this->display_settings ?? []);
    }

    public function itemCount(): int
    {
        return $this->items()->count();
    }

    public function icon(): string
    {
        return match ($this->location) {
            'header' => 'fa-desktop',
            'footer' => 'fa-align-justify',
            default  => 'fa-bars',   // custom menus
        };
    }

    public function badgeClass(): string
    {
        return match ($this->location) {
            'header' => 'bg-primary',
            'footer' => 'bg-secondary',
            default  => 'bg-dark',   // custom menus
        };
    }
}
