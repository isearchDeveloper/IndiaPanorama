<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('experience_categories')->cascadeOnDelete();

            $table->string('name');
            // Shares one slug namespace with experience_categories (validated in the app layer,
            // a DB-unique constraint can't span two tables) and must never contain "-in-"
            // (reserved as the state-filter separator, e.g. waterfalls-tours-in-kerala).
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->text('description')->nullable();
            $table->string('popular_tag')->nullable();

            // Subcategory listing page banner
            $table->string('banner_image')->nullable();
            $table->text('banner_description')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_subcategories');
    }
};
