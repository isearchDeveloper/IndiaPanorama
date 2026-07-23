<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Backfills the `media` table from whatever image files already exist on the
 * upload disk, so the Media Library shows every image ever uploaded — not
 * just ones created through the library itself. Safe to re-run any time
 * (existing paths are skipped via firstOrCreate); also exposed as a
 * "Sync Library" button in the admin UI (MediaController::sync()).
 *
 *   php artisan media:sync
 */
class SyncMediaLibrary extends Command
{
    protected $signature   = 'media:sync';
    protected $description = 'Backfill the media table from files already present on the upload disk';

    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    public function handle(): int
    {
        $disk  = config('filesystems.upload_disk', 'public');
        $files = Storage::disk($disk)->allFiles();

        $this->info(count($files) . " file(s) found on the '{$disk}' disk.");

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $created = 0;
        $skipped = 0;

        foreach ($files as $path) {
            $bar->advance();

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($ext, self::IMAGE_EXTENSIONS, true)) {
                continue;
            }

            $existing = Media::withTrashed()->where('path', $path)->first();
            if ($existing) {
                $skipped++;
                continue;
            }

            Media::create([
                'disk'     => $disk,
                'folder'   => Str::contains($path, '/') ? Str::before($path, '/') : null,
                'path'     => $path,
                'filename' => basename($path),
                'mime_type' => static::guessMimeFromExtension($ext),
                'size'     => rescue(fn () => Storage::disk($disk)->size($path), null, report: false),
                'source'   => 'synced',
            ]);
            $created++;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Added: {$created}, already tracked: {$skipped}.");

        return self::SUCCESS;
    }

    private static function guessMimeFromExtension(string $ext): ?string
    {
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'webp'        => 'image/webp',
            'gif'         => 'image/gif',
            'svg'         => 'image/svg+xml',
            default       => null,
        };
    }
}
