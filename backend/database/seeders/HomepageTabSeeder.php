<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageTabSetting;
use App\Models\HomepageTabLocation;
use App\Models\Region;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class HomepageTabSeeder extends Seeder
{
    /**
     * Seed homepage tabs using EXISTING regions and locations.
     * Zero data duplication — only FK references.
     *
     * Adjust the region names and location names below
     * to match your actual database records.
     */
    public function run(): void
    {
        $tabsConfig = [
            [
                'region_name' => 'South India',
                'tab_order'   => 1,
                'is_visible'  => 1,
                'is_highlight' => 1,  // default selected
                'locations'   => ['Kerala', 'Tamil Nadu', 'Karnataka', 'Andhra Pradesh', 'Telangana', 'Puducherry'],
            ],
            [
                'region_name' => 'North India',
                'tab_order'   => 2,
                'is_visible'  => 1,
                'is_highlight' => 0,
                'locations'   => ['Rajasthan', 'Uttar Pradesh', 'Himachal Pradesh', 'Uttarakhand', 'Delhi', 'Punjab', 'Jammu & Kashmir'],
            ],
            [
                'region_name' => 'East & North East India',
                'tab_order'   => 3,
                'is_visible'  => 1,
                'is_highlight' => 0,
                'locations'   => ['West Bengal', 'Odisha', 'Bihar', 'Assam', 'Sikkim', 'Meghalaya', 'Nagaland'],
            ],
            [
                'region_name' => 'West & Central India',
                'tab_order'   => 4,
                'is_visible'  => 1,
                'is_highlight' => 0,
                'locations'   => ['Gujarat', 'Maharashtra', 'Madhya Pradesh', 'Goa', 'Chhattisgarh'],
            ],
        ];

        DB::transaction(function () use ($tabsConfig) {

            foreach ($tabsConfig as $config) {

                // Find existing region — NOT creating anything
                $region = Region::where('name', $config['region_name'])->first();

                if (!$region) {
                    $this->command->warn("Region '{$config['region_name']}' not found — skipping.");
                    continue;
                }

                // Create tab setting (pointer to existing region)
                $tab = HomepageTabSetting::updateOrCreate(
                    ['region_id' => $region->id],
                    [
                        'is_visible'   => $config['is_visible'],
                        'is_highlight' => $config['is_highlight'],
                        'tab_order'    => $config['tab_order'],
                    ]
                );

                $this->command->info("Tab '{$config['region_name']}' -> region #{$region->id}");

                // Assign locations (pointers to existing locations)
                foreach ($config['locations'] as $sortOrder => $locationName) {

                    $location = Location::where('name', $locationName)->first();

                    if (!$location) {
                        $this->command->warn("  Location '{$locationName}' not found — skipping.");
                        continue;
                    }

                    HomepageTabLocation::updateOrCreate(
                        [
                            'region_id'   => $region->id,
                            'location_id' => $location->id,
                        ],
                        [
                            'sort_order' => $sortOrder + 1,
                        ]
                    );

                    $this->command->info("  Location '{$locationName}' (#{$location->id}) -> sort " . ($sortOrder + 1));
                }
            }
        });
    }
}