<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarCity;
use App\Models\CarRoute;
use App\Models\CarRentalContent;
use App\Models\State;
use App\Models\HomeAboutFeature;
use App\Models\HomeSection;
use App\Models\Location;
use App\Models\HomeBlogItem;
use App\Models\Package;
use App\Models\Page;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function home()
    {
        // ── 1. Hero Slider banners ───────────────────────────────────────
        $banners = Banner::where('is_static', '!=', 1)
            ->orderBy('sort_order')->orderBy('id')
            ->get();

        // ── 2. Customized Tours: all active packages for search/select ───
        $all_packages = Package::where('is_active', 1)
            ->select('id', 'title', 'slug', 'is_customized')
            ->orderBy('title')
            ->get();

        // ── 3. Trusted Tour Operator: feature list ───────────────────────
        $about_features = HomeAboutFeature::ordered()->get();

        // ── 4. Latest Blogs — admin-managed items ───────────────────────
        $blog_items = HomeBlogItem::ordered()->get();

        // ── Section CMS keys (title, subtitle, visibility, etc.) ─────────
        $sections = HomeSection::allKeyed();

        // ── Home page SEO meta ────────────────────────────────────────────
        // The public Home API (page_id = 8) and this "Meta Setting" modal both
        // hardcode id 8 for the home page's Page/meta record — self-heal it
        // here if it's ever missing (e.g. a fresh DB) instead of silently
        // hiding the button and leaving the live site's SEO tags empty.
        $page_details = Page::with('meta')->firstOrCreate(
            ['id' => 8],
            ['slug' => 'home', 'title' => 'Home', 'description' => '', 'banner_image' => '']
        );

        return view('admin.settings.home', compact(
            'banners',
            'all_packages',
            'about_features',
            'blog_items',
            'sections',
            'page_details'
        ));
    }

    public function car(Request $request)
    {
        if ($request->has('active_tab')) {
            session(['active_tab' => $request->active_tab]);
        }

        $page_details = Page::find(6);
        $locations    = Location::where('country_id', 1)->where('is_active', 1)->get();
        $categories   = CarCategory::get();

        $car_routes = CarRoute::with(['cars.category'])
            ->when($request->filled('route_search'), function ($q) use ($request) {
                $search = $request->route_search;
                $q->where(function ($q) use ($search) {
                    $q->where('from_location', 'like', "%{$search}%")
                      ->orWhere('to_location',   'like', "%{$search}%")
                      ->orWhere('slug',            'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(25);

        $car_city = CarCity::with(['cars.category'])
            ->when($request->filled('city_search'), function ($q) use ($request) {
                $search = $request->city_search;
                $q->where(function ($q) use ($search) {
                    $q->where('location', 'like', "%{$search}%")
                      ->orWhere('slug',     'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(25);

        $cars = Car::with('category')->orderByDesc('id')->get();

        $car_packages = \App\Models\CarPackage::with(['cars.category'])
            ->when($request->filled('package_search'), function ($q) use ($request) {
                $search = $request->package_search;
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(25);

        $car_destinations = \App\Models\CarDestination::with(['cars.category', 'state', 'location'])
            ->when($request->filled('destination_search'), function ($q) use ($request) {
                $search = $request->destination_search;
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(25);

        $car_content = CarRentalContent::current()->load(['checklistItems', 'galleryImages', 'whyChooseStats', 'amenities']);
        $states      = State::where('is_active', 1)->orderBy('name')->get(['id', 'name']);

        return view('admin.settings.car', compact(
            'page_details', 'locations', 'categories', 'cars', 'car_routes', 'car_city',
            'car_packages', 'car_destinations',
            'car_content', 'states'
        ));
    }

}
