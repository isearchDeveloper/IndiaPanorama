<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alter payments table to store full Razorpay signature data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('razorpay_order_id', 100)->nullable()->after('booking_id');
            $table->string('razorpay_payment_id', 100)->nullable()->after('razorpay_order_id');
            $table->string('razorpay_signature', 255)->nullable()->after('razorpay_payment_id');
            $table->json('razorpay_payload')->nullable()->after('razorpay_signature');
            // Expand status to include razorpay states
            $table->string('payment_status', 20)->default('pending')->after('razorpay_payload');
            // Values: pending | captured | failed | refunded
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_order_id',
                'razorpay_payment_id',
                'razorpay_signature',
                'razorpay_payload',
                'payment_status',
            ]);
        });
    }
};
