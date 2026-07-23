<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('location_id');
            $table->foreign('category_id')->references('id')->on('tourist_activity_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tourist_activities', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
