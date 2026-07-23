<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Country;
use App\Models\Region;
use App\Models\State;

/**
 * Seeds Indian states/UTs into the `states` table.
 * Safe to re-run — uses updateOrCreate, no duplicates.
 *
 * Options:
 *   --country-id=N   Use an explicit country ID instead of auto-detecting India
 *
 * Usage:
 *   php artisan db:seed --class=IndianStatesSeeder
 *   php artisan db:seed --class=IndianStatesSeeder -- --country-id=1
 */
class IndianStatesSeeder extends Seeder
{
    /** Region → State/UT name mapping for India */
    private function statesByRegion(): array
    {
        return [
            'North India' => [
                'Uttar Pradesh', 'Chandigarh', 'Haryana', 'Punjab',
                'Himachal Pradesh', 'Uttarakhand', 'Jammu & Kashmir',
                'Ladakh', 'Delhi (NCT)',
            ],
            'South India' => [
                'Lakshadweep', 'Kerala', 'Tamil Nadu', 'Karnataka',
                'Andhra Pradesh', 'Puducherry', 'Telangana', 'Andaman Nicobar',
            ],
            'East & North East India' => [
                'West Bengal', 'Odisha', 'Bihar', 'Jharkhand', 'Assam',
                'Arunachal Pradesh', 'Meghalaya', 'Tripura', 'Manipur',
                'Mizoram', 'Nagaland', 'Sikkim',
            ],
            'West & Central India' => [
                'Maharashtra', 'Madhya Pradesh', 'Chhattisgarh',
                'Rajasthan', 'Gujarat', 'Goa',
            ],
        ];
    }

    /** Find or create the India country record */
    private function resolveIndia(): Country
    {
        // 1. Try name/code match
        $india = Country::where('name', 'India')
                         ->orWhere('name', 'india')
                         ->orWhere('code', 'IN')
                         ->first();

        if ($india) {
            return $india;
        }

        // 2. Try id=1 (hardcoded in SettingsController for India packages)
        $india = Country::find(1);
        if ($india) {
            $this->command->warn("  Found country at id=1 ({$india->name}) — treating as India.");
            return $india;
        }

        // 3. Create India (handle continent_id NOT NULL by using 0 or next available)
        $this->command->warn("  India not found — creating it now.");

        $continentId = 0;

        // Try to find Asia in continents table if it exists
        if (Schema::hasTable('continents')) {
            $asia = DB::table('continents')
                       ->where('name', 'like', '%Asia%')
                       ->first();
            $continentId = $asia?->id ?? 0;
        }

        $fillable = ['name' => 'India', 'code' => 'IN', 'slug' => 'india'];

        // Only include continent_id if the column exists and has a NOT NULL constraint
        if (Schema::hasColumn('countries', 'continent_id')) {
            $fillable['continent_id'] = $continentId;
        }

        return Country::create($fillable);
    }

    /** Seed the four meta-regions if they don't already exist */
    private function seedRegions(): void
    {
        $regions = [
            ['name' => 'North India',             'order_seq' => 1],
            ['name' => 'South India',             'order_seq' => 2],
            ['name' => 'East & North East India', 'order_seq' => 3],
            ['name' => 'West & Central India',    'order_seq' => 4],
        ];

        foreach ($regions as $data) {
            // Match the slug convention used by RegionsController::store
            $slug = \Illuminate\Support\Str::slug($data['name'] . '-tour-packages');

            Region::updateOrCreate(
                ['name' => $data['name']],
                ['slug' => $slug, 'order_seq' => $data['order_seq']]
            );
        }
    }

    public function run(): void
    {
        $india = $this->resolveIndia();
        $this->seedRegions();

        $regionMap = Region::pluck('id', 'name')->toArray();
        $inserted  = 0;
        $skipped   = 0;

        foreach ($this->statesByRegion() as $regionName => $states) {
            $regionId = $regionMap[$regionName] ?? null;

            if (!$regionId) {
                $this->command->warn("  Region not found in DB: {$regionName} — states will have region_id=null");
            }

            foreach ($states as $stateName) {
                $exists = State::where('country_id', $india->id)
                               ->where('name', $stateName)
                               ->exists();

                if ($exists) {
                    // Update region_id if still missing
                    if ($regionId) {
                        State::where('country_id', $india->id)
                             ->where('name', $stateName)
                             ->whereNull('region_id')
                             ->update(['region_id' => $regionId]);
                    }
                    $skipped++;
                    continue;
                }

                // Unique slug
                $slug    = Str::slug($stateName);
                $final   = $slug;
                $i       = 1;
                while (State::where('slug', $final)->exists()) {
                    $final = $slug . '-' . $i++;
                }

                State::create([
                    'country_id' => $india->id,
                    'region_id'  => $regionId,
                    'name'       => $stateName,
                    'slug'       => $final,
                    'is_active'  => true,
                ]);

                $inserted++;
            }
        }

        $this->command->info(
            "IndianStatesSeeder: country='{$india->name}' (id={$india->id}), " .
            "{$inserted} states inserted, {$skipped} already existed."
        );
    }
}
