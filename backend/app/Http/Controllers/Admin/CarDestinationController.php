<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Car;
use App\Models\CarDestination;
use App\Models\CarDestinationDetails;
use App\Models\CarDestinationFaq;
use App\Models\CarDestinationHighlight;

class CarDestinationController extends Controller
{
    public function index(Request $r)
    {
        if (!$r->ajax() && !$r->filled('destination_search')) {
            return redirect()->route('admin.page-settings.car');
        }

        $query = CarDestination::with(['state', 'location']);

        if ($r->filled('destination_search')) {
            $search = $r->destination_search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('sort_order')->paginate(20));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'        => 'required|string|max:255',
            'state_id'    => 'nullable|exists:states,id',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $slug = Str::slug($r->name);

        if (CarDestination::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Destination already exists.',
            ]);
        }

        CarDestination::create([
            'name'        => $r->name,
            'slug'        => $slug,
            'state_id'    => $r->state_id,
            'location_id' => $r->location_id,
            'sort_order'  => (int) CarDestination::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'destination_list')
            ->with('success', 'Destination created');
    }

    public function showMeta(CarDestination $car_destination)
    {
        return response()->json($car_destination);
    }

    public function showPage(CarDestination $car_destination)
    {
        $data = $car_destination->toArray();
        return response()->json($data);
    }

    public function show($id)
    {
        $carDestination = CarDestination::with(['cars.category', 'state', 'location'])->findOrFail($id);
        return response()->json($carDestination);
    }

    public function update(Request $r, CarDestination $car_destination)
    {
        if ($r->has('status')) {
            $car_destination->is_active = $r->status;
            $car_destination->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $car_destination->is_active,
            ]);
        }

        if ($r->has('is_popular')) {
            $car_destination->is_popular = $r->is_popular;
            $car_destination->save();

            return response()->json([
                'success' => true,
                'message' => 'Popular flag updated successfully',
                'is_popular' => $car_destination->is_popular,
            ]);
        }

        if ($r->exists('meta_setting')) {
            $r->validate([
                'meta_title'       => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string',
                'meta_details'     => 'nullable|string',
            ]);

            $car_destination->update([
                'meta_title'       => $r->meta_title,
                'meta_description' => $r->meta_description,
                'meta_keywords'    => $r->meta_keywords,
                'h1_heading'       => $r->h1_heading,
                'meta_details'     => $r->meta_details,
            ]);

            return redirect()->route('admin.page-settings.car')
                ->with('active_tab', 'destination_list')
                ->with('success', 'Destination meta updated successfully');
        }

        if ($r->exists('page_setting')) {
            $r->validate([
                'title'              => 'required|string|max:255',
                'description'        => 'nullable|string',
                'banner_image'       => 'nullable|string|exists:media,path',
                'banner_image_alt'   => 'nullable|string|max:255',
                'about_title'        => 'nullable|string|max:255',
                'about_image'        => 'nullable|string|exists:media,path',
                'about_image_alt'    => 'nullable|string|max:255',
                'about_description'  => 'nullable|string',
                'distance_text'      => 'nullable|string|max:255',
                'duration_text'      => 'nullable|string|max:255',
                'ideal_for'          => 'nullable|string|max:255',
                'best_season'        => 'nullable|string|max:255',
            ]);

            $bannerImageChanged = $r->has('banner_image') && $r->input('banner_image') !== $car_destination->banner_image;
            $aboutImageChanged  = $r->has('about_image') && $r->input('about_image') !== $car_destination->about_image;

            $bannerPath = $car_destination->banner_image;
            if ($bannerImageChanged) {
                $bannerPath = $r->input('banner_image');
            }

            $aboutPath = $car_destination->about_image;
            if ($aboutImageChanged) {
                $aboutPath = $r->input('about_image');
            }

            $car_destination->update([
                'name'               => $r->title,
                'banner_image'       => $bannerPath,
                'banner_image_alt'   => $r->banner_image_alt,
                'description'        => $r->description,
                'about_title'        => $r->about_title,
                'about_image'        => $aboutPath,
                'about_image_alt'    => $r->about_image_alt,
                'about_description'  => $r->about_description,
                'distance_text'      => $r->distance_text,
                'duration_text'      => $r->duration_text,
                'ideal_for'          => $r->ideal_for,
                'best_season'        => $r->best_season,
            ]);

            return redirect()->route('admin.page-settings.car')
                ->with('active_tab', 'destination_list')
                ->with('success', 'Destination page details updated successfully');
        }

        $r->validate([
            'name'        => 'required|string|max:255',
            'state_id'    => 'nullable|exists:states,id',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        $car_destination->update([
            'name'        => $r->name,
            'state_id'    => $r->state_id,
            'location_id' => $r->location_id,
        ]);

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'destination_list')
            ->with('success', 'Destination updated successfully');
    }

    public function destroy(CarDestination $car_destination)
    {
        // Note: banner_image/about_image are intentionally NOT deleted from storage
        // here — they live in the shared Media Library and may still be used elsewhere.
        \Illuminate\Support\Facades\DB::transaction(function () use ($car_destination) {
            $car_destination->highlights()->delete();
            $car_destination->faqs()->delete();
            $car_destination->delete();
        });
        return response()->json(['success' => true, 'message' => 'Destination deleted successfully']);
    }

    public function slugDuplicateCheck(Request $r)
    {
        return response()->json([
            'exists' => CarDestination::where('slug', Str::slug($r->name))
                ->where('id', '!=', $r->id)
                ->exists(),
        ]);
    }

    public function getDestinationCars($id)
    {
        $all_car = Car::with(['category'])->where('is_active', 1)->get();
        $carDestination = CarDestination::with(['cars'])->findOrFail($id);
        return response()->json(['destination' => $carDestination, 'all_car' => $all_car]);
    }

    public function syncCars(Request $r)
    {
        $r->validate([
            'destination_id' => 'required|integer|exists:car_destinations,id',
            'car_ids'        => 'array',
            'car_ids.*'      => 'integer|exists:cars,id',
        ]);

        $destinationId = $r->destination_id;
        $carIds = $r->car_ids ?? [];

        CarDestinationDetails::where('destination_id', $destinationId)
            ->whereNotIn('car_id', $carIds)
            ->delete();

        $existingCarIds = CarDestinationDetails::where('destination_id', $destinationId)->pluck('car_id')->toArray();
        $newCarIds = array_diff($carIds, $existingCarIds);

        foreach ($newCarIds as $carId) {
            CarDestinationDetails::create(['destination_id' => $destinationId, 'car_id' => $carId]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'destination_list')
            ->with('success', 'Destination car sync successfully');
    }

    public function faqs(Request $r)
    {
        $car_destination = CarDestination::with('faqs')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car_destination' => $car_destination]);
        }
    }

    public function updateFaq(Request $r, CarDestination $car_destination)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_destination) {
            $car_destination->faqs()->delete();
            if ($r->has('faqs')) {
                foreach ($r->faqs as $obj) {
                    CarDestinationFaq::create([
                        'destination_id' => $car_destination->id,
                        'question'       => $obj['question'],
                        'answer'         => $obj['answer'] ?? null,
                    ]);
                }
            }
            $car_destination->faq_title = $r->faq_title;
            $car_destination->faq_sub_title = $r->faq_sub_title;
            $car_destination->save();
        });

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'destination_list')
            ->with('success', 'Faq updated successfully');
    }

    public function highlights(Request $r)
    {
        $car_destination = CarDestination::with('highlights')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car_destination' => $car_destination]);
        }
    }

    public function updateHighlights(Request $r, CarDestination $car_destination)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_destination) {
            $car_destination->highlights()->delete();
            if ($r->has('highlights')) {
                foreach ($r->highlights as $i => $obj) {
                    if (trim($obj['title'] ?? '') === '') {
                        continue;
                    }
                    CarDestinationHighlight::create([
                        'destination_id' => $car_destination->id,
                        'title'          => $obj['title'],
                        'description'    => $obj['description'] ?? null,
                        'sort_order'     => $i,
                    ]);
                }
            }
        });

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'destination_list')
            ->with('success', 'Destination highlights updated successfully');
    }
}
