<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `cms_pages`, `car_categories`, `car_city`, `car_routes`, and `cars` all generate their
 * slug via an app-level "check if taken, retry with a suffix" loop but have no DB-level
 * unique constraint backing it up — under a genuine race (two admins saving the same name
 * at once) this would silently create duplicate slugs rather than fail loudly, since
 * nothing at the database layer would reject the second insert.
 */
return new class extends Migration
{
    private array $tables = ['cms_pages', 'car_categories', 'car_city', 'car_routes', 'cars'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'slug') && !$this->hasIndex($table, 'slug')) {
                Schema::table($table, fn (Blueprint $t) => $t->unique('slug'));
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && $this->hasIndex($table, 'slug')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropUnique([$table . '_slug_unique']));
            }
        }
    }

    private function hasIndex(string $table, string $column): bool
    {
        return collect(\Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}` WHERE Column_name = ?", [$column]))->isNotEmpty();
    }
};
