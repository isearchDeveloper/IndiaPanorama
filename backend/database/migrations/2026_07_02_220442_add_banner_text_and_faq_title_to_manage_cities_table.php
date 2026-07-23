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
            $table->text('banner_text')->nullable()->after('sub_title');
            $table->string('faq_title')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('manage_cities', function (Blueprint $table) {
            $table->dropColumn(['banner_text', 'faq_title']);
        });
    }
};
