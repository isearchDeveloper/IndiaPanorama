<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Vehicle category icons (for "Our Wide Range Of Vehicles") ──
        Schema::table('car_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('car_categories', 'icon')) {
                $table->string('icon')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('car_categories', 'icon_alt')) {
                $table->string('icon_alt')->nullable()->after('icon');
            }
        });

        // ── Per-city Features/Benefits override + gallery ──
        Schema::table('car_city', function (Blueprint $table) {
            if (!Schema::hasColumn('car_city', 'features_title')) {
                $table->string('features_title')->nullable()->after('display_label');
            }
            if (!Schema::hasColumn('car_city', 'benefits_title')) {
                $table->string('benefits_title')->nullable()->after('features_title');
            }
        });

        if (!Schema::hasTable('car_city_features')) {
            Schema::create('car_city_features', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('city_id')->index();
                $table->string('text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_city_benefits')) {
            Schema::create('car_city_benefits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('city_id')->index();
                $table->string('text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_city_gallery_images')) {
            Schema::create('car_city_gallery_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('city_id')->index();
                $table->string('image');
                $table->string('image_alt')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // ── Global default Features/Benefits (fallback when a city hasn't set its own) ──
        Schema::table('car_rental_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('car_rental_contents', 'features_title')) {
                $table->string('features_title')->nullable()->after('short_description');
            }
            if (!Schema::hasColumn('car_rental_contents', 'benefits_title')) {
                $table->string('benefits_title')->nullable()->after('features_title');
            }
        });

        if (!Schema::hasTable('car_rental_features')) {
            Schema::create('car_rental_features', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_rental_content_id')->index();
                $table->string('text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_rental_benefits')) {
            Schema::create('car_rental_benefits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_rental_content_id')->index();
                $table->string('text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // ── Vehicle (Car) detail page: description, specs, gallery, highlight tags, amenities ──
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('cars', 'vehicle_type')) {
                $table->string('vehicle_type', 100)->nullable()->after('seats');
            }
            if (!Schema::hasColumn('cars', 'transmission')) {
                $table->string('transmission', 100)->nullable()->after('vehicle_type');
            }
            if (!Schema::hasColumn('cars', 'luggage_capacity')) {
                $table->string('luggage_capacity', 100)->nullable()->after('transmission');
            }
            if (!Schema::hasColumn('cars', 'mileage')) {
                $table->string('mileage', 100)->nullable()->after('luggage_capacity');
            }
        });

        if (!Schema::hasTable('car_gallery_images')) {
            Schema::create('car_gallery_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_id')->index();
                $table->string('image');
                $table->string('image_alt')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_highlight_tags')) {
            Schema::create('car_highlight_tags', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_id')->index();
                $table->string('text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_amenities')) {
            Schema::create('car_amenities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('car_id')->index();
                $table->string('icon')->nullable();
                $table->string('icon_alt')->nullable();
                $table->string('label');
                $table->string('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('car_amenities');
        Schema::dropIfExists('car_highlight_tags');
        Schema::dropIfExists('car_gallery_images');
        Schema::dropIfExists('car_rental_benefits');
        Schema::dropIfExists('car_rental_features');
        Schema::dropIfExists('car_city_gallery_images');
        Schema::dropIfExists('car_city_benefits');
        Schema::dropIfExists('car_city_features');

        Schema::table('cars', function (Blueprint $table) {
            foreach (['description', 'vehicle_type', 'transmission', 'luggage_capacity', 'mileage'] as $col) {
                if (Schema::hasColumn('cars', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('car_rental_contents', function (Blueprint $table) {
            foreach (['features_title', 'benefits_title'] as $col) {
                if (Schema::hasColumn('car_rental_contents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('car_city', function (Blueprint $table) {
            foreach (['features_title', 'benefits_title'] as $col) {
                if (Schema::hasColumn('car_city', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('car_categories', function (Blueprint $table) {
            foreach (['icon', 'icon_alt'] as $col) {
                if (Schema::hasColumn('car_categories', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
