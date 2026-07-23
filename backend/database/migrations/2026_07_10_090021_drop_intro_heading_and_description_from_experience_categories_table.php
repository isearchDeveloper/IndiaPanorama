<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The category page's "Intro" section no longer needs its own heading/description —
 * the category's existing Short Description now serves as the intro text too.
 * `intro_image` is kept (still edited from the Add/Edit Category form, just relabelled
 * "Intro Image" and moved to sit right after Short Description).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experience_categories', function (Blueprint $table) {
            $table->dropColumn(['intro_heading', 'intro_description']);
        });
    }

    public function down(): void
    {
        Schema::table('experience_categories', function (Blueprint $table) {
            $table->string('intro_heading')->nullable();
            $table->text('intro_description')->nullable();
        });
    }
};
