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
        Schema::table('car_routes', function (Blueprint $table) {
            $table->string('faq_sub_title')->nullable()->after('faq_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('car_routes', function (Blueprint $table) {
            $table->dropColumn('faq_sub_title');
        });
    }
};
