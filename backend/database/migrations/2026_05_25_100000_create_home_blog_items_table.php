<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_blog_items', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('image_alt', 255)->nullable();
            $table->string('link', 500)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_blog_items');
    }
};
