<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->integer('days')->default(0);
            $table->integer('nights')->default(0);
            $table->longText('itinerary')->nullable();
            $table->longText('includes')->nullable();
            $table->longText('excludes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('package_details'); }
};
