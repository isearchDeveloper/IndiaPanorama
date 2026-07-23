<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('role', 80)->nullable()->after('user_name');
            $table->json('old_value')->nullable()->after('properties');
            $table->json('new_value')->nullable()->after('old_value');
            $table->string('device_type', 20)->nullable()->after('new_value');
            $table->string('os_name', 60)->nullable()->after('device_type');
            $table->string('browser_name', 60)->nullable()->after('os_name');
            $table->string('country', 100)->nullable()->after('browser_name');

            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['action']);
            $table->dropColumn(['role', 'old_value', 'new_value', 'device_type', 'os_name', 'browser_name', 'country']);
        });
    }
};
