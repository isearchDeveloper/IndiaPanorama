<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festival_state_pages', function (Blueprint $table) {
            $table->id();
            $table->integer('state_id')->unique();
            $table->unsignedBigInteger('featured_festival_id')->nullable();
            $table->string('title')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->string('banner_text')->nullable();
            $table->text('short_description')->nullable();
            $table->string('why_visit_title')->nullable();
            $table->text('why_visit_sub_title')->nullable();
            $table->string('faq_title')->nullable();
            $table->text('faq_sub_title')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('h1_heading')->nullable();
            $table->text('meta_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states')->cascadeOnDelete();
            $table->foreign('featured_festival_id')->references('id')->on('festivals')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festival_state_pages');
    }
};
