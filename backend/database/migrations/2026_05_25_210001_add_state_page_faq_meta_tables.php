<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add faq_title to states (same as regions table has it)
        if (!Schema::hasColumn('states', 'faq_title')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('faq_title')->nullable()->after('is_active');
            });
        }

        // state_details — banner, content, author (mirrors regions_details)
        if (!Schema::hasTable('state_details')) {
            Schema::create('state_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('state_id')->index();
                $table->string('title')->nullable();
                $table->string('sub_title')->nullable();
                $table->string('banner_image')->nullable();
                $table->string('banner_image_alt')->nullable();
                $table->longText('about')->nullable();
                $table->string('author_name')->nullable();
                $table->timestamps();
            });
        }

        // state_faqs — Q&A pairs (mirrors region_faqs)
        if (!Schema::hasTable('state_faqs')) {
            Schema::create('state_faqs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('state_id')->index();
                $table->text('question');
                $table->longText('answer')->nullable();
                $table->timestamps();
            });
        }

        // state_meta_data — SEO fields (mirrors regions_meta_data)
        if (!Schema::hasTable('state_meta_data')) {
            Schema::create('state_meta_data', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('state_id')->index();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->string('h1_heading')->nullable();
                $table->longText('meta_details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('state_meta_data');
        Schema::dropIfExists('state_faqs');
        Schema::dropIfExists('state_details');

        if (Schema::hasColumn('states', 'faq_title')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('faq_title');
            });
        }
    }
};
