<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Closes a gap left by 2026_07_06_120000_add_deleted_at_to_all_tables — the
 * Experience module was added afterward without the softDeletes() column, so
 * its models never got SoftDeletes.
 */
return new class extends Migration
{
    private array $tables = [
        'experience_category_faqs', 'experience_category_perfect_fors',
        'experience_category_popular_cities', 'experience_category_quick_infos',
        'experience_faqs', 'experience_gallery_images', 'experience_highlights',
        'experience_pages', 'experience_page_activities', 'experience_page_faqs',
        'experience_page_highlights', 'experience_settings', 'experience_setting_best_times',
        'experience_setting_faqs', 'experience_setting_why_choose_items',
    ];

    public function up(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && !Schema::hasColumn($t, 'deleted_at')) {
                Schema::table($t, fn (Blueprint $table) => $table->softDeletes());
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'deleted_at')) {
                Schema::table($t, fn (Blueprint $table) => $table->dropSoftDeletes());
            }
        }
    }
};
