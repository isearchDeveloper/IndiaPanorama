<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `festival_faqs` and `festival_meta_data` already exist as pre-existing tables
     * (festival_id, question/answer, and the standard meta_title/.../meta_details set
     * respectively) — only `sort_order` and `festivals.faq_title` were actually missing.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('festivals', 'faq_title')) {
            Schema::table('festivals', function (Blueprint $table) {
                $table->string('faq_title')->nullable()->after('why_visit_title');
            });
        }

        if (!Schema::hasColumn('festival_faqs', 'sort_order')) {
            Schema::table('festival_faqs', function (Blueprint $table) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('answer');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('festival_faqs', 'sort_order')) {
            Schema::table('festival_faqs', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasColumn('festivals', 'faq_title')) {
            Schema::table('festivals', function (Blueprint $table) {
                $table->dropColumn('faq_title');
            });
        }
    }
};
