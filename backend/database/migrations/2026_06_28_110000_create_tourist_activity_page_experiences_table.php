<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->string('experiences_title')->nullable()->after('short_description');
        });

        Schema::create('tourist_activity_page_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('tourist_activity_pages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourist_activity_page_experiences');

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropColumn('experiences_title');
        });
    }
};
