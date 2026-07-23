<?php

use App\Models\Category;
use App\Models\Country;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (!function_exists('list_config')) {
    function list_config(): array
    {
        return [
            'limit'     => 10,
            'order_by'  => 'id',
            'direction' => 'desc',
        ];
    }
}

if (!function_exists('capped_limit')) {
    /**
     * Resolve a "how many items for this slider/list" limit, honoring an optional
     * `?limit=` query param but clamped to a sane max so a caller can't request
     * an unbounded/huge payload.
     */
    function capped_limit(?\Illuminate\Http\Request $request, int $default = 12, int $max = 24): int
    {
        $limit = $request ? (int) $request->get('limit', $default) : $default;
        return max(1, min($limit, $max));
    }
}

if (!function_exists('invalidRequest')) {
    function invalidRequest(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status'  => 'failed',
            'message' => 'Invalid Request',
        ], 404);
    }
}

if (!function_exists('getColor')) {
    function getColor(): array
    {
        return [
            'bg-primary text-white',
            'bg-success text-white',
            'bg-danger text-white',
            'bg-info text-white',
        ];
    }
}

if (!function_exists('getFleetType')) {
    function getFleetType(): array
    {
        return ['Economy', 'Executive', 'Luxury'];
    }
}

if (!function_exists('strip_figma_paste_junk')) {
    /**
     * Pasting text copied from Figma into a rich-text editor leaves behind hidden
     * `data-metadata`/`data-buffer` clipboard spans containing a huge base64 blob
     * (Figma's "paste back into Figma" round-trip data). Strip it before saving.
     */
    function strip_figma_paste_junk(?string $html): ?string
    {
        if (!$html) {
            return $html;
        }
        $html = preg_replace('/<span\s+data-metadata="[^"]*"><\/span>/i', '', $html);
        $html = preg_replace('/(<span)\s+data-buffer="[^"]*"/i', '$1', $html);
        return $html;
    }
}

if (!function_exists('storage_link')) {
    function storage_link(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        $disk = config('filesystems.upload_disk', 'public');
        // S3: use CDN/bucket absolute URL
        if ($disk === 's3') {
            $cdnUrl = rtrim(config('filesystems.disks.s3.url', ''), '/');
            if ($cdnUrl) {
                if (str_starts_with($path, 'http')) {
                    $urlPath = parse_url($path, PHP_URL_PATH) ?? $path;
                    $pos     = strpos($urlPath, '/storage/');
                    $path    = $pos !== false ? ltrim(substr($urlPath, $pos + 9), '/') : ltrim($urlPath, '/');
                }
                return $cdnUrl . '/' . ltrim($path, '/');
            }
        }

        // Normalise stored path to bare relative path (strip any URL or /storage/ prefix).
        // Handles: http://host/storage/x, http://host/crm/storage/x,
        //          http://host/crm/public/storage/x, /storage/x, /public/storage/x
        if (str_starts_with($path, 'http')) {
            $urlPath = parse_url($path, PHP_URL_PATH) ?? $path;
            $pos     = strpos($urlPath, '/storage/');
            $path    = $pos !== false ? ltrim(substr($urlPath, $pos + 9), '/') : ltrim($urlPath, '/');
        } elseif (($pos = strpos($path, '/storage/')) !== false) {
            $path = ltrim(substr($path, $pos + 9), '/');
        } elseif (str_starts_with($path, '/')) {
            $path = ltrim($path, '/');
        }

        // Build the storage URL:
        // 1. STORAGE_BASE_URL (CDN subdomain pointed straight at storage/app/public — no "/storage/" segment)
        // 2. APP_URL fallback — served via the public/storage symlink, so "/storage/" is required
        $cdnBase = rtrim(config('app.storage_base_url', ''), '/');
        if ($cdnBase) {
            return $cdnBase . '/' . $path;
        }
        return rtrim(config('app.url'), '/') . '/storage/' . $path;
    }
}

