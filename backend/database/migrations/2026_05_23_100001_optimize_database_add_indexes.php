<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Production DB Optimisation — May 2026
 *
 * 1. Drop dead Women-Chauffeur tables (module removed)
 * 2. Drop orphan 'department' table (no active code uses it)
 * 3. Add missing composite / single-column indexes for
 *    high-traffic query patterns:
 *      packages, package_details, package_images, banners,
 *      countries, states, users, tour_services, why_cholan_tour,
 *      news, hotels, hotel_cities
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ── 1. Drop Women-Chauffeur tables (module removed) ──────────────
        Schema::dropIfExists('women_chauffeur_cards');
        Schema::dropIfExists('women_chauffeur_sections');
        Schema::dropIfExists('women_chauffeur_banner');

        // Also clean up the seeded page row if it still exists
        if (Schema::hasTable('pages')) {
            DB::table('pages')->where('slug', 'women-chauffeur-driver')->delete();
        }

        // ── 2. Drop orphan 'department' table ────────────────────────────
        Schema::dropIfExists('department');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── 3. Add missing indexes ────────────────────────────────────────

        // packages table
        Schema::table('packages', function (Blueprint $table) {
            if (!$this->hasIndex('packages', 'idx_pkg_active_deleted')) {
                $table->index(['is_active', 'is_deleted'], 'idx_pkg_active_deleted');
            }
            if (!$this->hasIndex('packages', 'idx_pkg_country')) {
                $table->index('country_id', 'idx_pkg_country');
            }
            if (!$this->hasIndex('packages', 'idx_pkg_location')) {
                $table->index('location_id', 'idx_pkg_location');
            }
            if (!$this->hasIndex('packages', 'idx_pkg_slug')) {
                $table->index('slug', 'idx_pkg_slug');
            }
            if (!$this->hasIndex('packages', 'idx_pkg_category')) {
                $table->index('category_id', 'idx_pkg_category');
            }
        });

        // package_details — always joined with packages
        if (Schema::hasTable('package_details')) {
            Schema::table('package_details', function (Blueprint $table) {
                if (!$this->hasIndex('package_details', 'idx_pd_package')) {
                    $table->index('package_id', 'idx_pd_package');
                }
            });
        }

        // package_images
        if (Schema::hasTable('package_images')) {
            Schema::table('package_images', function (Blueprint $table) {
                if (!$this->hasIndex('package_images', 'idx_pi_package')) {
                    $table->index('package_id', 'idx_pi_package');
                }
            });
        }

        // banners — ordered by sort_order + filtered by is_active
        Schema::table('banners', function (Blueprint $table) {
            if (!$this->hasIndex('banners', 'idx_ban_active_sort')) {
                $table->index(['is_active', 'sort_order'], 'idx_ban_active_sort');
            }
        });

        // countries — slug lookups on frontend
        Schema::table('countries', function (Blueprint $table) {
            if (!$this->hasIndex('countries', 'idx_country_slug')) {
                $table->index('slug', 'idx_country_slug');
            }
            if (Schema::hasColumn('countries', 'is_active') &&
                !$this->hasIndex('countries', 'idx_country_active')) {
                $table->index('is_active', 'idx_country_active');
            }
        });

        // states — slug lookups, active filter
        Schema::table('states', function (Blueprint $table) {
            if (Schema::hasColumn('states', 'slug') &&
                !$this->hasIndex('states', 'idx_state_slug')) {
                $table->index('slug', 'idx_state_slug');
            }
            if (Schema::hasColumn('states', 'is_active') &&
                !$this->hasIndex('states', 'idx_state_active')) {
                $table->index('is_active', 'idx_state_active');
            }
        });

        // users — permission & admin checks
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_super_admin') &&
                !$this->hasIndex('users', 'idx_user_super')) {
                $table->index('is_super_admin', 'idx_user_super');
            }
            if (Schema::hasColumn('users', 'is_admin') &&
                !$this->hasIndex('users', 'idx_user_admin')) {
                $table->index('is_admin', 'idx_user_admin');
            }
        });

        // tour_services — active filter
        Schema::table('tour_services', function (Blueprint $table) {
            if (Schema::hasColumn('tour_services', 'is_deleted') &&
                !$this->hasIndex('tour_services', 'idx_ts_deleted')) {
                $table->index('is_deleted', 'idx_ts_deleted');
            }
        });

        // why_cholan_tour — active filter
        Schema::table('why_cholan_tour', function (Blueprint $table) {
            if (Schema::hasColumn('why_cholan_tour', 'is_active') &&
                !$this->hasIndex('why_cholan_tour', 'idx_wct_active')) {
                $table->index('is_active', 'idx_wct_active');
            }
        });

        // news
        if (Schema::hasTable('news')) {
            Schema::table('news', function (Blueprint $table) {
                if (Schema::hasColumn('news', 'is_active') &&
                    !$this->hasIndex('news', 'idx_news_active')) {
                    $table->index(['is_active', 'created_at'], 'idx_news_active');
                }
                if (Schema::hasColumn('news', 'slug') &&
                    !$this->hasIndex('news', 'idx_news_slug')) {
                    $table->index('slug', 'idx_news_slug');
                }
            });
        }

        // hotels
        if (Schema::hasTable('hotels')) {
            Schema::table('hotels', function (Blueprint $table) {
                if (Schema::hasColumn('hotels', 'is_deleted') &&
                    !$this->hasIndex('hotels', 'idx_hotel_deleted')) {
                    $table->index('is_deleted', 'idx_hotel_deleted');
                }
                if (Schema::hasColumn('hotels', 'slug') &&
                    !$this->hasIndex('hotels', 'idx_hotel_slug')) {
                    $table->index('slug', 'idx_hotel_slug');
                }
            });
        }

        // hotel_cities — join target
        if (Schema::hasTable('hotel_cities')) {
            Schema::table('hotel_cities', function (Blueprint $table) {
                if (Schema::hasColumn('hotel_cities', 'location_id') &&
                    !$this->hasIndex('hotel_cities', 'idx_hc_location')) {
                    $table->index('location_id', 'idx_hc_location');
                }
            });
        }
    }

    public function down(): void
    {
        // Re-create dropped women-chauffeur tables
        Schema::create('women_chauffeur_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->default(10);
            $table->string('section_title')->nullable();
            $table->text('section_description')->nullable();
            $table->timestamps();
        });

        Schema::create('women_chauffeur_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('women_chauffeur_banner', function (Blueprint $table) {
            $table->id();
            $table->string('url')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('banner_image_alt')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        // Drop added indexes
        $drops = [
            ['packages',       ['idx_pkg_active_deleted','idx_pkg_country','idx_pkg_location','idx_pkg_slug','idx_pkg_category']],
            ['package_details',['idx_pd_package']],
            ['package_images', ['idx_pi_package']],
            ['banners',        ['idx_ban_active_sort']],
            ['countries',      ['idx_country_slug','idx_country_active']],
            ['states',         ['idx_state_slug','idx_state_active']],
            ['users',          ['idx_user_super','idx_user_admin']],
            ['tour_services',  ['idx_ts_deleted']],
            ['why_cholan_tour',['idx_wct_active']],
            ['news',           ['idx_news_active','idx_news_slug']],
            ['hotels',         ['idx_hotel_deleted','idx_hotel_slug']],
            ['hotel_cities',   ['idx_hc_location']],
        ];

        foreach ($drops as [$tbl, $idxList]) {
            if (!Schema::hasTable($tbl)) continue;
            Schema::table($tbl, function (Blueprint $table) use ($tbl, $idxList) {
                foreach ($idxList as $idx) {
                    if ($this->hasIndex($tbl, $idx)) {
                        $table->dropIndex($idx);
                    }
                }
            });
        }
    }

    /** Check if a named index already exists on a table. */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
