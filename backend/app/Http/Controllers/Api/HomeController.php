<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeAboutFeature;
use App\Models\HomeBlogItem;
use App\Models\HomeSection;
use App\Models\Banner;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * GET /api/v1/home
 *
 * Single-source-of-truth home page API.
 * All data comes from the admin CMS.
 * Only active / visible content is returned.
 *
 * Response keys:
 *   hero_banner          — slider banners from `banners` (is_static = 0)
 *   india_tour_packages  — section meta + Region → State → City tree
 *   customized_tours     — section meta + admin-selected packages
 *   trusted_operator     — section meta (about_intro) + feature bullets
 *   why_indian_panorama  — section meta (why_choose)
 *   latest_blogs         — section meta + home_blog_items
 *   promo_banner         — promotional_banner section + banner image (is_static = 1)
 *   seo_meta             — page_meta_data for page_id = 8 (home page)
 */
class HomeController extends Controller
{
    public const CACHE_KEY = 'api_home_v3';
    public const CACHE_TTL = 60; // seconds

    public function index(): JsonResponse
    {
        try {
            $data = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn() => $this->build());

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            Log::error('HomeController: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load home page data.',
                'data'    => [],
            ], 500);
        }
    }

    /** Called by admin controllers whenever CMS data changes. */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ──────────────────────────────────────────────────────────────
    // CORE BUILDER — all queries run here, no N+1
    // ──────────────────────────────────────────────────────────────

    private function build(): array
    {
        // ── 1. All home sections in ONE query (cached via HomeSection::allKeyed) ──
        $sections = HomeSection::allKeyed();

        // ── 2. All banners in ONE query — split by type in-memory ────────────────
        $allBanners  = Banner::where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(capped_limit(request()))
            ->get();
        $heroBanners = $allBanners->where('is_static', 0)->values();

        // ── 3. Feature bullets (one query) ───────────────────────────────────────
        $features = HomeAboutFeature::active()->ordered()->limit(capped_limit(request()))->get();

        // ── 4. Blog items (one query, limit 6) ───────────────────────────────────
        $blogs = HomeBlogItem::ordered()->limit(6)->get();

        // ── 5. SEO meta for the home page (page_id = 8) ──────────────────────────
        $seoMeta = DB::table('page_meta_data')->where('page_id', 8)->first();

        // ── 6. Customized packages — admin selects specific IDs via extra_data ────
        $customTourSection = $sections->get('customized_tours');
        $packageIds        = array_filter(array_map('intval', (array) ($customTourSection?->extra('package_ids', []) ?? [])));

        $customizedPkgs = Package::where('is_active', 1)
            ->when(
                ! empty($packageIds),
                // Admin has hand-picked packages → fetch exactly those, preserve order
                fn ($q) => $q->whereIn('id', $packageIds)
                              ->orderByRaw('FIELD(id, ' . implode(',', $packageIds) . ')'),
                // Fallback: no IDs configured → use special/customised flags
                fn ($q) => $q->where(function ($inner) {
                    $inner->where('is_special_package', 1)->orWhere('is_customized', 1);
                })->orderByDesc('id')->limit(max(1, (int) ($customTourSection?->extra('card_count', 6) ?? 6)))
            )
            ->with([
                'details:package_id,duration_days,duration_nights',
                'images'  => fn ($q) => $q->orderBy('sort_order')->limit(1),
            ])
            ->get();

        // ── 7. India tour regions (title + slug + banner only) ────────────────
        $indiaRegions = $this->buildIndiaTourRegions();

        return [
            'hero_banner'         => $this->formatHeroBanner($heroBanners),
            'india_tour_packages' => $this->formatIndiaTourPackages($sections->get('india_tours'), $indiaRegions),
            'customized_tours'    => $this->formatCustomizedTours($sections->get('customized_tours'), $customizedPkgs),
            'trusted_operator'    => $this->formatTrustedOperator($sections->get('about_intro'), $features),
            'why_indian_panorama' => $this->formatWhySection($sections->get('why_choose')),
            'latest_blogs'        => $this->formatLatestBlogs($sections->get('latest_blogs'), $blogs),
            'promo_banner'        => $this->formatPromoBanner($sections->get('promotional_banner')),
            'seo_meta'            => $this->formatSeoMeta($seoMeta),
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // REGION LIST  (title + slug + banner only — no state/city tree)
    // ──────────────────────────────────────────────────────────────

    /**
     * Return regions flagged is_popular, with their title, slug, and optional home-page card image.
     * Image comes from regions_details.home_image (left-joined) — kept separate from
     * regions_details.banner_image, which is used on the region's own landing page.
     * Ordered by regions.order_seq.
     */
    private function buildIndiaTourRegions(): array
    {
        $rows = DB::table('regions')
            ->leftJoin('regions_details', 'regions_details.region_id', '=', 'regions.id')
            ->where('regions.is_popular', 1)
            ->select(
                'regions.id',
                'regions.name',
                'regions.slug',
                'regions_details.home_image',
                'regions_details.home_image_alt',
                'regions_details.about',
            )
            ->orderBy('regions.order_seq')
            ->orderBy('regions.name')
            ->limit(capped_limit(request(), 20))
            ->get();

        return $rows->map(fn ($r) => [
            'id'          => $r->id,
            'title'       => $r->name,
            'slug'        => $r->slug,
            'url'         => $this->frontendUrl('/holidays/' . $r->slug),
            'banner'      => $r->home_image     ? $this->storageUrl($r->home_image)     : null,
            'banner_alt'  => $r->home_image_alt ?? null,
            'description' => $r->about          ?? null,
        ])->values()->toArray();
    }

    // ──────────────────────────────────────────────────────────────
    // SECTION FORMATTERS
    // ──────────────────────────────────────────────────────────────

    /**
     * Hero banner — slider slides.
     * Each slide carries its own title/subtitle/button/image.
     */
    private function formatHeroBanner($banners): array
    {
        $slides = $banners->map(fn(Banner $b) => [
            'image'       => $b->banner_image     ? $this->storageUrl($b->banner_image)     : null,
            'image_alt'   => $b->banner_image_alt ?? null,
            'title'       => $b->title            ?? null,
            'subtitle'    => $b->subtitle         ?? null,
            'button_text' => $b->button_text      ?? null,
            'button_url'  => $b->url              ?? null,
        ])->values()->toArray();

        return ['slides' => $slides];
    }

    /**
     * India Tour Packages — section meta + location tree.
     */
    private function formatIndiaTourPackages(?HomeSection $section, array $tree): array
    {
        return [
            'title'       => $section?->title,
            'subtitle'    => $section?->subtitle,
            'description' => $section?->description,
            'button_text' => $section?->button_text,
            'button_url'  => $section?->button_url,
            'regions'     => $tree,
        ];
    }

    /**
     * Customized Tours — section meta + admin-selected packages.
     */
    private function formatCustomizedTours(?HomeSection $section, $packages): array
    {
        $pkgData = $packages->map(function (Package $p) {
            $image = $p->images->first();
            return [
                'id'           => $p->id,
                'title'        => $p->title,
                'slug'         => $p->slug,
                'image'        => $p->primary_image
                    ? $this->storageUrl($p->primary_image)
                    : ($image?->image_path ? $this->storageUrl($image->image_path) : null),
                'image_alt'    => $p->primary_image_alt ?? $image?->image_alt ?? null,
                'duration_days'   => $p->details?->duration_days,
                'duration_nights' => $p->details?->duration_nights,
                'price'        => $p->price ? (float) $p->price : null,
            ];
        })->values()->toArray();

        return [
            'title'       => $section?->title,
            'subtitle'    => $section?->subtitle,
            'button_text' => $section?->button_text,
            'button_url'  => $section?->button_url,
            'packages'    => $pkgData,
        ];
    }

    /**
     * Trusted Operator — left side (text/image) + right side (features).
     */
    private function formatTrustedOperator(?HomeSection $section, \Illuminate\Database\Eloquent\Collection $features): array
    {
        return [
            // Left side
            'title'       => $section?->title,
            'description' => $section?->description,
            'button_text' => $section?->button_text,
            'button_url'  => $section?->button_url,
            'image'       => $section?->image ? $this->storageUrl($section->image) : null,
            'image_alt'   => $section?->image_alt,
            // Right side — master text from extra_data + feature bullets
            'master_text' => $section?->extra('master_text'),
            'features'    => $features->map(fn(HomeAboutFeature $f) => [
                'icon_class'  => $f->icon_class,
                'title'       => $f->text,
                'description' => $f->feature_description,
                'sort_order'  => $f->sort_order,
            ])->values()->toArray(),
        ];
    }

    /**
     * Why Indian Panorama — section meta + image.
     */
    private function formatWhySection(?HomeSection $section): array
    {
        return [
            'title'     => $section?->title,
            'subtitle'  => $section?->subtitle,
            'image'     => $section?->image ? $this->storageUrl($section->image) : null,
            'image_alt' => $section?->image_alt,
        ];
    }

    /**
     * Latest Blogs — section meta + blog items.
     */
    private function formatLatestBlogs(?HomeSection $section, \Illuminate\Database\Eloquent\Collection $blogs): array
    {
        $blogData = $blogs->map(fn(HomeBlogItem $b) => [
            'title'      => $b->title,
            'image'      => $b->image ? $this->storageUrl($b->image) : null,
            'image_alt'  => $b->image_alt,
            'url'        => $b->link,
            'sort_order' => $b->sort_order,
        ])->values()->toArray();

        return [
            'title'       => $section?->title,
            'subtitle'    => $section?->subtitle,
            'button_text' => $section?->button_text,
            'button_url'  => $section?->button_url,
            'blogs'       => $blogData,
        ];
    }

    /**
     * Promo Banner — image-only response.
     *
     * Managed via Home Page CMS → Promo Banner tab (home_sections table,
     * section_key = 'promotional_banner'). Returns null when the section is
     * not visible or has no image uploaded.
     */
    private function formatPromoBanner(?HomeSection $section): ?array
    {
        if (! $section?->is_visible || ! $section->image) {
            return null;
        }

        return [
            'image'     => $this->storageUrl($section->image),
            'image_alt' => $section->image_alt ?? null,
            'is_active' => true,
        ];
    }

    /**
     * SEO Meta — from page_meta_data for the home page (page_id = 8).
     */
    private function formatSeoMeta(?object $meta): array
    {
        return [
            'meta_title'       => $meta?->meta_title       ?? null,
            'meta_description' => $meta?->meta_description ?? null,
            'meta_keywords'    => $meta?->meta_keywords    ?? null,
            'h1_heading'       => $meta?->h1_heading       ?? null,
            'extra_meta_head'  => $meta?->meta_details     ?? null,
            'extra_meta_body'  => $meta?->meta_body_details ?? null,
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // HELPER
    // ──────────────────────────────────────────────────────────────

    private function storageUrl(string $path): string
    {
        // Already absolute URL
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return function_exists('storage_link')
            ? storage_link($path)
            : asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Generate a URL pointing to the Next.js frontend.
     * Falls back to the CRM's own APP_URL if FRONTEND_URL is not configured.
     *
     * Set FRONTEND_URL in .env to your Next.js app URL, e.g.:
     *   FRONTEND_URL=https://www.indianpanorama.in
     */
    private function frontendUrl(string $path): string
    {
        $base = rtrim(env('FRONTEND_URL', config('app.url')), '/');
        return $base . '/' . ltrim($path, '/');
    }
}
