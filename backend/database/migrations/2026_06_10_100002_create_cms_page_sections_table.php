<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_page_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('cms_page_id');
            $table->foreign('cms_page_id')->references('id')->on('cms_pages')->cascadeOnDelete();
            $table->unsignedBigInteger('reusable_block_id')->nullable();
            $table->foreign('reusable_block_id')->references('id')->on('reusable_blocks')->nullOnDelete();
            $table->string('type', 50);
            $table->string('label', 150)->nullable();
            $table->json('content')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['cms_page_id', 'sort_order']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_page_sections');
    }
};
