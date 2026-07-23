<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\HolidaySetting;
use App\Models\HolidaySettingDetail;
use App\Models\HolidaySettingMeta;
use App\Services\ImageLicenseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HolidaySettingController extends Controller
{
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

    public function index(Request $request)
    {
        if ($request->ajax() && $request->exists('faqs') && $request->id) {
            $holiday = HolidaySetting::with('faqs')->findOrFail($request->id);
            $data = $holiday->toArray();
            $data['faq_license'] = $this->licenseForJson($holiday->imageLicense('faq'));
            return response()->json(['status' => true, 'holiday' => $data]);
        }

        $holidays = HolidaySetting::orderBy('name')->paginate(20);
        return view('admin.holiday-setting.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:200']);

        $slug = Str::slug($request->name . '-holiday');
        $base = $slug;
        $i    = 1;
        while (HolidaySetting::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $holiday = HolidaySetting::create([
            'name'      => $request->name,
            'slug'      => $slug,
            'is_active' => true,
        ]);

        ActivityLog::log('created', 'HolidaySetting', "Created holiday setting: {$holiday->name}");

        return response()->json(['status' => true, 'message' => 'Holiday setting created.', 'holiday' => $holiday]);
    }

    public function show(HolidaySetting $holidaySetting)
    {
        $holidaySetting->load(['details', 'meta', 'faqs']);
        $data = $holidaySetting->toArray();
        $data['faq_license'] = $this->licenseForJson($holidaySetting->imageLicense('faq'));
        return response()->json($data);
    }

    public function update(Request $request, HolidaySetting $holidaySetting)
    {
        // Edit modal: Banner Title, Description, Image, Alt, Short Description
        if ($request->has('edit_modal')) {
            $request->validate([
                'banner_image' => 'nullable|string|exists:media,path',
            ]);

            $imageChanged = $request->has('banner_image') && $request->input('banner_image') !== $holidaySetting->details?->banner_image;

            $data = [
                'banner_title'       => $request->banner_title,
                'banner_description' => $request->banner_description,
                'banner_image_alt'   => $request->banner_image_alt,
                'short_description'  => $request->short_description,
            ];

            if ($imageChanged) {
                $data['banner_image'] = $request->input('banner_image');
            }

            HolidaySettingDetail::updateOrCreate(
                ['holiday_setting_id' => $holidaySetting->id],
                $data
            );

            ActivityLog::log('updated', 'HolidaySetting', "Updated content for: {$holidaySetting->name}");
            return response()->json(['status' => true, 'message' => 'Content saved.']);
        }

        // Settings modal: Long Description + Popular Packages
        if ($request->has('settings_modal')) {
            $data = [
                'long_description'             => $request->long_description,
                'popular_packages_heading'     => $request->popular_packages_heading,
                'popular_packages_description' => $request->popular_packages_description,
            ];

            HolidaySettingDetail::updateOrCreate(
                ['holiday_setting_id' => $holidaySetting->id],
                $data
            );

            ActivityLog::log('updated', 'HolidaySetting', "Updated settings for: {$holidaySetting->name}");
            return response()->json(['status' => true, 'message' => 'Settings saved.']);
        }

        // Meta modal
        if ($request->has('meta_setting')) {
            HolidaySettingMeta::updateOrCreate(
                ['holiday_setting_id' => $holidaySetting->id],
                [
                    'meta_title'       => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords'    => $request->meta_keywords,
                    'h1_heading'       => $request->h1_heading,
                    'meta_details'     => $request->meta_details,
                ]
            );

            return response()->json(['status' => true, 'message' => 'Meta settings saved.']);
        }

        return response()->json(['status' => false, 'message' => 'Unknown update type.'], 422);
    }

    public function updateFaq(Request $request, HolidaySetting $holidaySetting)
    {
        $request->validate(array_merge(
            ['faq_image' => 'nullable|string|exists:media,path'],
            ImageLicenseManager::rules('faq_license')
        ));

        $faqImageChanged = $request->has('faq_image') && $request->input('faq_image') !== $holidaySetting->faq_image;

        if ($faqImageChanged) {
            $errors = ImageLicenseManager::validationErrors(
                $request, 'faq_license', 'the FAQ Image', $holidaySetting->imageLicense('faq')
            );
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }
        }

        $holidaySetting->faqs()->delete();

        if ($request->faqs && is_array($request->faqs)) {
            foreach ($request->faqs as $faq) {
                if (!empty($faq['question'])) {
                    $holidaySetting->faqs()->create([
                        'question' => $faq['question'],
                        'answer'   => $faq['answer'] ?? null,
                    ]);
                }
            }
        }

        $updateData = [
            'faq_title'     => $request->faq_title,
            'faq_image_alt' => $request->faq_image_alt,
        ];

        if ($faqImageChanged) {
            $updateData['faq_image'] = $request->input('faq_image');
        }

        $holidaySetting->update($updateData);

        if ($faqImageChanged) {
            ImageLicenseManager::save($holidaySetting, $request, 'faq_license', 'faq');
        }

        return response()->json(['status' => true, 'message' => 'FAQs updated.']);
    }

    public function toggleStatus(HolidaySetting $holidaySetting)
    {
        $holidaySetting->update(['is_active' => !$holidaySetting->is_active]);
        ActivityLog::log('status-changed', 'HolidaySetting', "Toggled status for: {$holidaySetting->name}");
        return response()->json([
            'status'    => true,
            'is_active' => $holidaySetting->is_active,
            'message'   => 'Status updated.',
        ]);
    }

    public function destroy(HolidaySetting $holidaySetting)
    {
        $name    = $holidaySetting->name;
        $details = $holidaySetting->details;
        // Note: faq_image is intentionally NOT deleted from storage here — it lives
        // in the shared Media Library and may still be used elsewhere.
        $holidaySetting->faqs()->delete();
        $holidaySetting->meta()->delete();
        $holidaySetting->details()->delete();
        $holidaySetting->delete();
        ActivityLog::log('deleted', 'HolidaySetting', "Deleted holiday setting: {$name}");
        return response()->json(['status' => true, 'message' => 'Holiday setting deleted.']);
    }
}
