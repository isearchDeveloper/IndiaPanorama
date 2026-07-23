<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;

use Illuminate\Http\Request;
use App\Models\Category;



class CategoryController extends Controller{

    public function packagesByCategory(Request $r)
    {
        $limit     = (int) $r->get('limit', list_config()['limit']);
        $orderBy   = $r->get('order_by', list_config()['order_by']);
        $direction = $r->get('direction', list_config()['direction']);
        $categorySlug = $r->category; // filter by slug

        // Find category by slug
        $category = Category::where('is_active', 1)
            ->where('slug', $categorySlug)
            ->first();
        //print_r($category->toArray());die;
        // If category not found → return empty
        if (! $category) {
            return response()->json([
                'status'  => 'success',
                'message' => 'No packages found',
                'data'    => [
                    'category'   => null,
                    'packages'   => [],
                    'pagination' => [
                        'total' => 0,
                        'limit' => $limit,
                        'page'  => (int) $r->get('page', 1),
                    ],
                ],
            ], 200);
        }

        $query = $category->packages()
            ->where('is_active', 1)
            ->when($r->filled('type'), function ($q) use ($r) {
                $q->where('type', $r->type);
            })
            ->when($r->filled('country'), function ($q) use ($r) {
                $q->whereHas('country', function ($cq) use ($r) {
                    $cq->where('slug', $r->country);
                });
            })
            ->when($r->filled('location'), function ($q) use ($r) {
                $q->whereHas('location', function ($cq) use ($r) {
                    $cq->where('slug', $r->location);
                });
            })
            ->with(['details', 'location.details']);


        //print_r($query->get()->toArray());die;
        // Total count
        $total = $query->count();

        // Pagination
        $page  = (int) $r->get('page', 1);
        $paged = $query->orderBy($orderBy, $direction)->forPage($page, $limit)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing Packages',
            'data'    => [
                'packages'   => PackageResource::collection($paged),
                'pagination' => [
                    'total'     => $total,
                    'limit'     => $limit,
                    'page'      => $page
                ],
            ],
        ], 200);
    }

    public function countryPackagesByCategory(Request $r)
    {
        $limit     = (int) $r->get('limit', list_config()['limit']);
        $orderBy   = $r->get('order_by', list_config()['order_by']);
        $direction = $r->get('direction', list_config()['direction']);
        $categorySlug = $r->get('slug'); // filter by slug

        // Find category by slug
        $category = Category::where('is_active', 1)
            ->where('slug', $categorySlug)
            ->first();

        // If category not found → return empty
        // if (! $category) {
        //     return response()->json([
        //         'status'  => 'success',
        //         'message' => 'No packages found',
        //         'data'    => [
        //             'category'   => null,
        //             'packages'   => [],
        //             'pagination' => [
        //                 'total' => 0,
        //                 'limit' => $limit,
        //                 'page'  => (int) $r->get('page', 1),
        //             ],
        //         ],
        //     ], 200);
        // }

        // Get country packages for this category
        $query = $category->allPackages()
            ->where('is_active', 1)
            ->whereHas('country', function ($q) use ($r) {
                $q->where('slug', $r->country);
            }) 
            ->with(['details','location.details']);

        // Total count
        $total = $query->count();

        // Pagination
        $page  = (int) $r->get('page', 1);
        $paged = $query->orderBy($orderBy, $direction)->forPage($page, $limit)->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Listing',
            'data'    => [
                'packages'   => PackageResource::collection($paged),
                'pagination' => [
                    'total'     => $total,
                    'limit'     => $limit,
                    'page'      => $page
                ],
            ],
        ], 200);
    }

}
