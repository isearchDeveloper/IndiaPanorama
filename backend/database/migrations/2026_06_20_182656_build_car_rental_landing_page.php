<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Fleet: replace fixed Economy/Executive/Luxury "type" with CarCategory-driven
        //    filtering (Sedan/SUV/Traveller/Bus etc, matching the live frontend), add fuel_type.
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'type')) {
                $table->dropColumn('type');
            }
            if (!Schema::hasColumn('cars', 'fuel_type')) {
                $table->string('fuel_type', 50)->nullable()->after('seats');
            }
        });

        // ── Popular Locations widget: flag + optional custom display label on existing City/Route.
        Schema::table('car_city', function (Blueprint $table) {
            if (!Schema::hasColumn('car_city', 'is_popular')) {
                $table->boolean('is_popular')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('car_city', 'display_label')) {
                $table->string('display_label', 256)->nullable()->after('is_popular');
            }
        });

        Schema::table('car_routes', function (Blueprint $table) {
            if (!Schema::hasColumn('car_routes', 'is_popular')) {
                $table->boolean('is_popular')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('car_routes', 'display_label')) {
                $table->string('display_label', 256)->nullable()->after('is_popular');
            }
        });

        // ── Car rental landing page — extra structured content not covered by the
        //    generic `pages` table (which already drives hero/welcome-intro/FAQs/meta for Page id 6).
        if (!Schema::hasTable('car_rental_contents')) {
            Schema::create('car_rental_contents', function (Blueprint $table) {
                $table->id();
                $table->string('checklist_title')->nullable();
                $table->string('about_title')->nullable();
                $table->longText('about_description')->nullable();
                $table->string('why_choose_title')->nullable();
                $table->longText('why_choose_description')->nullable();
                $table->string('popular_locations_title')->nullable();
                $table->string('road_trip_title')->nullable();
                $table->string('road_trip_subtitle')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_rental_checklist_items')) {
            Schema::create('car_rental_checklist_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_rental_content_id')->index();
                $table->string('text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_rental_gallery_images')) {
            Schema::create('car_rental_gallery_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_rental_content_id')->index();
                $table->string('image');
                $table->string('image_alt')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_rental_why_choose_stats')) {
            Schema::create('car_rental_why_choose_stats', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_rental_content_id')->index();
                $table->string('icon')->nullable();
                $table->string('icon_alt')->nullable();
                $table->string('label');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // ── "Popular Road Trip Destinations" carousel.
        if (!Schema::hasTable('car_rental_road_trips')) {
            Schema::create('car_rental_road_trips', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('state_id')->index();
                $table->string('image')->nullable();
                $table->string('image_alt')->nullable();
                $table->decimal('rating', 2, 1)->default(0);
                $table->string('route_text')->nullable();
                $table->integer('duration_days')->nullable();
                $table->integer('duration_nights')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('car_rental_road_trips');
        Schema::dropIfExists('car_rental_why_choose_stats');
        Schema::dropIfExists('car_rental_gallery_images');
        Schema::dropIfExists('car_rental_checklist_items');
        Schema::dropIfExists('car_rental_contents');

        Schema::table('car_routes', function (Blueprint $table) {
            if (Schema::hasColumn('car_routes', 'display_label')) {
                $table->dropColumn('display_label');
            }
            if (Schema::hasColumn('car_routes', 'is_popular')) {
                $table->dropColumn('is_popular');
            }
        });

        Schema::table('car_city', function (Blueprint $table) {
            if (Schema::hasColumn('car_city', 'display_label')) {
                $table->dropColumn('display_label');
            }
            if (Schema::hasColumn('car_city', 'is_popular')) {
                $table->dropColumn('is_popular');
            }
        });

        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'fuel_type')) {
                $table->dropColumn('fuel_type');
            }
            if (!Schema::hasColumn('cars', 'type')) {
                $table->string('type', 256)->default('Economy');
            }
        });
    }
};
