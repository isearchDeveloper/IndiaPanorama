<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->string('primary_image')->nullable();
            $table->decimal('price',10,2)->default(0);
            $table->boolean('featured')->default(false);
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('packages'); }
};
