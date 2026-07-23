<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourService;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\ImageLicenseManager;


class TourServiceController extends Controller{

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

    public function store(Request $r){
        $r->validate(array_merge([
            'title'        => 'required|string|max:255',
            'link'         => 'nullable|string|max:500',
            'banner_image' => 'nullable|image|mimes:webp',
        ], ImageLicenseManager::rules('banner_license')));

        if ($r->hasFile('banner_image')) {
            $errors = ImageLicenseManager::validationErrors(
                $r, 'banner_license', 'the Tour Service Banner Image', null
            );
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }
        }

        $obj = [
            'link' => $r->link,
            'title' => $r->title,
            'banner_image_alt' => $r->banner_image_alt
        ];
        session()->flash('active_tab', 'tservice');

        if ($r->hasFile('banner_image')) {
            $file = $r->file('banner_image');

            if ($file->isValid()) {
                // keep original filename
                $filename = unique_filename($file);

                // store in S3 under "tour_service/"
                try {
                    $path = $file->storeAs('tour_service', $filename, config('filesystems.upload_disk'));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('[TourService Store] storeAs failed', ['error' => $e->getMessage()]);
                    return response()->json(['success' => false, 'message' => 'Banner image upload failed. Please try again.'], 500);
                }

                $obj['banner_image'] = $path;
            }
        }


        $service = TourService::create($obj);

        if ($r->hasFile('banner_image')) {
            ImageLicenseManager::save($service, $r, 'banner_license', 'banner');
        }

        return response()->json(['success'=>true]);
    }

    public function edit(TourService $tour_service){
        $tour_service = TourService::where('id',$tour_service->id)->first();
        $data = $tour_service->toArray();
        $data['banner_license'] = $this->licenseForJson($tour_service->imageLicense('banner'));
        return response()->json($data);
    }

    public function update(Request $r, TourService $tour_service){

        if (!$r->exists('status')) {
            $r->validate(array_merge([
                'title'        => 'required|string|max:255',
                'link'         => 'nullable|string|max:500',
                'banner_image' => 'nullable|image|mimes:webp',
            ], ImageLicenseManager::rules('banner_license')));

            if ($r->hasFile('banner_image')) {
                $errors = ImageLicenseManager::validationErrors(
                    $r, 'banner_license', 'the Tour Service Banner Image', $tour_service->imageLicense('banner')
                );
                if ($errors) {
                    throw ValidationException::withMessages($errors);
                }
            }

            $path = $tour_service->banner_image ?? '';
            if ($r->hasFile('banner_image')) {
                $file = $r->file('banner_image');

                if ($file->isValid()) {
                    $oldBannerImage = $tour_service->banner_image;

                    // keep original filename
                    $filename = unique_filename($file);

                    // store in S3 under "tour_service/"
                    try {
                        $path = $file->storeAs('tour_service', $filename, config('filesystems.upload_disk'));
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('[TourService Update] storeAs failed', ['error' => $e->getMessage()]);
                        if ($r->ajax()) {
                            return response()->json(['success' => false, 'message' => 'Banner image upload failed. Please try again.'], 500);
                        }
                        return back()->withInput()->with('error', 'Banner image upload failed. Please try again.');
                    }

                    // delete old file only after the new one is safely stored
                    if ($oldBannerImage && \Storage::disk(config('filesystems.upload_disk'))->exists($oldBannerImage)) {
                        \Storage::disk(config('filesystems.upload_disk'))->delete($oldBannerImage);
                    }

                    ImageLicenseManager::save($tour_service, $r, 'banner_license', 'banner');
                }
            }

            $obj = [
                'title'       => $r->title,
                'link'   => $r->link,
                'banner_image'=> $path,
                'banner_image_alt' => $r->banner_image_alt
            ];


            $tour_service->update($obj);
            session()->flash('active_tab', 'tservice');
        }  else {
            // Normal banner just update status
            $tour_service->is_active = $r->status ? 1 : 0;
            $tour_service->save();
        }
        
        // If AJAX request, return JSON response
        if ($r->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Service updated successfully',
                'data'    => $tour_service
            ]);
        }

        // Fallback for non-AJAX
        return redirect()
        ->route('admin.settings.index')
        ->with('success', 'Service updated successfully');
    }

    public function destroy(TourService $tour_service){
        $tour_service->delete();

        // The DB delete above already succeeded — a storage hiccup here shouldn't
        // turn a successful delete into a 500 for the admin, so failures are logged
        // rather than thrown.
        rescue(function () use ($tour_service) {
            if ($tour_service->banner_image && \Storage::disk(config('filesystems.upload_disk'))->exists($tour_service->banner_image)) {
                \Storage::disk(config('filesystems.upload_disk'))->delete($tour_service->banner_image);
            }
        }, function (\Throwable $e) use ($tour_service) {
            \Illuminate\Support\Facades\Log::error('[TourService Delete] Failed to delete banner image from disk', [
                'tour_service_id' => $tour_service->id,
                'banner_image'    => $tour_service->banner_image,
                'error'           => $e->getMessage(),
            ]);
        }, report: false);

        return response()->json(['success'=>true]);
    }
}
