<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarRentalRoadTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarRentalRoadTripController extends Controller
{
    public function store(Request $r)
    {
        $r->validate(array_merge([
            'state_id'        => 'required|exists:states,id',
            'image'           => 'required|image|mimes:webp',
            'image_alt'       => 'nullable|string|max:255',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'route_text'      => 'nullable|string|max:255',
            'duration_days'   => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
        ], \App\Services\ImageLicenseManager::rules('image_license')));

        if ($r->hasFile('image')) {
            $errors = \App\Services\ImageLicenseManager::validationErrors(
                $r, 'image_license', 'the Road Trip Image', null
            );
            if ($errors) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
        }

        try {
            $path = $r->file('image')->storeAs('car-rental/road-trips', unique_filename($r->file('image')), config('filesystems.upload_disk'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[CarRentalRoadTrip Upload] storeAs failed', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Image upload failed. Please try again.');
        }

        $road_trip = CarRentalRoadTrip::create([
            'state_id'        => $r->state_id,
            'image'           => $path,
            'image_alt'       => $r->image_alt,
            'rating'          => $r->rating ?? 0,
            'route_text'      => $r->route_text,
            'duration_days'   => $r->duration_days,
            'duration_nights' => $r->duration_nights,
            'sort_order'      => CarRentalRoadTrip::count(),
            'is_active'       => true,
        ]);

        \App\Services\ImageLicenseManager::save($road_trip, $r, 'image_license', 'image');

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'road_trip_list')
            ->with('success', 'Road trip destination added.');
    }

    public function show(CarRentalRoadTrip $road_trip)
    {
        $data = $road_trip->toArray();
        $data['image_license'] = $this->licenseForJson($road_trip->imageLicense('image'));
        return response()->json($data);
    }

    private function licenseForJson(?\App\Models\ImageLicense $license): ?array
    {
        if (!$license) return null;

        return [
            'source_of_image'      => $license->source_of_image,
            'download_date'        => $license->download_date?->format('Y-m-d'),
            'account_id'           => $license->account_id,
            'license_key'          => $license->license_key,
            'license_key_file_url' => $license->license_key_file ? storage_link($license->license_key_file) : null,
        ];
    }

    public function update(Request $r, CarRentalRoadTrip $road_trip)
    {
        if ($r->has('status')) {
            $road_trip->is_active = $r->status;
            $road_trip->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status'  => $road_trip->is_active,
            ]);
        }

        $r->validate(array_merge([
            'state_id'        => 'required|exists:states,id',
            'image'           => 'nullable|image|mimes:webp',
            'image_alt'       => 'nullable|string|max:255',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'route_text'      => 'nullable|string|max:255',
            'duration_days'   => 'nullable|integer|min:0',
            'duration_nights' => 'nullable|integer|min:0',
        ], \App\Services\ImageLicenseManager::rules('image_license')));

        if ($r->hasFile('image')) {
            $errors = \App\Services\ImageLicenseManager::validationErrors(
                $r, 'image_license', 'the Road Trip Image', $road_trip->imageLicense('image')
            );
            if ($errors) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
        }

        $path = $road_trip->image;
        if ($r->hasFile('image')) {
            $oldPath = $path;
            try {
                $path = $r->file('image')->storeAs('car-rental/road-trips', unique_filename($r->file('image')), config('filesystems.upload_disk'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[CarRentalRoadTrip Update] storeAs failed', ['error' => $e->getMessage()]);
                return back()->withInput()->with('error', 'Image upload failed. Please try again.');
            }
            if ($oldPath && Storage::disk(config('filesystems.upload_disk'))->exists($oldPath)) {
                Storage::disk(config('filesystems.upload_disk'))->delete($oldPath);
            }

            \App\Services\ImageLicenseManager::save($road_trip, $r, 'image_license', 'image');
        }

        $road_trip->update([
            'state_id'        => $r->state_id,
            'image'           => $path,
            'image_alt'       => $r->image_alt,
            'rating'          => $r->rating ?? 0,
            'route_text'      => $r->route_text,
            'duration_days'   => $r->duration_days,
            'duration_nights' => $r->duration_nights,
        ]);

        return redirect()->route('admin.page-settings.car')
            ->with('active_tab', 'road_trip_list')
            ->with('success', 'Road trip destination updated.');
    }

    public function destroy(CarRentalRoadTrip $road_trip)
    {
        if ($road_trip->image && Storage::disk(config('filesystems.upload_disk'))->exists($road_trip->image)) {
            Storage::disk(config('filesystems.upload_disk'))->delete($road_trip->image);
        }
        $road_trip->delete();

        return response()->json(['success' => true, 'message' => 'Road trip destination deleted.']);
    }
}
