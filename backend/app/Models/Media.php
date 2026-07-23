<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A single entry in the shared Media Library — one row per physical file on
 * the upload disk (S3/CDN). Entity tables (banners, packages, manage_cities,
 * ...) keep storing the bare relative `path` string directly in their own
 * columns exactly as before; this table exists so the same path can be
 * discovered and reused across many entities instead of re-uploading the
 * same file over and over. See App\Http\Controllers\Admin\MediaController
 * and the <x-media-picker> Blade component.
 */
class Media extends Model
{
    use SoftDeletes;
    use \App\Models\Concerns\HasImageLicenses;

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'folder',
        'path',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'uploaded_by',
        'source',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
