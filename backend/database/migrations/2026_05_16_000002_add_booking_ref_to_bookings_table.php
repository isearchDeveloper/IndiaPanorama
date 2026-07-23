<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // varchar(20) comfortably holds the 14-digit primary format
        // and the 17-digit millisecond fallback format
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_ref', 20)->nullable()->unique()->after('id');
        });

        // Back-fill existing rows
        // Format: last 4 digits of customer_mobile + Unix timestamp from created_at
        // Matches the same logic in Booking::generateUniqueRef()
        $usedRefs = [];

        DB::table('bookings')->orderBy('id')->chunk(200, function ($rows) use (&$usedRefs) {
            foreach ($rows as $row) {
                $stripped = preg_replace('/\D/', '', $row->customer_mobile ?? '');
                $last4    = str_pad(substr($stripped, -4), 4, '0', STR_PAD_LEFT);
                $ts       = (int) strtotime($row->created_at);

                // Build ref and resolve any back-fill duplicates by incrementing ts
                $ref    = $last4 . $ts;
                $offset = 0;
                while (in_array($ref, $usedRefs, true)) {
                    $offset++;
                    $ref = $last4 . ($ts + $offset);
                }

                $usedRefs[] = $ref;

                DB::table('bookings')
                    ->where('id', $row->id)
                    ->update(['booking_ref' => $ref]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['booking_ref']);
            $table->dropColumn('booking_ref');
        });
    }
};
