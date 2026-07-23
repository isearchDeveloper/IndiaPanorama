<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->string('banner_subtitle')->nullable()->after('image_alt');
            $table->string('banner_description')->nullable()->after('banner_subtitle');
            $table->string('highlights_title')->nullable()->after('key_experience_title');
            $table->string('places_title')->nullable()->after('why_visit_title');
            $table->string('packages_title')->nullable()->after('places_title');
        });

        Schema::create('festival_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('festival_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('festival_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_places');
        Schema::dropIfExists('festival_highlights');
        Schema::dropIfExists('festival_stats');

        Schema::table('festivals', function (Blueprint $table) {
            $table->dropColumn([
                'banner_subtitle', 'banner_description',
                'highlights_title', 'places_title', 'packages_title',
            ]);
        });
    }
};
