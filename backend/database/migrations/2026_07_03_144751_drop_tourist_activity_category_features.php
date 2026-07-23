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
        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropForeign(['featured_category_id']);
            $table->dropColumn(['featured_category_enabled', 'featured_category_title', 'featured_category_id']);
        });

        Schema::dropIfExists('tourist_activity_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tourist_activity_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->boolean('featured_category_enabled')->default(false)->after('experiences_title');
            $table->string('featured_category_title')->nullable()->after('featured_category_enabled');
            $table->unsignedBigInteger('featured_category_id')->nullable()->after('featured_category_title');
            $table->foreign('featured_category_id')->references('id')->on('tourist_activity_categories')->nullOnDelete();
        });

        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('location_id');
            $table->foreign('category_id')->references('id')->on('tourist_activity_categories')->nullOnDelete();
        });
    }
};
