<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Preserve existing user→permission_name assignments before dropping old structure
        $existing = [];
        if (Schema::hasTable('admin_user_permissions') && Schema::hasTable('admin_permissions')) {
            $existing = DB::table('admin_user_permissions')
                ->join('admin_permissions', 'admin_user_permissions.admin_permission_id', '=', 'admin_permissions.id')
                ->select('admin_user_permissions.user_id', 'admin_permissions.name as permission_name')
                ->get()
                ->toArray();
        } elseif (Schema::hasTable('admin_user_permissions') && Schema::hasColumn('admin_user_permissions', 'permission_name')) {
            // Already migrated — nothing to do
            return;
        }

        // 2. Drop old pivot and re-create with string-based permission_name
        Schema::dropIfExists('admin_user_permissions');
        Schema::create('admin_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('permission_name');
            $table->timestamps();
            $table->unique(['user_id', 'permission_name']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Re-insert only permissions that still exist in config
        $validPerms = \App\Services\AdminPermissions::allNames();
        foreach ($existing as $row) {
            if (in_array($row->permission_name, $validPerms)) {
                DB::table('admin_user_permissions')->insertOrIgnore([
                    'user_id'         => $row->user_id,
                    'permission_name' => $row->permission_name,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        // 4. Drop the now-unused admin_permissions definition table
        Schema::dropIfExists('admin_permissions');
    }

    public function down(): void
    {
        // Restore admin_permissions table and old pivot structure
        Schema::dropIfExists('admin_user_permissions');

        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('group')->default('General');
            $table->timestamps();
        });

        Schema::create('admin_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_permission_id');
            $table->timestamps();
            $table->unique(['user_id', 'admin_permission_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_permission_id')->references('id')->on('admin_permissions')->onDelete('cascade');
        });
    }
};
