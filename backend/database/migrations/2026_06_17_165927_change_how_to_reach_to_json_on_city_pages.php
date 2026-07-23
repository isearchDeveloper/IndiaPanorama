<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE city_pages SET how_to_reach = NULL WHERE how_to_reach IS NOT NULL AND JSON_VALID(how_to_reach) = 0');
        DB::statement('ALTER TABLE city_pages MODIFY how_to_reach JSON NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE city_pages MODIFY how_to_reach LONGTEXT NULL');
    }
};
