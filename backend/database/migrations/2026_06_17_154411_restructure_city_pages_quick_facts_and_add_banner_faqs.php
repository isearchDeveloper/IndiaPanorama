<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('city_pages', function (Blueprint $table) {
            $table->dropColumn([
                'capital_city', 'best_season', 'major_cities', 'climate',
                'famous_festivals', 'key_attractions', 'languages', 'cuisine', 'unique_experiences',
            ]);

            $table->string('title')->nullable()->after('state_id');
            $table->string('banner_image')->nullable()->after('title');
            $table->string('banner_image_alt')->nullable()->after('banner_image');
            $table->text('banner_text')->nullable()->after('banner_image_alt');
            $table->text('short_description')->nullable()->after('banner_text');
        });

        Schema::create('city_page_quick_facts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_page_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('city_page_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_page_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->text('answer')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_page_faqs');
        Schema::dropIfExists('city_page_quick_facts');

        Schema::table('city_pages', function (Blueprint $table) {
            $table->dropColumn(['title', 'banner_image', 'banner_image_alt', 'banner_text', 'short_description']);

            $table->string('capital_city')->nullable();
            $table->string('best_season')->nullable();
            $table->string('major_cities')->nullable();
            $table->string('climate')->nullable();
            $table->string('famous_festivals')->nullable();
            $table->string('key_attractions')->nullable();
            $table->string('languages')->nullable();
            $table->string('cuisine')->nullable();
            $table->string('unique_experiences')->nullable();
        });
    }
};
