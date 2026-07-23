<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * seats was int(11) NOT NULL — widened to varchar so admins can enter
     * multi-value seating like "6,7,8" for cars that come in more than one
     * seating configuration, not just a single number.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE cars MODIFY seats VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE cars MODIFY seats INT(11) NOT NULL");
    }
};
