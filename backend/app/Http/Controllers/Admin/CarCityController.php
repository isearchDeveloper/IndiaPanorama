<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Car;
use App\Models\CarCity;
use App\Models\CarCityDetails;
use App\Models\CarCityMetaData;
use App\Models\CarCityPageDetails;
use App\Models\CarCityFaq;
use Illuminate\Support\Facades\Storage;

class CarCityController extends Controller
{
    /**
     * Show all categories
     */
    public function index(Request $r)
    {
        if (!$r->ajax() && !$r->filled('city_search')) {
            return redirect()->route('admin.page-settings.car');
        }

        $query = CarCity::query();

        if ($r->filled('city_search')) {
            $search = $r->city_search;
            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
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
            'location' => 'required',
            'thumbnail_image' => 'required|string|exists:media,path',
            'thumbnail_alt' => 'nullable|string|max:255'

        ]);

        $slug = Str::slug($r->location);

        if (CarCity::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'City already exists.',
            ]);
        }

        $data = [
            'slug' => $slug,
            'location' => $r->location,
            'thumbnail_alt' => $r->thumbnail_alt,
            'is_active' => 1,
            // thumbnail_image has historically stored a full URL (not a bare relative
            // path like every other module) — preserved here for consistency with
            // existing rows and the API's direct (non-storage_link) consumption of it.
            'thumbnail_image' => Storage::disk(config('filesystems.upload_disk'))->url($r->input('thumbnail_image')),
        ];

        $carCity = CarCity::create($data);

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','city_list')
        ->with('success','City created');
    }

    public function showMeta(CarCity $car_city){
        $car_city->load('meta');
        return response()->json($car_city);
    }

    /**
     * Show single category
     */
    public function show($id)
    {
        $carCity = CarCity::with(['cars.category', 'details'])->findOrFail($id);
        $data = $carCity->toArray();
        $data['thumbnail_image_path'] = $carCity->thumbnail_image
            ? ltrim(parse_url($carCity->thumbnail_image, PHP_URL_PATH), '/')
            : null;
        return response()->json($data);
    }

    /**
     * Update category
     */
    public function update(Request $r, CarCity $car_city)
    {
        if ($r->has('status')) {
            $car_city->is_active = $r->status;
            $car_city->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $car_city->is_active
            ]);
        }

        if ($r->has('is_popular')) {
            $car_city->is_popular = $r->is_popular;
            $car_city->save();

            return response()->json([
                'success' => true,
                'message' => 'Popular flag updated successfully',
                'is_popular' => $car_city->is_popular
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
            if ($car_city->meta) {
                //print_r($r->toArray());die;
                $car_city->meta->update([
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]);
            } else {
                CarCityMetaData::create([
                    'city_id' => $car_city->id,
                    'meta_title' => $r->meta_title,
                    'meta_description' => $r->meta_description,
                    'meta_keywords' => $r->meta_keywords,
                    'h1_heading' => $r->h1_heading,
                    'meta_details' => $r->meta_details,
                ]);
            }
            return redirect()->route('admin.page-settings.car')
            ->with('active_tab','city_list')
            ->with('success','City meta updated successfully');
        }

        if ($r->exists('page_setting')) {
            $r->validate([
                'description'          => 'nullable|string',
                'gallery_title'        => 'nullable|string|max:255',
                'gallery_description'  => 'nullable|string',
            ]);

            if ($r->has('description')) {
                $car_city->update([
                    'why_choose_enabled' => $r->boolean('why_choose_enabled'),
                ]);
            }

            // Only touch the fields this particular form actually submitted —
            // the Gallery modal posts here too, so a blind overwrite would
            // null out the description owned by the other form.
            $pageDetails = $r->only(['description', 'gallery_title', 'gallery_description']);

            if ($car_city->details) {
                $car_city->details->update($pageDetails);
            } else {
                CarCityPageDetails::create($pageDetails + ['city_id' => $car_city->id]);
            }

            return redirect()->route('admin.page-settings.car')
                ->with('active_tab', 'city_list')
                ->with('success', 'City page settings updated successfully');
        }

        $r->validate([
            'location' => 'required|string|max:255',
            'thumbnail_alt' => 'nullable|string|max:255',
            'thumbnail_image' => 'nullable|string|exists:media,path',
            'title' => 'required|string|max:255',
        ]);

        // thumbnail_image is historically stored as a full URL (not a bare relative
        // path) — recover the bare path to compare against what the picker submits.
        $existingThumbnailPath = $car_city->thumbnail_image
            ? ltrim(parse_url($car_city->thumbnail_image, PHP_URL_PATH), '/')
            : null;
        $thumbnailChanged = $r->has('thumbnail_image') && $r->input('thumbnail_image') !== $existingThumbnailPath;

        $data = [
            'location' => $r->location,
            'thumbnail_alt' => $r->thumbnail_alt,
            'display_label' => $r->display_label,
        ];

        if ($thumbnailChanged) {
            $data['thumbnail_image'] = Storage::disk(config('filesystems.upload_disk'))->url($r->input('thumbnail_image'));
        }

        $car_city->update($data);

        $pageDetails = [
            'city_id' => $car_city->id,
            'title' => $r->title,
        ];

        if ($car_city->details) {
            $car_city->details->update($pageDetails);
        } else {
            CarCityPageDetails::create($pageDetails);
        }

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','city_list')
        ->with('success','City updated successfully');
    }

    /**
     * Delete category
     */

    public function destroy(CarCity $car_city)
    {
        // Note: thumbnail_image is intentionally NOT deleted from storage here —
        // it lives in the shared Media Library and may still be used elsewhere.
        \Illuminate\Support\Facades\DB::transaction(function () use ($car_city) {
            foreach ($car_city->galleryImages as $item) {
                if ($item->image && Storage::disk(config('filesystems.upload_disk'))->exists($item->image)) {
                    Storage::disk(config('filesystems.upload_disk'))->delete($item->image);
                }
            }
            $car_city->meta()->delete();
            $car_city->details()->delete();
            $car_city->faqs()->delete();
            $car_city->features()->delete();
            $car_city->benefits()->delete();
            $car_city->galleryImages()->delete();
            $car_city->whyChooseStats()->delete();
            $car_city->highlights()->delete();
            $car_city->delete();
        });

        return response()->json(['success'=>true,'message' => 'City deleted successfully']);
    }

    public function slugDuplicateCheck(Request $r){
        return response()->json([ 
            'exists' => CarCity::where('slug', Str::slug($r->location))
            ->where('id', '!=', $r->id)
            ->exists()
        ]);

    }

    public function getCityCars($id)
    {
        $all_car = Car::with(['category'])->where('is_active', 1)->get();
        $carCity = CarCity::with(['cars'])->findOrFail($id);
        return response()->json(['city'=>$carCity,'all_car'=>$all_car]);
    }

    public function syncCars(Request $r)
    {
        // echo '<pre>';
        // print_r($r->toArray());die;
        $r->validate([
            'city_id' => 'required|integer|exists:car_city,id',
            'car_ids'  => 'array', // can be empty if user unchecks all
            'car_ids.*' => 'integer|exists:cars,id',
        ]);

        $cityId = $r->city_id;
        $carIds  = $r->car_ids ?? []; // selected cars (checked)

        // 🔄 Sync logic:
        // 1️⃣ Delete cars not in the list.
        CarCityDetails::where('city_id', $cityId)
            ->whereNotIn('car_id', $carIds)
            ->delete();

        // 2️⃣ Add new cars that don’t already exist.
        $existingCarIds = CarCityDetails::where('city_id', $cityId)
            ->pluck('car_id')
            ->toArray();

        $newCarIds = array_diff($carIds, $existingCarIds);

        foreach ($newCarIds as $carId) {
            CarCityDetails::create([
                'city_id' => $cityId,
                'car_id'   => $carId,
            ]);
        }

        return redirect()->route('admin.page-settings.car')
        ->with('active_tab','city_list')
        ->with('success','Route car sync successfully');
    }

    public function faqs(Request $r){
        $car_city = CarCity::with('faqs')->where('id',$r->id)->first();
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'car_city'    => $car_city
            ]);
        }
    }

    public function updateFaq(Request $r, CarCity $car_city){
        $car_city->faqs()->delete();
        if ($r->has('faqs')) {
            foreach ($r->faqs as $obj) {
                CarCityFaq::create([
                    'city_id'  => $car_city->id,
                    'question' => $obj['question'],
                    'answer'   => $obj['answer'] ?? null,
                ]);
            }
        }
        $car_city->faq_title = $r->faq_title;
        $car_city->faq_sub_title = $r->faq_sub_title;
        $car_city->save();
        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','city_list')
        ->with('success', 'Faq updated successfully');
    }

    public function updateFeatures(Request $r, CarCity $car_city)
    {
        $car_city->features()->delete();
        if ($r->has('items')) {
            foreach ($r->items as $i => $text) {
                if (trim($text) === '') {
                    continue;
                }
                $car_city->features()->create(['text' => $text, 'sort_order' => $i]);
            }
        }
        $car_city->features_title = $r->features_title;
        $car_city->save();

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Features saved successfully']);
        }

        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','city_list')
        ->with('success', 'Features saved successfully');
    }

    public function updateBenefits(Request $r, CarCity $car_city)
    {
        $car_city->benefits()->delete();
        if ($r->has('items')) {
            foreach ($r->items as $i => $text) {
                if (trim($text) === '') {
                    continue;
                }
                $car_city->benefits()->create(['text' => $text, 'sort_order' => $i]);
            }
        }
        $car_city->benefits_title = $r->benefits_title;
        $car_city->save();

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Benefits saved successfully']);
        }

        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','city_list')
        ->with('success', 'Benefits saved successfully');
    }

    public function addGalleryImage(Request $r, CarCity $car_city)
    {
        $r->validate([
            'gallery_images'         => 'nullable|array',
            'gallery_images.*.path'  => 'required|string|exists:media,path',
            'gallery_images.*.alt'   => 'nullable|string|max:255',
            'gallery_title'          => 'nullable|string|max:255',
            'gallery_description'    => 'nullable|string',
        ]);

        $galleryFields = $r->only(['gallery_title', 'gallery_description']);
        if ($car_city->details) {
            $car_city->details->update($galleryFields);
        } else {
            CarCityPageDetails::create($galleryFields + ['city_id' => $car_city->id]);
        }

        $sortOrder = $car_city->galleryImages()->count();
        $images = [];

        foreach ($r->input('gallery_images', []) as $item) {
            if (empty($item['path'])) continue;
            $image = $car_city->galleryImages()->create([
                'image'      => $item['path'],
                'image_alt'  => $item['alt'] ?? null,
                'sort_order' => $sortOrder++,
            ]);
            $images[] = $image;
        }

        if ($r->ajax()) {
            return response()->json(['status' => true, 'message' => 'Images added.', 'images' => $images]);
        }

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'city_list')
            ->with('open_city_gallery_modal', $car_city->id)
            ->with('success', 'Gallery images added.');
    }

    public function deleteGalleryImage(\App\Models\CarCityGalleryImage $gallery_image)
    {
        $gallery_image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image removed.']);
    }

    public function getFeaturesBenefits(Request $r)
    {
        $car_city = CarCity::with(['features', 'benefits', 'galleryImages', 'details'])->where('id', $r->id)->first();
        return response()->json(['status' => 'success', 'car_city' => $car_city]);
    }

    public function highlights(Request $r)
    {
        $car_city = CarCity::with('highlights')->where('id', $r->id)->first();
        if ($r->ajax()) {
            return response()->json(['status' => 'success', 'car_city' => $car_city]);
        }
    }

    public function updateHighlights(Request $r, CarCity $car_city)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($r, $car_city) {
            $car_city->highlights()->delete();
            if ($r->has('highlights')) {
                foreach ($r->highlights as $i => $obj) {
                    if (trim($obj['title'] ?? '') === '') {
                        continue;
                    }
                    \App\Models\CarCityHighlight::create([
                        'city_id'     => $car_city->id,
                        'title'       => $obj['title'],
                        'description' => $obj['description'] ?? null,
                        'sort_order'  => $i,
                    ]);
                }
            }
        });

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'city_list')
            ->with('success', 'City highlights updated successfully');
    }

}
