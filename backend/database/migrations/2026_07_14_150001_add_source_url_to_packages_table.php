<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks which old-site URL a Package was migrated from, so the old-site
 * import command can detect and skip already-imported pages — needed because
 * old-site listing pages (region/state) re-list the same package URLs across
 * multiple pages (e.g. a Rajasthan package also appears on the North India
 * listing).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('packages', 'source_url')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->string('source_url')->nullable()->unique()->after('slug');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('packages', 'source_url')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('source_url');
        });
    }
};
