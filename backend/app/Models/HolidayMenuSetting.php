<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * HolidayMenuSetting
 * ──────────────────
 * Admin override table for the auto-generated Holiday Packages menu.
 * Each row stores sort_order and is_visible for one region / state / location.
 *
 * @property int    $id
 * @property string $type          'region' | 'state' | 'location'
 * @property int    $reference_id  PK of the referenced row in regions/states/locations
 * @property int    $sort_order
 * @property int    $is_visible    1 = show, 0 = hide
 */
class HolidayMenuSetting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'holiday_menu_settings';

    public const TYPE_REGION   = 'region';
    public const TYPE_STATE    = 'state';
    public const TYPE_LOCATION = 'location';

    protected $fillable = [
        'type',
        'reference_id',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'reference_id' => 'integer',
        'sort_order'   => 'integer',
        'is_visible'   => 'integer',
    ];

    // ──────────────────────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────────────────────

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // ──────────────────────────────────────────────────────────────
    // STATIC HELPERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Load all settings of one type, keyed by reference_id.
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function mapForType(string $type): \Illuminate\Support\Collection
    {
        return static::where('type', $type)->get()->keyBy('reference_id');
    }

    /**
     * Toggle is_visible for a given (type, reference_id).
     * Creates the row if it does not exist yet.
     *
     * @return int  New is_visible value (0 or 1)
     */
    public static function toggleVisibility(string $type, int $refId): int
    {
        $row = static::withTrashed()->firstOrNew(['type' => $type, 'reference_id' => $refId]);
        if ($row->trashed()) $row->restore();
        $row->is_visible = $row->is_visible ? 0 : 1;
        $row->save();

        return $row->is_visible;
    }

    /**
     * Bulk-upsert sort_order for a list of IDs (in position order).
     * Safe: validates all IDs exist in source table before writing.
     */
    public static function bulkReorder(string $type, array $orderedIds): void
    {
        \DB::transaction(function () use ($type, $orderedIds) {
            foreach ($orderedIds as $position => $id) {
                $row = static::withTrashed()->updateOrCreate(
                    ['type' => $type, 'reference_id' => (int) $id],
                    ['sort_order' => $position + 1]
                );
                if ($row->trashed()) $row->restore();
            }
        });
    }
}
