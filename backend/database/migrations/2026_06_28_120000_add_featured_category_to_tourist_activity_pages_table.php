<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Lets admin spotlight one real category's activities (e.g. "Exciting Water Activities in Kerala") per state/city page. */
    public function up(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->boolean('featured_category_enabled')->default(false)->after('experiences_title');
            $table->string('featured_category_title')->nullable()->after('featured_category_enabled');
            $table->unsignedBigInteger('featured_category_id')->nullable()->after('featured_category_title');

            $table->foreign('featured_category_id')->references('id')->on('tourist_activity_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activity_pages', function (Blueprint $table) {
            $table->dropForeign(['featured_category_id']);
            $table->dropColumn(['featured_category_enabled', 'featured_category_title', 'featured_category_id']);
        });
    }
};
