<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alter bookings table to support Razorpay group-tour booking flow.
 * Drops the old FK-constrained user_id (guests don't need accounts)
 * and adds all required passenger / contact fields.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Guest contact details (no auth required)
            $table->string('customer_name', 150)->nullable()->after('id');
            $table->string('customer_email', 150)->nullable()->after('customer_name');
            $table->string('customer_mobile', 20)->nullable()->after('customer_email');
            $table->unsignedBigInteger('package_id')->nullable()->after('id');

            // Journey date FK
            $table->unsignedBigInteger('journey_date_id')->nullable()->after('package_id');
            $table->foreign('journey_date_id')
                ->references('id')
                ->on('packages_group_dates')
                ->onDelete('set null');

            // Passenger breakdown
            $table->unsignedSmallInteger('adults')->default(1)->after('journey_date_id');
            $table->unsignedSmallInteger('child')->default(0)->after('adults');
            $table->unsignedSmallInteger('infant')->default(0)->after('child');

            // Room type
            $table->string('room_type', 50)->nullable()->after('infant');

            // Razorpay order reference
            $table->string('razorpay_order_id', 100)->nullable()->after('room_type');

            // Rename total_price → total_amount for clarity (add new column)
            $table->decimal('total_amount', 10, 2)->default(0)->after('razorpay_order_id');

            // Expand status enum
            $table->string('booking_status', 20)->default('pending')->after('total_amount');
            // Values: pending | success | failed | cancelled
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['journey_date_id']);
            $table->dropColumn([
                'customer_name',
                'customer_email',
                'customer_mobile',
                'journey_date_id',
                'adults',
                'child',
                'infant',
                'room_type',
                'razorpay_order_id',
                'total_amount',
                'booking_status',
            ]);
        });
    }
};
