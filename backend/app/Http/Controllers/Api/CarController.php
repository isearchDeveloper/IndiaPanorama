<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\CarCategoryResource;
use App\Http\Resources\CarResource;
use App\Http\Resources\CarRouteResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\CarCityResource;

use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarRoute;
use App\Models\CarCity;


class CarController extends Controller{

    public function index(Request $r)
    {
        $limit     = $r->get('limit', list_config()['limit']); // default limit
        $page      = $r->get('page', 1); // current page
        $orderBy   = list_config()['order_by']; // default order by
        $direction = list_config()['direction']; // default desc
        $categorySlug = $r->get('category');

        $carsQuery = Car::where('is_active', 1)
            ;

        // 🔹 If category slug is provided, filter by category_id
        if ($categorySlug) {
            $category = CarCategory::where('slug', $categorySlug)->first();
            if ($category) {
                $carsQuery->where('category_id', $category->id);
            } else {
                // If slug not found, return empty result
                return response()->json([
                    'status' => 'success',
                    'message' => 'Listing',
                    'data' => [
                        'cars' => [],
                        'pagination'  => [
                            'total' => 0,
                            'page'  => (int) $page,
                            'limit' => (int) $limit,
                        ]
                    ]
                ], 200);
            }
        }

        // 🔹 Get total count before pagination
        $totalCars = $carsQuery->count();

        // 🔹 Apply pagination
        $cars = $carsQuery->orderBy($orderBy, $direction)
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Listing',
            'data' => [
                'cars' =>  CarResource::collection($cars),
                'pagination'  => [
                    'total' => $totalCars,
                    'page'  => (int) $page,
                    'limit' => (int) $limit,
                ]
            ]
        ], 200);
    }


    public function getCarsByRoute(Request $r)
    {

        $limit        = $r->get('limit', 9); // default limit
        $page         = $r->get('page', 1); // current page
        $orderBy      = list_config()['order_by']; // default order by
        $direction    = list_config()['direction']; // default direction
        $routeSlug    = $r->get('route');
        $categorySlug = $r->get('category');
        $fromLocation = explode('-to-', $routeSlug)[0];

        $routes = CarRoute::where('is_active', 1)
            ->with('details')
            ->where('slug', '!=', $routeSlug)
            ->where('from_location', 'LIKE', $fromLocation . '%')
            ->orderBy('id','desc')
            ->get();

        // 🔹 Find the route with its cars & meta
        $route = CarRoute::with(['cars.category', 'meta','details','faqs'])
            ->where('slug', $routeSlug)
            ->firstOrFail();

        // 🔹 Base query for route cars
        $carsQuery = $route->cars()
            ->where('is_active', 1)
            ->with('category')
            ->orderBy($orderBy, $direction);

        // 🔹 Get all route cars for unique category extraction
        $allRouteCars = $route->cars()
            ->where('is_active', 1)
            ->with('category')
            ->get();

        // 🔹 Extract unique categories
        $uniqueCategories = $allRouteCars
            ->pluck('category')
            ->unique('id')
            ->values();

        // 🔹 Filter by category slug (if provided)
        if ($categorySlug) {
            $category = CarCategory::where('slug', $categorySlug)->first();
            if ($category) {
                $carsQuery->where('category_id', $category->id);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No cars found for this category',
                    'data' => [
                        'route'      => new CarRouteResource($route),
                        'categories' => [],
                        'cars'       => [],
                        'pagination' => [
                            'total' => 0,
                            'page'  => (int) $page,
                            'limit' => (int) $limit,
                        ]
                    ]
                ], 200);
            }
        }

        // 🔹 Total before pagination
        $totalCars = $carsQuery->count();

        $fleets = $allRouteCars
            ->groupBy(fn($car) => $car->category->slug ?? 'uncategorized')
            ->map(function ($group) {
                return $group->take(5)->values();
            });
        // 🔹 Apply pagination
        $cars = $carsQuery->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // 🔹 Final response
        return response()->json([
            'status' => 'success',
            'message' => 'Listing',
            'data' => [
                'route'      => new CarRouteResource($route), // ✅ FIXED (not collection)
                'categories' => CarCategoryResource::collection($uniqueCategories),
                'cars'       => CarResource::collection($cars),
                'abc'   => $routeSlug,
                'routes'     => CarRouteResource::collection($routes),
                'fleets'     => $fleets->map(fn($group) => CarResource::collection($group)),
                'pagination' => [
                    'total' => $totalCars,
                    'page'  => (int) $page,
                    'limit' => (int) $limit,
                ]
            ]
        ], 200);
    }

    public function getCarsByCity(Request $r)
    {

        $limit        = $r->get('limit', 9); // default limit
        $page         = $r->get('page', 1); // current page
        $orderBy      = list_config()['order_by']; // default order by
        $direction    = list_config()['direction']; // default direction
        $citySlug    = $r->get('city');
        $categorySlug = $r->get('category');

        // 🔹 Find the route with its cars & meta
        $city = CarCity::with(['cars.category', 'meta','details','faqs'])
            ->where('slug', $citySlug)
            ->firstOrFail();

        // 🔹 Base query for route cars
        $carsQuery = $city->cars()
            ->where('is_active', 1)
            ->with('category')
            ->orderBy($orderBy, $direction);

        // 🔹 Get all route cars for unique category extraction
        $allCityCars = $city->cars()
            ->where('is_active', 1)
            ->with('category')
            ->get();

        // 🔹 Extract unique categories
        $uniqueCategories = $allCityCars
            ->pluck('category')
            ->unique('id')
            ->values();

        // 🔹 Filter by category slug (if provided)
        if ($categorySlug) {
            $category = CarCategory::where('slug', $categorySlug)->first();
            if ($category) {
                $carsQuery->where('category_id', $category->id);
            } else {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No cars found for this category',
                    'data' => [
                        'city'      => new CarRouteResource($city),
                        'categories' => [],
                        'cars'       => [],
                        'pagination' => [
                            'total' => 0,
                            'page'  => (int) $page,
                            'limit' => (int) $limit,
                        ]
                    ]
                ], 200);
            }
        }

        // 🔹 Total before pagination
        $totalCars = $carsQuery->count();

        $fleets = $allCityCars
            ->groupBy(fn($car) => $car->category->slug ?? 'uncategorized')
            ->map(function ($group) {
                return $group->take(5)->values();
            });
        // 🔹 Apply pagination
        $cars = $carsQuery->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();
            
        $routes = CarRoute::where('is_active', 1)
            ->with('details')
            ->where('from_location',$citySlug)
            ->orderBy('id','desc')
            ->get();

        // 🔹 Final response
        return response()->json([
            'status' => 'success',
            'message' => 'Listing',
            'data' => [
                'city'      => new CarCityResource($city), // ✅ FIXED (not collection)
                'categories' => CarCategoryResource::collection($uniqueCategories),
                'cars'       => CarResource::collection($cars),
                'routes'     => CarRouteResource::collection($routes),
                'fleets'     => $fleets->map(fn($group) => CarResource::collection($group)),
                'pagination' => [
                    'total' => $totalCars,
                    'page'  => (int) $page,
                    'limit' => (int) $limit,
                ]
            ]
        ], 200);
    }


}
