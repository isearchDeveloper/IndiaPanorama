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
            $table->string('intro_image')->nullable()->after('short_description');
            $table->string('intro_image_alt')->nullable()->after('intro_image');
            $table->longText('long_description')->nullable()->after('intro_image_alt');
            $table->string('key_experience_title')->nullable()->after('long_description');
            $table->string('why_visit_title')->nullable()->after('key_experience_title');
        });

        Schema::create('festival_key_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('festival_how_to_reach', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('mode');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('festival_why_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_why_visits');
        Schema::dropIfExists('festival_how_to_reach');
        Schema::dropIfExists('festival_key_experiences');

        Schema::table('festivals', function (Blueprint $table) {
            $table->dropColumn([
                'intro_image', 'intro_image_alt', 'long_description',
                'key_experience_title', 'why_visit_title',
            ]);
        });
    }
};
