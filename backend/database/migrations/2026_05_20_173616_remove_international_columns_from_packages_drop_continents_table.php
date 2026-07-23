<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function dropForeignIfExists(string $table, string $fkName): void
    {
        $exists = DB::select("
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = ?
              AND CONSTRAINT_NAME = ?
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$table, $fkName]);

        if (!empty($exists)) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`");
        }
    }

    public function up(): void
    {
        // Drop international FK constraints (if they exist)
        $this->dropForeignIfExists('packages', 'packages_continent_id_foreign');
        $this->dropForeignIfExists('packages', 'packages_source_continent_id_foreign');
        $this->dropForeignIfExists('packages', 'packages_source_country_id_foreign');

        // Drop international columns from packages (only if they exist)
        Schema::table('packages', function (Blueprint $table) {
            $toDrop = array_filter(
                ['type', 'continent_id', 'source_continent_id', 'source_country_id'],
                fn($col) => Schema::hasColumn('packages', $col)
            );

            if (!empty($toDrop)) {
                $table->dropColumn(array_values($toDrop));
            }
        });

        // Drop continent-related tables
        Schema::dropIfExists('continent_countries');
        Schema::dropIfExists('continents');
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->after('slug');
            $table->unsignedBigInteger('continent_id')->nullable()->after('type');
            $table->unsignedBigInteger('source_continent_id')->nullable();
            $table->unsignedBigInteger('source_country_id')->nullable();
        });
    }
};
