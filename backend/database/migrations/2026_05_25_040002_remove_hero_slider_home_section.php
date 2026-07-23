<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The hero_slider HomeSection row stored overlay title/subtitle/visibility
        // for the hero banner panel. That panel has been removed; banners now manage
        // their own title/subtitle/button_text/url directly. Row is no longer needed.
        DB::table('home_sections')->where('section_key', 'hero_slider')->delete();
    }

    public function down(): void
    {
        DB::table('home_sections')->insertOrIgnore([
            'section_key' => 'hero_slider',
            'title'       => null,
            'subtitle'    => null,
            'is_visible'  => 1,
            'sort_order'  => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
};
