<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomepageResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\CarCategoryResource;
use App\Http\Resources\CarResource;
use App\Http\Resources\CarRouteResource;

use Illuminate\Http\Request;

use App\Models\Banner;
use App\Models\SpecialPackage;
use App\Models\HomeSection;
use App\Models\Page;
use App\Models\TourService;
use App\Models\Car;
use App\Models\CarRoute;
use App\Models\CarCity;
use App\Models\Menu;
use App\Services\HolidayMenuService;
use App\Http\Controllers\Api\HeaderMenuController;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/menu  (legacy — prefer /api/v1/header-menu)
    // ──────────────────────────────────────────────────────────────────────────

    public function menu()
    {
        $mainMenus = Menu::orderBy('order_seq', 'asc')
            ->get(['id', 'name', 'slug', 'type']);

        // Build India mega menu via HolidayMenuService (Region → State → Location)
        $indiaMenu = Cache::remember('settings_india_menu', 60, function () {
            return app(HolidayMenuService::class)
                ->buildAutoTree()
                ->filter(fn ($r) => $r->is_visible)
                ->map(fn ($region) => [
                    'id'     => $region->id,
                    'name'   => $region->name,
                    'slug'   => $region->slug,
                    'states' => collect($region->states)
                        ->filter(fn ($s) => $s->is_visible)
                        ->map(fn ($state) => [
                            'id'        => $state->id,
                            'name'      => $state->name,
                            'slug'      => $state->slug,
                            'locations' => collect($state->locations)
                                ->filter(fn ($l) => $l->is_visible)
                                ->map(fn ($l) => ['id' => $l->id, 'name' => $l->name, 'slug' => $l->slug])
                                ->values()->toArray(),
                        ])->values()->toArray(),
                ])->values()->toArray();
        });

        $car_city = CarCity::select('location', 'slug')
            ->where('is_active', 1)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'menus'              => $mainMenus,
            'india_mega_menu'    => $indiaMenu,
            'holidays_mega_menu' => $indiaMenu,
            'car_city'           => $car_city,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/home  (legacy — prefer /api/v1/home)
    // ──────────────────────────────────────────────────────────────────────────

    public function home()
    {
        $page_details = Page::with('meta')
            ->where('id', 8)
            ->firstOrFail();

        $data = [
            'slider_banners'  => Banner::where('is_active', 1)->where('is_static', 0)->orderBy('sort_order')->orderBy('id')->limit(capped_limit(request()))->get(),
            'single_banner'   => Banner::where('is_active', 1)->where('is_static', 1)->first(),
            'special_package' => SpecialPackage::with('package.details')->where('is_active', 1)->first(),
            'tour_services'   => TourService::where('is_active', 1)->limit(capped_limit(request()))->get(),
            'why_choose'      => HomeSection::where('section_key', 'why_choose')->first(),
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Details',
            'data'    => new HomepageResource([
                'extra' => $data,
                'page'  => $page_details,
            ]),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/page/settings/car
    // ──────────────────────────────────────────────────────────────────────────

    public function car()
    {
        $page_details = Page::with('faqs', 'meta')->where('id', 6)->firstOrFail();

        $cars = Car::where('is_active', 1)
            ->with('category')
            ->orderBy('id', 'desc')
            ->get();

        $categories = $cars
            ->pluck('category')
            ->filter(fn ($cat) => $cat && $cat->is_active == 1)
            ->unique('id')
            ->values();

        $fleets = $cars->groupBy('type')->map(fn ($g) => $g->take(5)->values());

        $routes = CarRoute::where('is_active', 1)
            ->with('details')
            ->orderBy('id', 'desc')
            ->limit(capped_limit(request()))
            ->get();

        $car_city = CarCity::select('location', 'slug', 'thumbnail_image', 'thumbnail_alt')
            ->where('is_active', 1)
            ->orderByDesc('id')
            ->get();

        $limitedCars = $cars->take(6);
        $totalCars   = $cars->count();

        return response()->json([
            'status'  => 'success',
            'message' => 'Details',
            'data'    => [
                'details'    => new PageResource($page_details),
                'categories' => CarCategoryResource::collection($categories),
                'cars'       => CarResource::collection($limitedCars),
                'routes'     => CarRouteResource::collection($routes),
                'fleets'     => $fleets->map(fn ($g) => CarResource::collection($g)),
                'carcity'    => $car_city->map(fn($c) => [
                    'location'        => $c->location,
                    'slug'            => $c->slug,
                    'thumbnail_image' => $c->thumbnail_image ? storage_link($c->thumbnail_image) : null,
                    'thumbnail_alt'   => $c->thumbnail_alt,
                ]),
                'pagination' => ['total' => $totalCars, 'page' => 1, 'limit' => 6],
            ],
        ]);
    }

}
