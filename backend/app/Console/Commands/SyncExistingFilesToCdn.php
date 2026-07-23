<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * One-time push of every already-uploaded file from the local 'public' disk
 * to the remote 'cdn' SFTP disk, preserving relative paths (packages/, trains/, etc).
 * Run once after CDN_SFTP_* credentials are configured, before flipping UPLOAD_DISK=cdn:
 *
 *   php artisan cdn:sync-existing --dry-run
 *   php artisan cdn:sync-existing
 */
class SyncExistingFilesToCdn extends Command
{
    protected $signature   = 'cdn:sync-existing {--dry-run : List files without uploading}';
    protected $description = 'Push every existing file from the local public disk to the remote CDN disk';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $files  = Storage::disk('public')->allFiles();

        $this->info(count($files) . ' file(s) found on the local public disk.');

        if ($dryRun) {
            foreach ($files as $path) {
                $this->line($path);
            }
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $copied  = 0;
        $skipped = 0;
        $failed  = [];

        foreach ($files as $path) {
            try {
                if (Storage::disk('cdn')->exists($path)) {
                    $skipped++;
                } else {
                    Storage::disk('cdn')->put($path, Storage::disk('public')->get($path));
                    $copied++;
                }
            } catch (\Throwable $e) {
                $failed[] = "{$path}: {$e->getMessage()}";
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Copied: {$copied}, already present: {$skipped}, failed: " . count($failed));

        foreach ($failed as $f) {
            $this->error($f);
        }

        return empty($failed) ? self::SUCCESS : self::FAILURE;
    }
}