if (!function_exists('unique_filename')) {
    /**
     * Build a collision-safe filename that still reads as the original name,
     * e.g. "image.webp" -> "image-1719764729123.webp". Used by every upload
     * across the app so two files with the same name never overwrite each other.
     */
    function unique_filename(\Illuminate\Http\UploadedFile $file): string
    {
        $base = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $unique = (int) round(microtime(true) * 1000);

        // Derive the stored extension from the file's actual detected content
        // (guessExtension() inspects the real bytes, not the client-supplied
        // filename) so a spoofed upload can't smuggle in an executable
        // extension by simply naming itself e.g. "shell.php". Only fall back
        // to the client-supplied extension — and only after checking it
        // against a fixed image/document whitelist — if content-sniffing
        // can't determine one at all.
        $ext = $file->guessExtension();
        if (!$ext) {
            $clientExt = strtolower($file->getClientOriginalExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'pdf'];
            $ext = in_array($clientExt, $allowed, true) ? $clientExt : 'bin';
        }

        return ($base !== '' ? $base : 'file') . '-' . $unique . '.' . $ext;
    }
}

if (!function_exists('storage_base_url')) {
    // Returns the storage base URL string for JS use.
    // e.g. "https://cdn.indianpanorama.in/" or, locally, "https://projects.isearchsolution.com/crm/storage/"
    function storage_base_url(): string
    {
        if (config('filesystems.upload_disk') === 's3') {
            $cdnUrl = rtrim(config('filesystems.disks.s3.url', ''), '/');
            if ($cdnUrl) {
                return $cdnUrl . '/';
            }
        }

        $cdnBase = rtrim(config('app.storage_base_url', ''), '/');
        if ($cdnBase) {
            return $cdnBase . '/';
        }
        return rtrim(config('app.url'), '/') . '/storage/';
    }
}

if (!function_exists('public_link')) {
    function public_link(string $path): string
    {
        /** @var \Illuminate\Http\Request $req */
        $req  = app('request');
        $host = $req->getHost();

        if (
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            str_starts_with($host, '192.168.') ||
            str_starts_with($host, '10.')
        ) {
            return asset('/' . ltrim($path, '/'));
        }

        return asset('/public/' . ltrim($path, '/'));
    }
}

if (!function_exists('humanize_folder_label')) {
    /**
     * Turn a raw Media Library storage folder key (e.g. "home-sections",
     * "car_route") into a readable label for dropdowns (e.g. "Home Sections",
     * "Car Route").
     */
    function humanize_folder_label(string $folder): string
    {
        $label = ucwords(str_replace(['-', '_', '/'], ' ', $folder));

        return str_ireplace('Cms', 'CMS', $label);
    }
}

if (!function_exists('split_pipe')) {
    function split_pipe(): string
    {
        return '#';
    }
}

if (!function_exists('getPackageType')) {
    function getPackageType(): array
    {
        return [(object) ['id' => 1, 'name' => 'India Tour Package']];
    }
}

if (!function_exists('getPackageTypeName')) {
    /** @param mixed $id */
    function getPackageTypeName($id): string
    {
        return 'India Tour Package';
    }
}

if (!function_exists('getCategoryName')) {
    function getCategoryName(int $id): ?string
    {
        return Category::find($id)?->name ?? null;
    }
}

if (!function_exists('getCountryName')) {
    function getCountryName(int $id): ?string
    {
        return Country::find($id)?->name ?? null;
    }
}

if (!function_exists('getLocationName')) {
    function getLocationName(int $id): ?string
    {
        return Location::find($id)?->name ?? null;
    }
}

function getExecutedQuery(\Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query): string
{
    $sql      = $query->toSql();
    $bindings = $query->getBindings();

    foreach ($bindings as $binding) {
        $binding = is_numeric($binding) ? $binding : "'" . addslashes((string) $binding) . "'";
        $sql     = preg_replace('/\?/', (string) $binding, $sql, 1);
    }

    return $sql ?? '';
}

