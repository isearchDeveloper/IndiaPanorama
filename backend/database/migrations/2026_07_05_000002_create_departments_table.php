<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename old singular `department` table to plural `departments` (preserves data)
        if (Schema::hasTable('department') && !Schema::hasTable('departments')) {
            Schema::rename('department', 'departments');
        }

        // Fresh install: create the table if it still doesn't exist
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('departments') && !Schema::hasTable('department')) {
            Schema::rename('departments', 'department');
        }
    }
};
