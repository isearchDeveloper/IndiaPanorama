<?php

namespace App\Models\Concerns;

use App\Models\ImageLicense;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Add to any Eloquent model that has one or more license-tracked images.
 * Usage:
 *   class Package extends Model {
 *       use \App\Models\Concerns\HasImageLicenses;
 *   }
 *   $package->imageLicense('primary');           // single/main image
 *   $package->imageLicense('gallery-'.$image->id); // one per repeatable image
 */
trait HasImageLicenses
{
    public function imageLicenses(): MorphMany
    {
        return $this->morphMany(ImageLicense::class, 'licensable');
    }

    public function imageLicense(string $fieldKey = 'primary'): ?ImageLicense
    {
        return $this->imageLicenses()->where('field_key', $fieldKey)->first();
    }
}