function getUrl(int $page): array
{
    $url       = 'admin.page-settings.';
    $page_name = '';

    if ($page === 2) {
        $url .= 'luxury-train';
        $page_name = 'train_page';
    }
    if ($page === 3) {
        $url .= 'india';
        $page_name = 'india';
    }
    if ($page === 5) {
        $url .= 'luxury-hotel';
        $page_name = 'hotel_page';
    }
    if ($page === 6) {
        $url .= 'car';
        $page_name = 'car_content';
    }
    if ($page === 7) {
        $url .= 'customized-holiday';
        $page_name = 'customized-holiday';
    }
    if ($page === 8) {
        $url .= 'home';
        $page_name = 'home';
    }
    if ($page === 9) {
        $url .= 'bus';
        $page_name = 'bus_page';
    }

    return ['url' => $url, 'page' => $page_name];
}

function normalizeRegionSlug(string $text): string
{
    $text = strtolower($text);
    $text = str_replace(['&', ' and '], ' ', $text);
    $text = preg_replace('/[^a-z0-9\s]/', '', $text) ?? '';
    $text = preg_replace('/\s+/', '-', trim($text)) ?? '';
    return $text;
}

if (!function_exists('getRegionWiseCities')) {
    /**
     * Build region → state → cities structure from DB.
     *
     * @param \Illuminate\Support\Collection $currentCities  Location records
     * @return array<string, array<string, array{state: object, cities: array<int, object>}>>
     */
    function getRegionWiseCities(\Illuminate\Support\Collection $currentCities): array
    {
        $regionOrders = DB::table('regions')->pluck('order_seq', 'name')->toArray();
        $menu         = [];

        // ── Group via State → Region ──────────────────────────────────────────
        $stateIds = $currentCities->pluck('state_id')->filter()->unique()->toArray();

        if (!empty($stateIds)) {
            $states = \App\Models\State::whereIn('id', $stateIds)
                ->with('region:id,name')
                ->orderBy('name')
                ->get()
                ->keyBy('id');

            foreach ($currentCities as $city) {
                $stateId = $city->state_id ?? null;
                if (!$stateId || !$states->has($stateId)) {
                    continue;
                }
                $state      = $states[$stateId];
                $regionName = $state->region?->name;
                if (!$regionName) {
                    continue;
                }
                if (!isset($menu[$regionName][$state->name])) {
                    $menu[$regionName][$state->name] = ['state' => $state, 'cities' => []];
                }
                $menu[$regionName][$state->name]['cities'][] = $city;
            }
        }

        // ── Fallback: cities with direct region_id but no state ───────────────
        $unassigned = $currentCities->filter(fn($c) => empty($c->state_id) && !empty($c->region_id));
        if ($unassigned->isNotEmpty()) {
            $regionIds   = $unassigned->pluck('region_id')->unique()->toArray();
            $regionNames = DB::table('regions')->whereIn('id', $regionIds)->pluck('name', 'id');

            foreach ($unassigned as $city) {
                $regionName = $regionNames[$city->region_id] ?? null;
                if (!$regionName) {
                    continue;
                }
                if (!isset($menu[$regionName]['Other'])) {
                    $menu[$regionName]['Other'] = [
                        'state'  => (object) ['name' => 'Other', 'slug' => ''],
                        'cities' => [],
                    ];
                }
                $menu[$regionName]['Other']['cities'][] = $city;
            }
        }

        // ── Sort cities by sort_order within each state ───────────────────────
        foreach ($menu as &$stateGroups) {
            foreach ($stateGroups as &$data) {
                usort($data['cities'], fn($a, $b) => ($a->sort_order ?? 0) <=> ($b->sort_order ?? 0));
            }
        }
        unset($stateGroups, $data);

        // ── Sort regions by order_seq ─────────────────────────────────────────
        uksort($menu, fn($a, $b) => ($regionOrders[$a] ?? 999) <=> ($regionOrders[$b] ?? 999));

        return $menu;
    }
}
