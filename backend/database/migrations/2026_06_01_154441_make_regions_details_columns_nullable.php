<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions_details', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('sub_title')->nullable()->change();
            $table->string('banner_image')->nullable()->change();
            $table->string('banner_image_alt')->nullable()->change();
            $table->longText('about')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('regions_details', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('sub_title')->nullable(false)->change();
            $table->string('banner_image')->nullable(false)->change();
            $table->string('banner_image_alt')->nullable(false)->change();
            $table->longText('about')->nullable(false)->change();
        });
    }
};
