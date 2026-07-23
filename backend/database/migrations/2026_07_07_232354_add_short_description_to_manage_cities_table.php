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
        Schema::table('manage_cities', function (Blueprint $table) {
            $table->longText('short_description')->nullable()->after('sub_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manage_cities', function (Blueprint $table) {
            $table->dropColumn('short_description');
        });
    }
};
