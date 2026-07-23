<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('admin_user_permissions')) {
            Schema::create('admin_user_permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('admin_permission_id')->constrained('admin_permissions')->onDelete('cascade');
                $table->unique(['user_id', 'admin_permission_id']);
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('admin_user_permissions');
    }
};
