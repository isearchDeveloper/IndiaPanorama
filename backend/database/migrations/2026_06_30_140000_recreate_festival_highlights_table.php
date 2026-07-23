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
        Schema::create('festival_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('festivals', function (Blueprint $table) {
            $table->string('highlights_title')->nullable()->after('key_experience_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->dropColumn('highlights_title');
        });

        Schema::dropIfExists('festival_highlights');
    }
};
