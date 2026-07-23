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
        Schema::create('image_licenses', function (Blueprint $table) {
            $table->id();

            // Polymorphic owner — any model (Package, Region, State, Festival, Theme, ...) can attach one of these.
            $table->morphs('licensable');

            // A model can have more than one licensed image (e.g. primary + gallery items),
            // so field_key distinguishes which image slot this record belongs to.
            // 'primary' for a single/main image; an arbitrary string (e.g. gallery image ID) for repeatable ones.
            $table->string('field_key')->default('primary');

            $table->string('source_of_image')->nullable();
            $table->date('download_date')->nullable();
            $table->string('account_id')->nullable();
            $table->string('license_key')->nullable();
            $table->string('license_key_file')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_licenses');
    }
};
