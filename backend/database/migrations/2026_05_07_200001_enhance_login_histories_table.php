<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('login_histories', function (Blueprint $table) {
            $table->string('session_id', 100)->nullable()->after('id');
            $table->string('device_type', 20)->nullable()->after('user_agent');   // mobile|tablet|desktop
            $table->string('device_name', 100)->nullable()->after('device_type');
            $table->string('os_name', 60)->nullable()->after('device_name');
            $table->string('browser_name', 60)->nullable()->after('os_name');
            $table->string('browser_version', 30)->nullable()->after('browser_name');
            $table->string('country', 100)->nullable()->after('browser_version');
            $table->string('state', 100)->nullable()->after('country');
            $table->string('city', 100)->nullable()->after('state');
            $table->timestamp('last_activity_at')->nullable()->after('logged_in_at');
            $table->timestamp('logout_at')->nullable()->after('last_activity_at');

            $table->index('session_id');
            $table->index('last_activity_at');
            $table->index('logout_at');
        });
    }

    public function down(): void
    {
        Schema::table('login_histories', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropIndex(['last_activity_at']);
            $table->dropIndex(['logout_at']);
            $table->dropColumn([
                'session_id', 'device_type', 'device_name', 'os_name',
                'browser_name', 'browser_version', 'country', 'state', 'city',
                'last_activity_at', 'logout_at',
            ]);
        });
    }
};
