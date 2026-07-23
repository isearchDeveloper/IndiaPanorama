<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_page_faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('question');
            $table->text('answer')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('experience_pages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_page_faqs');
    }
};
