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
        Schema::table('festival_settings', function (Blueprint $table) {
            $table->string('why_experience_title')->nullable();
            $table->text('why_experience_sub_title')->nullable();
        });

        Schema::create('festival_setting_why_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_setting_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_setting_why_experiences');

        Schema::table('festival_settings', function (Blueprint $table) {
            $table->dropColumn(['why_experience_title', 'why_experience_sub_title']);
        });
    }
};
