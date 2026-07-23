<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Regions: details ─────────────────────────────────────────────────
        if (!Schema::hasTable('regions_details')) {
            Schema::create('regions_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('region_id')->index();
                $table->string('title')->nullable();
                $table->string('sub_title')->nullable();
                $table->string('banner_image')->nullable();
                $table->string('banner_image_alt')->nullable();
                $table->longText('about')->nullable();
                $table->string('author_name')->nullable();
                $table->timestamps();
            });
        }

        // ── Regions: meta ────────────────────────────────────────────────────
        if (!Schema::hasTable('regions_meta_data')) {
            Schema::create('regions_meta_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('region_id')->index();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->string('h1_heading')->nullable();
                $table->text('meta_details')->nullable();
                $table->timestamps();
            });
        }

        // ── Regions: faqs ────────────────────────────────────────────────────
        if (!Schema::hasTable('region_faqs')) {
            Schema::create('region_faqs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('region_id')->index();
                $table->text('question');
                $table->longText('answer')->nullable();
                $table->timestamps();
            });
        }

        // ── Locations: details ───────────────────────────────────────────────
        if (!Schema::hasTable('location_details')) {
            Schema::create('location_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_id')->index();
                $table->string('title')->nullable();
                $table->string('sub_title')->nullable();
                $table->string('banner_image')->nullable();
                $table->string('banner_image_alt')->nullable();
                $table->longText('about')->nullable();
                $table->timestamps();
            });
        }

        // ── Locations: meta ──────────────────────────────────────────────────
        if (!Schema::hasTable('location_meta_data')) {
            Schema::create('location_meta_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_id')->index();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->string('h1_heading')->nullable();
                $table->text('meta_details')->nullable();
                $table->timestamps();
            });
        }

        // ── Locations: faqs ──────────────────────────────────────────────────
        if (!Schema::hasTable('location_faqs')) {
            Schema::create('location_faqs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_id')->index();
                $table->text('question');
                $table->longText('answer')->nullable();
                $table->timestamps();
            });
        }

        // ── Ensure regions has faq_title column ──────────────────────────────
        if (Schema::hasTable('regions') && !Schema::hasColumn('regions', 'faq_title')) {
            Schema::table('regions', function (Blueprint $table) {
                $table->string('faq_title')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('region_faqs');
        Schema::dropIfExists('regions_meta_data');
        Schema::dropIfExists('regions_details');
        Schema::dropIfExists('location_faqs');
        Schema::dropIfExists('location_meta_data');
        Schema::dropIfExists('location_details');
    }
};
