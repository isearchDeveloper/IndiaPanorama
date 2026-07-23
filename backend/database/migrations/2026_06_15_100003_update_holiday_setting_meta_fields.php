<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_setting_meta', function (Blueprint $table) {
            $table->string('h1_heading')->nullable()->after('meta_keywords');
            $table->text('meta_details')->nullable()->after('h1_heading');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_setting_meta', function (Blueprint $table) {
            $table->dropColumn(['h1_heading', 'meta_details']);
        });
    }
};
