<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holiday_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 200)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->string('faq_title')->nullable();
            $table->timestamps();
        });

        Schema::create('holiday_setting_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_setting_id')->index();
            // Hero Section (Edit modal)
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->string('banner_title')->nullable();
            $table->text('banner_description')->nullable();
            // Content Section (Edit modal)
            $table->string('main_heading')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            // Tour Packages Section (Edit modal)
            $table->string('tour_packages_heading')->nullable();
            $table->text('tour_packages_description')->nullable();
            // Popular Packages Section (Edit modal)
            $table->string('popular_packages_heading')->nullable();
            $table->text('popular_packages_description')->nullable();
            // Enquiry Form (Settings modal)
            $table->string('enquiry_title')->nullable();
            $table->string('enquiry_subtitle')->nullable();
            // Luxury Adventure & Budget (Settings modal)
            $table->string('luxury_title')->nullable();
            $table->text('luxury_description')->nullable();
            // Popular Tour Packages (Settings modal)
            $table->string('popular_tour_title')->nullable();
            $table->text('popular_tour_description')->nullable();
            // Additional Content (Settings modal)
            $table->longText('additional_content')->nullable();
            $table->timestamps();
        });

        Schema::create('holiday_setting_faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_setting_id')->index();
            $table->text('question');
            $table->longText('answer')->nullable();
            $table->timestamps();
        });

        Schema::create('holiday_setting_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_setting_id')->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_setting_meta');
        Schema::dropIfExists('holiday_setting_faqs');
        Schema::dropIfExists('holiday_setting_details');
        Schema::dropIfExists('holiday_settings');
    }
};
