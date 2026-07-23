<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Note: App\Models\Permission was removed when permissions were restructured to
// be name-based (see 2026_07_01_200000_restructure_admin_permissions_to_name_based.php,
// which later drops the admin_permissions table entirely). Uses the query builder
// directly (not the deleted model) so this migration stays runnable on a fresh install.
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_permissions')) return;

        DB::table('admin_permissions')->updateOrInsert(
            ['name' => 'bookings.view'],
            ['label' => 'Bookings View', 'group' => 'More', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('admin_permissions')) return;

        DB::table('admin_permissions')->where('name', 'bookings.view')->delete();
    }
};
