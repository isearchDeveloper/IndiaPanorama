<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Country;
use App\Models\CountryDetails;
use App\Models\CountryFaq;
use App\Models\CountryMetaData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageLicenseManager;
use Illuminate\Validation\ValidationException;

/**
 * CountryController — India-only system.
 *
 * Creating, deleting, or listing countries via admin is disabled.
 * Only India (id=1) exists; its page content / FAQs / meta are editable.
 */
class CountryController extends Controller
{
    private const INDIA_ID = 1;

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

    /** Used by AJAX in locations view to fetch FAQs for India. */
    public function index(Request $request)
    {
        if ($request->exists('faqs')) {
            $country = Country::with('faqs')->findOrFail(self::INDIA_ID);
            return response()->json(['status' => 'success', 'country' => $country]);
        }

        // Direct GET visits redirect to Location Setting
        return redirect()->route('admin.location-setting.index');
    }

    public function show(Country $country)
    {
        abort_unless($country->id === self::INDIA_ID, 403, 'Only India is managed in this system.');
        $country->load('details');
        $data = $country->toArray();
        $data['banner_license'] = $this->licenseForJson($country->imageLicense('banner'));
        return response()->json($data);
    }

    public function update(Request $request, Country $country)
    {
        abort_unless($country->id === self::INDIA_ID, 403, 'Only India is managed in this system.');

        if ($request->exists('meta_setting')) {
            $request->validate([
                'meta_title'       => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords'    => 'nullable|string',
                'h1_heading'       => 'nullable|string',
                'meta_details'     => 'nullable|string',
            ]);

            CountryMetaData::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'meta_title'       => $request->meta_title,
                    'meta_description' => $request->meta_description,
                    'meta_keywords'    => $request->meta_keywords,
                    'h1_heading'       => $request->h1_heading,
                    'meta_details'     => $request->meta_details,
                ]
            );

            ActivityLog::log('updated', 'Country', 'Updated SEO meta for India');

            return redirect()->route('admin.location-setting.index')->with('success', 'India meta updated.');
        }

        // Page Settings
        $request->validate(array_merge([
            'author_name'  => 'required|string|max:255',
            'banner_image' => 'nullable|image|mimes:webp',
        ], ImageLicenseManager::rules('banner_license')));

        if ($request->hasFile('banner_image')) {
            $errors = ImageLicenseManager::validationErrors(
                $request, 'banner_license', 'the Country Banner Image', $country->imageLicense('banner')
            );
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }
        }

        $path = $country->details->banner_image ?? '';

        if ($request->hasFile('banner_image') && $request->file('banner_image')->isValid()) {
            $img = $request->file('banner_image');
            $oldBannerImage = $country->details?->banner_image;

            try {
                $path = $img->storeAs('country', unique_filename($img), config('filesystems.upload_disk'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[Country Update] storeAs failed', ['error' => $e->getMessage()]);
                return back()->withInput()->with('error', 'Banner image upload failed. Please try again.');
            }

            if ($oldBannerImage) {
                rescue(fn() => Storage::disk(config('filesystems.upload_disk'))->delete($oldBannerImage));
            }

            ImageLicenseManager::save($country, $request, 'banner_license', 'banner');
        }

        CountryDetails::updateOrCreate(
            ['country_id' => $country->id],
            [
                'title'            => $request->title,
                'sub_title'        => $request->sub_title,
                'banner_image'     => $path,
                'banner_image_alt' => $request->banner_image_alt,
                'about'            => $request->about,
                'author_name'      => $request->author_name,
            ]
        );

        ActivityLog::log('updated', 'Country', 'Updated page settings for India');

        return redirect()->route('admin.location-setting.index')->with('success', 'India page settings updated.');
    }

    public function updateFaq(Request $request, Country $country)
    {
        abort_unless($country->id === self::INDIA_ID, 403, 'Only India is managed in this system.');

        $country->faqs()->delete();

        if ($request->has('faqs')) {
            foreach ($request->faqs as $faq) {
                CountryFaq::create([
                    'country_id' => $country->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'] ?? null,
                ]);
            }
        }

        $country->update(['faq_title' => $request->faq_title]);

        ActivityLog::log('updated', 'Country', 'Updated FAQs for India');

        return redirect()->route('admin.location-setting.index')->with('success', 'India FAQs updated.');
    }

    // ── Blocked operations ────────────────────────────────────────────────────

    public function store()
    {
        abort(403, 'Creating countries is disabled. Only India is used in this system.');
    }

    public function create()
    {
        abort(403, 'Creating countries is disabled. Only India is used in this system.');
    }

    public function destroy()
    {
        abort(403, 'Deleting countries is disabled. Only India is used in this system.');
    }
}
