<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manage_city_meta', function (Blueprint $table) {
            $table->text('meta_details')->nullable()->after('h1_heading');
        });
    }

    public function down(): void
    {
        Schema::table('manage_city_meta', function (Blueprint $table) {
            $table->dropColumn('meta_details');
        });
    }
};
