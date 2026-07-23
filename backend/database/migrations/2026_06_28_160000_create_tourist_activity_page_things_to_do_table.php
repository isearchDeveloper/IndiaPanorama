<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->string('things_to_do_title')->nullable()->after('waterfalls_title');
        });

        Schema::create('tourist_activity_page_things_to_do', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('duration_timing')->nullable();
            $table->string('best_for')->nullable();
            $table->string('approximate_cost')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('tourist_activity_pages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourist_activity_page_things_to_do');

        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropColumn('things_to_do_title');
        });
    }
};
