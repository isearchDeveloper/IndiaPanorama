<?php

namespace App\Services;

use App\Models\ImageLicense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Reusable "Image License Details" logic (Source of Image, Download Date,
 * Account ID, License Key, License Document) shared by every image-upload
 * form in the project.
 *
 * Pair with the <x-image-license-fields> Blade component for the UI:
 *
 *   <x-image-license-fields name="license" :license="$model->imageLicense('primary')" />
 *
 * Then in the controller:
 *
 *   $errors = ImageLicenseManager::validationErrors($request, 'license', 'the Banner Image', $model->imageLicense('primary'));
 *   if ($errors) return back()->withInput()->withErrors($errors);
 *   ...
 *   ImageLicenseManager::save($model, $request, 'license', 'primary');
 *
 * The model must `use \App\Models\Concerns\HasImageLicenses;`.
 */
class ImageLicenseManager
{
    /** Laravel validation rules for the file-upload sub-field. Merge into the caller's own $request->validate(). */
    public static function rules(string $prefix): array
    {
        return [
            "{$prefix}.source_of_image" => 'nullable|string|max:255',
            "{$prefix}.download_date"   => 'nullable|date',
            "{$prefix}.account_id"      => 'nullable|string|max:255',
            "{$prefix}.license_key"     => 'nullable|string|max:255',
            "{$prefix}.license_file"    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Source of Image + Download Date are always required; at least one of
     * Account ID / License Key / License Document must be provided (an
     * already-uploaded document on $existing counts too). Returns a
     * [field => message] array — empty if everything's fine.
     */
    public static function validationErrors(Request $r, string $prefix, string $label, ?ImageLicense $existing = null): array
    {
        $errors = [];

        $sourceOfImage = $r->input("{$prefix}.source_of_image");
        $downloadDate  = $r->input("{$prefix}.download_date");
        $accountId     = $r->input("{$prefix}.account_id");
        $licenseKey    = $r->input("{$prefix}.license_key");
        $hasNewFile    = (bool) $r->file("{$prefix}.license_file");
        $hasOldFile    = (bool) $existing?->license_key_file;

        if (!$sourceOfImage) {
            $errors["{$prefix}.source_of_image"] = "Source of Image is required for {$label}.";
        }
        if (!$downloadDate) {
            $errors["{$prefix}.download_date"] = "Download Date is required for {$label}.";
        }
        if (!$accountId && !$licenseKey && !$hasNewFile && !$hasOldFile) {
            $errors["{$prefix}.license_file"] = "Provide an Account ID, License Key, or upload the License Document for {$label}.";
        }

        return $errors;
    }

    /**
     * Same field set as rules(), but wildcard-scoped for a repeatable/indexed
     * array of rows (e.g. gallery images, icon lists) whose count isn't known
     * ahead of time — e.g. rulesIndexed('highlight_license') covers every
     * highlight_license[$i][...] key in one shot.
     */
    public static function rulesIndexed(string $prefix): array
    {
        return [
            "{$prefix}.*.source_of_image" => 'nullable|string|max:255',
            "{$prefix}.*.download_date"   => 'nullable|date',
            "{$prefix}.*.account_id"      => 'nullable|string|max:255',
            "{$prefix}.*.license_key"     => 'nullable|string|max:255',
            "{$prefix}.*.license_file"    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Wipe every ImageLicense row under a repeatable field-key prefix (e.g. all
     * "highlight-0", "highlight-1", ... slots), deleting their license documents
     * too. Pair with a loop of save() calls to rebuild fresh rows — mirrors the
     * delete-then-recreate pattern already used by every repeatable-row
     * controller (gallery images, icon lists, amenities, ...) for the rows
     * themselves, so licenses never end up orphaned against a deleted row.
     */
    public static function clearIndexed(Model $licensable, string $fieldKeyPrefix): void
    {
        $licensable->imageLicenses()
            ->where('field_key', 'like', "{$fieldKeyPrefix}-%")
            ->get()
            ->each(function (ImageLicense $lic) {
                if ($lic->license_key_file) {
                    rescue(fn () => Storage::disk(config('filesystems.upload_disk'))->delete($lic->license_key_file));
                }
                $lic->delete();
            });
    }

    /**
     * Create/update the ImageLicense row for $licensable's $fieldKey slot,
     * uploading a replacement document if one was submitted.
     */
    public static function save(Model $licensable, Request $r, string $prefix, string $fieldKey = 'primary', string $folder = 'license-documents'): ImageLicense
    {
        $existing = $licensable->imageLicense($fieldKey);

        $filePath = $existing?->license_key_file;
        $newFile  = $r->file("{$prefix}.license_file");
        if ($newFile && $newFile->isValid()) {
            if ($filePath) {
                rescue(fn () => Storage::disk(config('filesystems.upload_disk'))->delete($filePath));
            }
            $filePath = $newFile->storeAs($folder, unique_filename($newFile), config('filesystems.upload_disk'));
        }

        return $licensable->imageLicenses()->updateOrCreate(
            ['field_key' => $fieldKey],
            [
                'source_of_image'  => $r->input("{$prefix}.source_of_image"),
                'download_date'    => $r->input("{$prefix}.download_date") ?: null,
                'account_id'       => $r->input("{$prefix}.account_id"),
                'license_key'      => $r->input("{$prefix}.license_key"),
                'license_key_file' => $filePath,
            ]
        );
    }
}
