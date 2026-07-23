<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove the foreign-key column from cms_page_sections first
        Schema::table('cms_page_sections', function (Blueprint $table) {
            if (Schema::hasColumn('cms_page_sections', 'reusable_block_id')) {
                $table->dropColumn('reusable_block_id');
            }
        });

        Schema::dropIfExists('reusable_blocks');
    }

    public function down(): void
    {
        Schema::create('reusable_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('type', 50);
            $table->json('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('cms_page_sections', function (Blueprint $table) {
            $table->foreignId('reusable_block_id')->nullable()->constrained('reusable_blocks')->nullOnDelete();
        });
    }
};
