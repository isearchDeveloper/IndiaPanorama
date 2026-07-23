<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE city_pages MODIFY travel_tips LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE city_pages MODIFY travel_tips JSON NULL');
    }
};
