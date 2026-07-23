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
        // Add content columns to existing manage_cities table
        Schema::table('manage_cities', function (Blueprint $table) {
            $table->longText('travel_tips')->nullable()->after('about');
            $table->longText('things_to_know')->nullable()->after('travel_tips');
            $table->longText('religious_tourism')->nullable()->after('things_to_know');
            $table->longText('souvenirs_to_shop')->nullable()->after('religious_tourism');
            $table->longText('popular_dishes')->nullable()->after('souvenirs_to_shop');
        });

        // How To Reach rows
        Schema::create('manage_city_how_to_reach', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_city_id')->index();
            $table->string('mode')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Quick Facts
        Schema::create('manage_city_quick_facts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_city_id')->index();
            $table->string('label')->nullable();
            $table->string('value')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // FAQs
        Schema::create('manage_city_faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_city_id')->index();
            $table->text('question')->nullable();
            $table->longText('answer')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Meta
        Schema::create('manage_city_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manage_city_id')->unique()->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('h1_heading')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manage_city_meta');
        Schema::dropIfExists('manage_city_faqs');
        Schema::dropIfExists('manage_city_quick_facts');
        Schema::dropIfExists('manage_city_how_to_reach');

        Schema::table('manage_cities', function (Blueprint $table) {
            $table->dropColumn(['travel_tips', 'things_to_know', 'religious_tourism', 'souvenirs_to_shop', 'popular_dishes']);
        });
    }
};
