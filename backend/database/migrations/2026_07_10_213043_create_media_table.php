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
        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->string('disk');
            $table->string('folder')->nullable();
            $table->string('path')->unique(); // bare relative path, e.g. "banner/xyz-169999.webp"
            $table->string('filename');
            $table->string('original_name')->nullable(); // known only for files uploaded via the library
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->unsignedInteger('width')->nullable(); // best-effort, captured at upload time only
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();

            // 'upload'  = created through the Media Library's own upload endpoint
            // 'synced'  = discovered on disk by the media:sync backfill command
            $table->string('source')->default('synced');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
