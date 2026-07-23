<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ImageLicenseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    /** Standalone Media Library page. */
    public function index()
    {
        $folders = Media::query()
            ->whereNotNull('folder')
            ->distinct()
            ->orderBy('folder')
            ->pluck('folder');

        return view('admin.media.index', compact('folders'));
    }

    /** AJAX: paginated/filterable grid data — used by the standalone page and the <x-media-picker> modal. */
    public function list(Request $r)
    {
        $query = Media::query()->with('imageLicenses')->latest('id');

        if ($r->filled('folder')) {
            $query->where('folder', $r->input('folder'));
        }

        if ($r->filled('search')) {
            $query->where(function ($q) use ($r) {
                $search = $r->input('search');
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(24)->withQueryString();

        return response()->json([
            'data' => collect($media->items())->map(fn (Media $m) => $this->toJson($m)),
            'current_page' => $media->currentPage(),
            'last_page'    => $media->lastPage(),
            'total'        => $media->total(),
        ]);
    }

    /**
     * AJAX upload — one or more files, straight into the Media Library.
     * License details are required here (once per upload batch) rather than
     * per-usage: this is the only place a brand-new image enters the library,
     * so it's the only place we can guarantee proof-of-rights is ever asked
     * for. Reusing an already-uploaded image elsewhere never re-asks — its
     * license already lives on this Media row.
     */
    public function store(Request $r)
    {
        $r->validate(array_merge([
            'files'   => 'required|array|min:1',
            'files.*' => 'image|mimes:webp|max:150',
            'folder'  => 'nullable|string|max:100',
        ], ImageLicenseManager::rules('license')));

        $errors = ImageLicenseManager::validationErrors($r, 'license', 'this image');
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $disk   = config('filesystems.upload_disk', 'public');
        $folder = $r->input('folder') ?: 'media';

        $created = [];

        foreach ($r->file('files') as $file) {
            $filename = unique_filename($file);

            try {
                $path = $file->storeAs($folder, $filename, $disk);
            } catch (\Throwable $e) {
                Log::error('[Media Upload] storeAs failed', [
                    'folder'   => $folder,
                    'filename' => $filename,
                    'error'    => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed while saving one of the files. Please try again.',
                    'data'    => $created,
                ], 500);
            }

            [$width, $height] = rescue(function () use ($file) {
                $dimensions = @getimagesize($file->getRealPath());
                return $dimensions ? [$dimensions[0], $dimensions[1]] : [null, null];
            }, [null, null], report: false);

            $media = Media::create([
                'disk'          => $disk,
                'folder'        => $folder,
                'path'          => $path,
                'filename'      => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getClientMimeType(),
                'size'          => $file->getSize(),
                'width'         => $width,
                'height'        => $height,
                'uploaded_by'   => auth()->id(),
                'source'        => 'upload',
            ]);

            ImageLicenseManager::save($media, $r, 'license', 'primary');

            $created[] = $this->toJson($media);
        }

        return response()->json(['success' => true, 'data' => $created]);
    }

    /** AJAX: full details for one item (license, uploader, dimensions, size, dates) — powers the "click a thumbnail" details modal. */
    public function show(Media $media)
    {
        $media->load(['uploader', 'imageLicenses']);
        $license = $media->imageLicenses->firstWhere('field_key', 'primary');

        return response()->json([
            'id'            => $media->id,
            'path'          => $media->path,
            'url'           => storage_link($media->path),
            'filename'      => $media->filename,
            'original_name' => $media->original_name,
            'folder'        => $media->folder,
            'mime_type'     => $media->mime_type,
            'size'          => $media->size,
            'size_human'    => $this->humanFileSize($media->size),
            'width'         => $media->width,
            'height'        => $media->height,
            'source'        => $media->source,
            'uploaded_by'       => $media->uploader?->name,
            'uploaded_by_email' => $media->uploader?->email,
            'created_at'    => $media->created_at?->format('d M Y, h:i A'),
            'license'       => $license ? [
                'source_of_image'      => $license->source_of_image,
                'download_date'        => $license->download_date?->format('Y-m-d'),
                'account_id'           => $license->account_id,
                'license_key'          => $license->license_key,
                'license_key_file_url' => $license->license_key_file ? storage_link($license->license_key_file) : null,
            ] : null,
        ]);
    }

    private function humanFileSize(?int $bytes): ?string
    {
        if (!$bytes) return null;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }

    /** Delete a library item (soft delete) and its underlying file. Phase 1: no usage-tracking. */
    public function destroy(Media $media)
    {
        rescue(function () use ($media) {
            Storage::disk($media->disk)->delete($media->path);
        }, function (\Throwable $e) use ($media) {
            Log::error('[Media Delete] Failed to delete file from disk; DB row is being removed anyway, file may be orphaned on storage.', [
                'media_id' => $media->id,
                'path'     => $media->path,
                'disk'     => $media->disk,
                'error'    => $e->getMessage(),
            ]);
        }, report: false);
        $media->delete();

        return response()->json(['success' => true]);
    }

    /** Re-run the disk backfill from the admin UI ("Sync Library" button). */
    public function sync()
    {
        Artisan::call('media:sync');

        return response()->json(['success' => true, 'output' => Artisan::output()]);
    }

    private function toJson(Media $m): array
    {
        return [
            'id'          => $m->id,
            'path'        => $m->path,
            'folder'      => $m->folder,
            'filename'    => $m->filename,
            'url'         => storage_link($m->path),
            'width'       => $m->width,
            'height'      => $m->height,
            'has_license' => (bool) ($m->relationLoaded('imageLicenses')
                ? $m->imageLicenses->firstWhere('field_key', 'primary')
                : $m->imageLicense('primary')),
        ];
    }
}
