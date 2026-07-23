<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('banner_image_alt');
            $table->string('template', 50)->default('default')->after('is_published');
            $table->string('meta_title')->nullable()->after('template');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('canonical_url')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->boolean('in_sitemap')->default(true)->after('og_image');
            $table->boolean('in_menu')->default(false)->after('in_sitemap');
            $table->string('menu_label')->nullable()->after('in_menu');
            $table->integer('menu_order')->default(0)->after('menu_label');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn([
                'is_published', 'template', 'meta_title', 'meta_description',
                'canonical_url', 'og_image', 'in_sitemap', 'in_menu', 'menu_label', 'menu_order',
            ]);
        });
    }
};
