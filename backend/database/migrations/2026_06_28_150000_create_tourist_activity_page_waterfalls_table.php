<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->string('waterfalls_title')->nullable()->after('featured_category_id');
        });

        Schema::create('tourist_activity_page_waterfalls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('image')->nullable();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('tourist_activity_pages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourist_activity_page_waterfalls');

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropColumn('waterfalls_title');
        });
    }
};
