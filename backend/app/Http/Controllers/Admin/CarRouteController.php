<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Car;
use App\Models\CarRoute;
use App\Models\CarRouteDetails;
use App\Models\CarRouteMetaData;
use App\Models\CarRoutePageDetails;
use App\Models\CarRouteFaq;
use App\Models\CarRouteHighlight;

class CarRouteController extends Controller
{
    /**
     * Show all categories
     */
    public function index(Request $r)
    {
        if (!$r->ajax() && !$r->filled('route_search')) {
            return redirect()->route('admin.page-settings.car');
        }

        $query = CarRoute::query();

        if ($r->filled('route_search')) {
            $search = $r->route_search;
            $query->where(function ($q) use ($search) {
                $q->where('from_location', 'like', "%{$search}%")
                  ->orWhere('to_location', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Store (Add) new category
     */
    public function store(Request $r)
    {
        $r->validate([
            'from_location' => 'required',
            'to_location' => 'required|different:from_location',
        ]);

        $slug = Str::slug($r->from_location.'-to-'.$r->to_location);

        if (CarRoute::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Route already exists.',
            ]);
        }

        $carRoutes = CarRoute::create([
            'slug' => $slug,
            'from_location' => $r->from_location,
            'to_location' => $r->to_location,
        ]);

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','route_list')
        ->with('success','Route created');
    }

    public function showMeta(CarRoute $car_route){
        $car_route->load('meta');
        return response()->json($car_route);
    }

    public function showPage(CarRoute $car_route){
        $car_route->load('details');
        $data = $car_route->toArray();
        return response()->json($data);
    }

    /**
     * Show single category
     */
    public function show($id)
    {
        $carRoute = CarRoute::with(['cars.category', 'details'])->findOrFail($id);
        $data = $carRoute->toArray();
        return response()->json($data);
    }

    /**
     * Update category
     */
    public function update(Request $r, CarRoute $car_route)
    {
        if ($r->has('status')) {
            $car_route->is_active = $r->status;
            $car_route->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $car_route->is_active
            ]);
        }

        if ($r->has('is_popular')) {
            $car_route->is_popular = $r->is_popular;
            $car_route->save();

            return response()->json([
                'success' => true,
                'message' => 'Popular flag updated successfully',
                'is_popular' => $car_route->is_popular
            ]);
        }

        if($r->exists('meta_setting')) {
            
            $r->validate([
                'meta_title'       => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string',
                'meta_details'     => 'nullable|string',
            ]);
            if ($car_route->meta) {
                //print_r($r->toArray());die;
                $car_route->meta->update([
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]);
            } else {
                CarRouteMetaData::create([
                    'route_id' => $car_route->id,
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]);
            }
            return redirect()->route('admin.page-settings.car')
            ->with('active_tab','route_list')
            ->with('success','Route meta updated successfully');
        }

        if ($r->exists('page_setting')) {
            // ✅ Validation — About Section only (Title/Banner/Short Description live on the main Edit form)
            $validatedData = $r->validate([
                'about_title'        => 'nullable|string|max:255',
                'about_image'        => 'nullable|string|exists:media,path',
                'about_image_alt'    => 'nullable|string|max:255',
                'about_description'  => 'nullable|string',
                'distance_text'      => 'nullable|string|max:255',
                'duration_text'      => 'nullable|string|max:255',
                'route_number'       => 'nullable|string|max:255',
                'best_season'        => 'nullable|string|max:255',
            ]);

            $aboutImageChanged = $r->has('about_image') && $r->input('about_image') !== ($car_route->details->about_image ?? null);

            // Handle About-section image (picked/uploaded via the Media Library)
            $aboutPath = $car_route->details->about_image ?? '';
            if ($aboutImageChanged) {
                $aboutPath = $r->input('about_image');
            }

            // ✅ Prepare update data
            $obj = [
                'route_id'           => $car_route->id,
                'about_title'        => $r->about_title,
                'about_image'        => $aboutPath,
                'about_image_alt'    => $r->about_image_alt,
                'about_description'  => $r->about_description,
                'distance_text'      => $r->distance_text,
                'duration_text'      => $r->duration_text,
                'route_number'       => $r->route_number,
                'best_season'        => $r->best_season,
            ];

            if ($car_route->details) {
                $car_route->details->update($obj);
            } else {
                CarRoutePageDetails::create($obj);
            }

            // ✅ Redirect with success message
            return redirect()
                ->route('admin.page-settings.car')
                ->with('active_tab', 'route_list')
                ->with('success', 'Route about section updated successfully');
        }


        $r->validate([
            'from_location'     => 'required',
            'to_location'       => 'required|different:from_location',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'banner_image'      => 'nullable|string|exists:media,path',
            'banner_image_alt'  => 'nullable|string|max:255',
        ]);

        $bannerImageChanged = $r->has('banner_image') && $r->input('banner_image') !== ($car_route->details->banner_image ?? null);

        $car_route->update([
            'from_location' => $r->from_location,
            'to_location' => $r->to_location,
            'display_label' => $r->display_label,
        ]);

        // Get existing banner image path safely
        $path = $car_route->details->banner_image ?? '';

        if ($bannerImageChanged) {
            $path = $r->input('banner_image');
        }

        $pageDetails = [
            'route_id'         => $car_route->id,
            'title'            => $r->title,
            'description'      => $r->description,
            'banner_image'     => $path,
            'banner_image_alt' => $r->banner_image_alt,
        ];

        if ($car_route->details) {
            $car_route->details->update($pageDetails);
        } else {
            CarRoutePageDetails::create($pageDetails);
        }

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','route_list')
        ->with('success','Route updated successfully');
    }

    /**
     * Delete category
     */

    public function destroy(CarRoute $car_route)
    {
        // Note: banner_image/about_image are intentionally NOT deleted from storage
        // here — they live in the shared Media Library and may still be used elsewhere.
        \Illuminate\Support\Facades\DB::transaction(function () use ($car_route) {
            $car_route->meta()->delete();
            $car_route->details()->delete();
            $car_route->faqs()->delete();
            $car_route->highlights()->delete();
            $car_route->delete();
        });

        return response()->json(['success'=>true,'message' => 'Route deleted successfully']);
    }

    public function slugDuplicateCheck(Request $r){ 
        return response()->json([
            'exists' => CarRoute::where('slug', Str::slug($r->from_location.'-to-'.$r->to_location))
            ->where('id', '!=', $r->id)
            ->exists()
        ]);

    }

    public function getRouteCars($id)
    {
        $all_car = Car::with(['category'])->where('is_active', 1)->get();
        $carRoute = CarRoute::with(['cars'])->findOrFail($id);
        return response()->json(['route'=>$carRoute,'all_car'=>$all_car]);
    }

    public function syncCars(Request $r)
    {
        // echo '<pre>';
        // print_r($r->toArray());die;
        $r->validate([
            'route_id' => 'required|integer|exists:car_routes,id',
            'car_ids'  => 'array', // can be empty if user unchecks all
            'car_ids.*' => 'integer|exists:cars,id',
        ]);

        $routeId = $r->route_id;
        $carIds  = $r->car_ids ?? []; // selected cars (checked)

        // 🔄 Sync logic:
        // 1️⃣ Delete cars not in the list.
        CarRouteDetails::where('route_id', $routeId)
            ->whereNotIn('car_id', $carIds)
            ->delete();

        // 2️⃣ Add new cars that don’t already exist.
        $existingCarIds = CarRouteDetails::where('route_id', $routeId)
            ->pluck('car_id')
            ->toArray();

        $newCarIds = array_diff($carIds, $existingCarIds);

        foreach ($newCarIds as $carId) {
            CarRouteDetails::create([
                'route_id' => $routeId,
                'car_id'   => $carId,
            ]);
        }

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','route_list')
        ->with('success','Route car sync successfully');
    }

    public function faqs(Request $r){
        $car_route = CarRoute::with('faqs')->where('id',$r->id)->first();
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'car_route'    => $car_route
            ]);
        }
    }

    public function updateFaq(Request $r, CarRoute $car_route){
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_route) {
            $car_route->faqs()->delete();
            if ($r->has('faqs')) {
                foreach ($r->faqs as $obj) {
                    CarRouteFaq::create([
                        'route_id'  => $car_route->id,
                        'question' => $obj['question'],
                        'answer'   => $obj['answer'] ?? null,
                    ]);
                }
            }
            $car_route->faq_title = $r->faq_title;
            $car_route->faq_sub_title = $r->faq_sub_title;
            $car_route->save();
        });
        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','route_list')
        ->with('success', 'Faq updated successfully');
    }

    public function highlights(Request $r){
        $car_route = CarRoute::with('highlights')->where('id',$r->id)->first();
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'car_route'    => $car_route
            ]);
        }
    }

    public function updateHighlights(Request $r, CarRoute $car_route){
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_route) {
            $car_route->highlights()->delete();
            if ($r->has('highlights')) {
                foreach ($r->highlights as $i => $obj) {
                    if (trim($obj['title'] ?? '') === '') {
                        continue;
                    }
                    CarRouteHighlight::create([
                        'route_id'    => $car_route->id,
                        'title'       => $obj['title'],
                        'description' => $obj['description'] ?? null,
                        'sort_order'  => $i,
                    ]);
                }
            }
        });
        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','route_list')
        ->with('success', 'Route highlights updated successfully');
    }

}
