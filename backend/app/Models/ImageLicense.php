<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Generic "Image License Details" record — Source of Image, Download Date,
 * Account ID, License Key, and an optional License Document (PDF/JPG/PNG).
 *
 * Attaches to ANY model via a polymorphic relation, so any image-upload form
 * anywhere in the project can carry license/proof-of-rights metadata without
 * needing its own dedicated DB columns. See App\Services\ImageLicenseManager
 * for the reusable validate + save logic, and the <x-image-license-fields>
 * Blade component for the reusable form UI.
 */
class ImageLicense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'licensable_type',
        'licensable_id',
        'field_key',
        'source_of_image',
        'download_date',
        'account_id',
        'license_key',
        'license_key_file',
    ];

    protected $casts = [
        'download_date' => 'date:Y-m-d',
    ];

    public function licensable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Whether at least one of Account ID / License Key / License Document has been provided. */
    public function hasProof(): bool
    {
        return (bool) ($this->account_id || $this->license_key || $this->license_key_file);
    }
}
