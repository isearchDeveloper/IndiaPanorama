<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('coupon_code', 50)->nullable()->after('room_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_code');
            $table->decimal('gst_amount', 10, 2)->default(0)->after('discount_amount');
            // grand_total = total_amount - discount_amount + gst_amount
            $table->decimal('grand_total', 10, 2)->default(0)->after('gst_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'discount_amount', 'gst_amount', 'grand_total']);
        });
    }
};
