<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        foreach (DB::table('festivals')->whereNull('slug')->get() as $festival) {
            DB::table('festivals')
                ->where('id', $festival->id)
                ->update(['slug' => Str::slug($festival->name) . '-' . $festival->id]);
        }

        Schema::table('festivals', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('festivals', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
