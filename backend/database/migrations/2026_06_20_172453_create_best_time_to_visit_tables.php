<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('states', 'best_time_title')) {
            Schema::table('states', function (Blueprint $table) {
                $table->string('best_time_title')->nullable()->after('faq_title');
            });
        }

        if (!Schema::hasColumn('locations', 'best_time_title')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('best_time_title')->nullable()->after('faq_title');
            });
        }

        if (!Schema::hasTable('state_best_times')) {
            Schema::create('state_best_times', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('state_id')->index();
                $table->string('month_range');
                $table->text('tagline')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('location_best_times')) {
            Schema::create('location_best_times', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_id')->index();
                $table->string('month_range');
                $table->text('tagline')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('state_best_times');
        Schema::dropIfExists('location_best_times');

        if (Schema::hasColumn('states', 'best_time_title')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('best_time_title');
            });
        }

        if (Schema::hasColumn('locations', 'best_time_title')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('best_time_title');
            });
        }
    }
};
