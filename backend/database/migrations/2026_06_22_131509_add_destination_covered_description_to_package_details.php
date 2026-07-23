<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_details', function (Blueprint $table) {
            if (!Schema::hasColumn('package_details', 'destination_covered_description')) {
                $table->longText('destination_covered_description')->nullable()->after('tour_highlights');
            }
        });
    }

    public function down(): void
    {
        Schema::table('package_details', function (Blueprint $table) {
            if (Schema::hasColumn('package_details', 'destination_covered_description')) {
                $table->dropColumn('destination_covered_description');
            }
        });
    }
};
