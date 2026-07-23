<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SlugCheckController extends Controller
{
    /**
     * Whitelisted type-key → [table, slug_column].
     * Add a new entry here whenever a new slug-bearing resource is created.
     */
    private const TABLE_MAP = [
        'packages'            => ['packages',            'slug'],
        'states'              => ['states',              'slug'],
        'locations'           => ['locations',           'slug'],
        'tourist-attractions' => ['tourist_attractions', 'slug'],
        'tourist-activities'  => ['tourist_activities',  'slug'],
        'themes'              => ['themes',              'slug'],
        'theme-spots'         => ['theme_spots',         'slug'],
        'festivals'           => ['festivals',           'slug'],
        'holiday-settings'    => ['holiday_settings',   'slug'],
        'car-packages'        => ['car_packages',        'slug'],
        'car-routes'          => ['car_routes',          'slug'],
        'cms-pages'           => ['cms_pages',           'slug'],
    ];

    /**
     * GET admin/slug-check?type=packages&slug=kerala-tour&exclude=5
     *
     * Returns { exists: true|false }.
     * The slug param is re-slugified server-side so JS and PHP always agree.
     */
    public function check(Request $request)
    {
        $type    = $request->input('type', '');
        $slug    = Str::slug($request->input('slug', ''));
        $exclude = (int) $request->input('exclude', 0);

        if (!isset(self::TABLE_MAP[$type]) || $slug === '') {
            return response()->json(['exists' => false]);
        }

        [$table, $col] = self::TABLE_MAP[$type];

        $query = DB::table($table)->where($col, $slug);

        if ($exclude > 0) {
            $query->where('id', '!=', $exclude);
        }

        return response()->json(['exists' => $query->exists()]);
    }
}
