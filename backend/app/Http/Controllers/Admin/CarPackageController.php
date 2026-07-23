<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Car;
use App\Models\CarPackage;
use App\Models\CarPackageDetails;
use App\Models\CarPackageFaq;
use App\Models\CarPackageStop;

class CarPackageController extends Controller
{
    public function index(Request $r)
    {
        if (!$r->ajax() && !$r->filled('package_search')) {
            return redirect()->route('admin.page-settings.car');
        }

        $query = CarPackage::query();

        if ($r->filled('package_search')) {
            $search = $r->package_search;
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
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($r->name);

        if (CarPackage::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Package already exists.',
            ]);
        }

        CarPackage::create([
            'name' => $r->name,
            'slug' => $slug,
            'sort_order' => (int) CarPackage::max('sort_order') + 1,
        ]);

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'package_list')
            ->with('success', 'Package created');
    }

    public function showMeta(CarPackage $car_package)
    {
        return response()->json($car_package);
    }

    public function showPage(CarPackage $car_package)
    {
        $data = $car_package->toArray();
        return response()->json($data);
    }

    public function show($id)
    {
        $carPackage = CarPackage::with(['cars.category', 'stops'])->findOrFail($id);
        $data = $carPackage->toArray();
        return response()->json($data);
    }

    public function update(Request $r, CarPackage $car_package)
    {
        if ($r->has('status')) {
            $car_package->is_active = $r->status;
            $car_package->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $car_package->is_active,
            ]);
        }

        if ($r->has('is_popular')) {
            $car_package->is_popular = $r->is_popular;
            $car_package->save();

            return response()->json([
                'success' => true,
                'message' => 'Popular flag updated successfully',
                'is_popular' => $car_package->is_popular,
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

            $car_package->update([
                'meta_title'       => $r->meta_title,
                'meta_description' => $r->meta_description,
                'meta_keywords'    => $r->meta_keywords,
                'h1_heading'       => $r->h1_heading,
                'meta_details'     => $r->meta_details,
            ]);

            return redirect()->route('admin.page-settings.car')
                ->with('active_tab', 'package_list')
                ->with('success', 'Package meta updated successfully');
        }

        if ($r->exists('page_setting')) {
            $r->validate([
                'about_title'        => 'nullable|string|max:255',
                'about_image'        => 'nullable|string|exists:media,path',
                'about_image_alt'    => 'nullable|string|max:255',
                'about_description'  => 'nullable|string',
                'duration_text'      => 'nullable|string|max:255',
                'best_season'        => 'nullable|string|max:255',
                'ideal_for'          => 'nullable|string|max:255',
            ]);

            $aboutImageChanged = $r->has('about_image') && $r->input('about_image') !== $car_package->about_image;

            $aboutPath = $car_package->about_image;
            if ($aboutImageChanged) {
                $aboutPath = $r->input('about_image');
            }

            $car_package->update([
                'about_title'        => $r->about_title,
                'about_image'        => $aboutPath,
                'about_image_alt'    => $r->about_image_alt,
                'about_description'  => $r->about_description,
                'duration_text'      => $r->duration_text,
                'best_season'        => $r->best_season,
                'ideal_for'          => $r->ideal_for,
            ]);

            return redirect()->route('admin.page-settings.car')
                ->with('active_tab', 'package_list')
                ->with('success', 'Package about section updated successfully');
        }

        $r->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'banner_image'      => 'nullable|string|exists:media,path',
            'banner_image_alt'  => 'nullable|string|max:255',
        ]);

        $bannerImageChanged = $r->has('banner_image') && $r->input('banner_image') !== $car_package->banner_image;

        $bannerPath = $car_package->banner_image;
        if ($bannerImageChanged) {
            $bannerPath = $r->input('banner_image');
        }

        $car_package->update([
            'name'             => $r->name,
            'banner_image'     => $bannerPath,
            'banner_image_alt' => $r->banner_image_alt,
            'description'      => $r->description,
        ]);

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'package_list')
            ->with('success', 'Package updated successfully');
    }

    public function destroy(CarPackage $car_package)
    {
        // Note: banner_image/about_image/amenities are intentionally NOT deleted from
        // storage here — they live in the shared Media Library and may still be used elsewhere.
        \Illuminate\Support\Facades\DB::transaction(function () use ($car_package) {
            $car_package->stops()->delete();
            $car_package->faqs()->delete();
            $car_package->amenities()->delete();
            $car_package->delete();
        });
        return response()->json(['success' => true, 'message' => 'Package deleted successfully']);
    }

    public function slugDuplicateCheck(Request $r)
    {
        return response()->json([
            'exists' => CarPackage::where('slug', Str::slug($r->name))
                ->where('id', '!=', $r->id)
                ->exists(),
        ]);
    }

    public function getPackageCars($id)
    {
        $all_car = Car::with(['category'])->where('is_active', 1)->get();
        $carPackage = CarPackage::with(['cars'])->findOrFail($id);
        return response()->json(['package' => $carPackage, 'all_car' => $all_car]);
    }

    public function syncCars(Request $r)
    {
        $r->validate([
            'package_id' => 'required|integer|exists:car_packages,id',
            'car_ids'    => 'array',
            'car_ids.*'  => 'integer|exists:cars,id',
        ]);

        $packageId = $r->package_id;
        $carIds = $r->car_ids ?? [];

        CarPackageDetails::where('package_id', $packageId)
            ->whereNotIn('car_id', $carIds)
            ->delete();

        $existingCarIds = CarPackageDetails::where('package_id', $packageId)->pluck('car_id')->toArray();
        $newCarIds = array_diff($carIds, $existingCarIds);

        foreach ($newCarIds as $carId) {
            CarPackageDetails::create(['package_id' => $packageId, 'car_id' => $carId]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'package_list')
            ->with('success', 'Package car sync successfully');
    }

    public function faqs(Request $r)
    {
        $car_package = CarPackage::with('faqs')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car_package' => $car_package]);
        }
    }

    public function updateFaq(Request $r, CarPackage $car_package)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_package) {
            $car_package->faqs()->delete();
            if ($r->has('faqs')) {
                foreach ($r->faqs as $obj) {
                    CarPackageFaq::create([
                        'package_id' => $car_package->id,
                        'question'   => $obj['question'],
                        'answer'     => $obj['answer'] ?? null,
                    ]);
                }
            }
            $car_package->faq_title = $r->faq_title;
            $car_package->faq_sub_title = $r->faq_sub_title;
            $car_package->save();
        });

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'package_list')
            ->with('success', 'Faq updated successfully');
    }

    public function stops(Request $r)
    {
        $car_package = CarPackage::with('stops')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car_package' => $car_package]);
        }
    }

    public function updateStops(Request $r, CarPackage $car_package)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_package) {
            $car_package->stops()->delete();
            if ($r->has('stops')) {
                foreach ($r->stops as $i => $obj) {
                    if (trim($obj['name'] ?? '') === '') {
                        continue;
                    }
                    $attractions = array_values(array_filter(array_map('trim', explode("\n", $obj['attractions'] ?? ''))));
                    CarPackageStop::create([
                        'package_id'  => $car_package->id,
                        'state_id'    => $obj['state_id'] ?? null,
                        'location_id' => $obj['location_id'] ?? null,
                        'name'        => $obj['name'],
                        'description' => $obj['description'] ?? null,
                        'attractions' => $attractions,
                        'sort_order'  => $i,
                    ]);
                }
            }
        });

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'package_list')
            ->with('success', 'Package stops updated successfully');
    }

    public function amenities(Request $r)
    {
        $car_package = CarPackage::with('amenities')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car_package' => $car_package->toArray()]);
        }
    }

    public function updateAmenities(Request $r, CarPackage $car_package)
    {
        $r->validate([
            'amenities.*.label'       => 'nullable|string|max:255',
            'amenities.*.description' => 'nullable|string|max:255',
            'amenities.*.icon'        => 'nullable|string|exists:media,path',
        ]);

        $rows = [];

        foreach ($r->input('amenities', []) as $i => $obj) {
            if (trim($obj['label'] ?? '') === '') {
                continue;
            }
            $rows[] = ['label' => $obj['label'], 'description' => $obj['description'] ?? null, 'icon' => $obj['icon'] ?? null];
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($car_package, $rows) {
            $car_package->amenities()->delete();

            foreach ($rows as $i => $row) {
                $car_package->amenities()->create([
                    'label'       => $row['label'],
                    'description' => $row['description'],
                    'icon'        => $row['icon'],
                    'sort_order'  => $i,
                ]);
            }
        });

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'package_list')
            ->with('success', 'Features & Amenities saved.');
    }
}
