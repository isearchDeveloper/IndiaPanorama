<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Ensures the countries table contains exactly one record: India (id = 1).
 * Safe to run multiple times (idempotent).
 */
class EnsureIndiaSeeder extends Seeder
{
    public function run(): void
    {
        // Guarantee India exists with id = 1
        $exists = DB::table('countries')->where('id', 1)->exists();

        if (!$exists) {
            DB::table('countries')->insert([
                'id'         => 1,
                'name'       => 'India',
                'code'       => 'IN',
                'slug'       => 'india-tour-packages',
                'faq_title'  => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('India inserted into countries table.');
        } else {
            // Ensure data is correct even if the row already existed
            DB::table('countries')->where('id', 1)->update([
                'name' => 'India',
                'code' => 'IN',
                'slug' => 'india-tour-packages',
            ]);

            $this->command->info('India record verified and updated in countries table.');
        }

        // Remove any other countries that may have been added accidentally
        $removed = DB::table('countries')->where('id', '!=', 1)->delete();
        if ($removed > 0) {
            $this->command->warn("Removed {$removed} non-India country record(s).");
        }
    }
}
