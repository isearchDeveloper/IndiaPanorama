<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('branches', 'image')) {
            Schema::table('branches', fn (Blueprint $table) => $table->dropColumn('image'));
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('branches', 'image')) {
            Schema::table('branches', fn (Blueprint $table) => $table->string('image')->nullable());
        }
    }
};
