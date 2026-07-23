<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create why_cholan_tour and tour_services tables
 * if they do not already exist (safe to run multiple times).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Why Indian Panorama items ──────────────────────────────────────
        if (!Schema::hasTable('why_cholan_tour')) {
            Schema::create('why_cholan_tour', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->json('details')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // ── Tour Services (icon cards on homepage) ─────────────────────────
        if (!Schema::hasTable('tour_services')) {
            Schema::create('tour_services', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->string('link', 500)->nullable();
                $table->string('banner_image', 500)->nullable();
                $table->string('banner_image_alt', 255)->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->boolean('is_deleted')->default(false)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_services');
        Schema::dropIfExists('why_cholan_tour');
    }
};
