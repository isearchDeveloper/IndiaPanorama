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
            $table->string('why_choose_title')->nullable();
            $table->text('why_choose_sub_title')->nullable();
        });

        Schema::create('festival_setting_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_setting_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->string('stat');
            $table->string('label')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('festival_setting_highlights');

        Schema::table('festival_settings', function (Blueprint $table) {
            $table->dropColumn(['why_choose_title', 'why_choose_sub_title']);
        });
    }
};
