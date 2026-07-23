<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use Illuminate\Support\Str;

class CarController extends Controller {

    public function index(Request $r){
        if ($r->exists('id')) {
            return response()->json(['car'=> Car::where('id',$r->id)->first()]);
        }

        return redirect()->route('admin.page-settings.car');
    }

    public function store(Request $r){
        // echo '<pre>';
        // print_r($r->toArray()); die;
        $validated = $r->validate([
            'title'       => 'required|max:256',
            'category_id' => 'required|exists:car_categories,id',
            'fuel_type'   => 'nullable|max:50',
            'seats'        => 'required|string|max:50',
            'primary_image'=> 'required|string|exists:media,path',
            'primary_image_alt' => 'required|max:256',
        ]);

        $path = $r->input('primary_image');
        $car = Car::create([
            'title'        => $validated['title'],
            'slug'        => Str::slug($validated['title']),
            'category_id'       => $validated['category_id'],
            'fuel_type'   => $validated['fuel_type'],
            'primary_image'=> $path,
            'primary_image_alt' => $validated['primary_image_alt'],
            'seats' => $validated['seats'],
        ]);

        if ($r->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Car created successfully',
                'car' => $car
            ]);
        }
        // Fallback for non-AJAX
        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','car_list')
        ->with('success', 'Car created successfully');
    }


    public function edit(Car $car){
        if (request()->ajax()) {
            return response()->json($car);
        }
    }

    public function show(Car $car)
    {
        $data = $car->toArray();
        return response()->json($data);
    }

    public function update(Request $r, Car $car){

        if ($r->exists('status')) {
            $car->is_active = $r->status;
            $car->save();
        } elseif ($r->exists('page_setting')) {
            $r->validate([
                'description'         => 'nullable|string',
                'banner_image'        => 'nullable|string|exists:media,path',
                'banner_image_alt'    => 'nullable|string|max:255',
                'vehicle_type'        => 'nullable|string|max:100',
                'transmission'        => 'nullable|string|max:100',
                'luggage_capacity'    => 'nullable|string|max:100',
                'mileage'             => 'nullable|string|max:100',
                'specs_title'         => 'nullable|string|max:255',
                'specs_description'   => 'nullable|string',
                'gallery_title'       => 'nullable|string|max:255',
                'gallery_description' => 'nullable|string',
            ]);

            $bannerImageChanged = $r->has('banner_image') && $r->input('banner_image') !== $car->banner_image;

            // Only touch the fields this particular form actually submitted —
            // several smaller forms share this same endpoint, so a blind
            // overwrite would null out fields owned by the other forms.
            $data = $r->only([
                'description', 'banner_image_alt', 'vehicle_type', 'transmission',
                'luggage_capacity', 'mileage', 'specs_title', 'specs_description',
                'gallery_title', 'gallery_description',
            ]);

            if ($bannerImageChanged) {
                $data['banner_image'] = $r->input('banner_image');
            }

            $car->update($data);

            if ($r->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Car page settings updated successfully',
                ]);
            }

            return redirect()->route('admin.page-settings.car')
                ->with('active_tab', 'car_list')
                ->with('success', 'Car page settings updated successfully');
        } else {
            $r->validate([
                'title'       => 'required|max:256',
                'category_id' => 'required|exists:car_categories,id',
                'fuel_type'   => 'nullable|max:50',
                'seats'        => 'required|string|max:50',
                'primary_image'=> 'nullable|string|exists:media,path',
                'primary_image_alt' => 'required|max:256',
            ]);

            $primaryImageChanged = $r->has('primary_image') && $r->input('primary_image') !== $car->primary_image;

            $path = $car->primary_image ?? '';
            if ($primaryImageChanged) {
                $path = $r->input('primary_image');
            }


            $car->update([
                'title'       => $r->title,
                'category_id'   => $r->category_id,
                'primary_image'=> $path,
                'primary_image_alt' => $r->primary_image_alt,
                'fuel_type' => $r->fuel_type,
                'seats' => $r->seats,
            ]);
        }

        // If AJAX request, return JSON response
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Car updated successfully',
                'data'    => $car
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
        ->route('admin.page-settings.car')
        ->with('active_tab','car_list')
        ->with('success', 'Car updated successfully');
    }


    public function destroy(Car $car){
        // Note: primary_image/banner_image are intentionally NOT deleted from storage
        // here — they live in the shared Media Library and may still be used elsewhere.
        $car->galleryImages()->delete();
        $car->highlightTags()->delete();
        $car->amenities()->delete();
        $car->delete();
        return response()->json(['success'=>true]);
    }

    public function slugDuplicateCheck(Request $r){ 
        return response()->json([
            'exists' => Car::where('slug',  Str::slug($r->title))
            ->where('id', '!=', $r->id)
            ->exists()
        ]);

    }
}
