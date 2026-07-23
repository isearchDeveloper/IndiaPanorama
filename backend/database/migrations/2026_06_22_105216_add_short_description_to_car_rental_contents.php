<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_rental_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('car_rental_contents', 'short_description')) {
                $table->text('short_description')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('car_rental_contents', function (Blueprint $table) {
            if (Schema::hasColumn('car_rental_contents', 'short_description')) {
                $table->dropColumn('short_description');
            }
        });
    }
};
