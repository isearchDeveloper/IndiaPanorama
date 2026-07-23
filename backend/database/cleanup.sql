-- =============================================================
--  Indian Panorama CRM — Dead Table Cleanup Script
--  Generated: 2026-05-25
--  Safe to run against the live DB after running migrations.
--  All tables confirmed to have zero model/route/view references.
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ── Mega Menu legacy (replaced by menus + menu_items) ──────────
DROP TABLE IF EXISTS `mega_menu_cities`;
DROP TABLE IF EXISTS `mega_menu_countries`;
DROP TABLE IF EXISTS `mega_menu_regions`;

-- ── Nav Menu legacy (replaced by menus + menu_items) ───────────
DROP TABLE IF EXISTS `nav_menu_items`;
DROP TABLE IF EXISTS `nav_menus`;

-- ── Women Chauffeur module (module removed entirely) ───────────
DROP TABLE IF EXISTS `women_chauffeur_sections`;
DROP TABLE IF EXISTS `women_chauffeur_cards`;
DROP TABLE IF EXISTS `women_chauffeur_banner`;

-- ── Home Section Items (replaced by home_about_features) ───────
DROP TABLE IF EXISTS `home_section_items`;

-- ── Countries master (duplicate of countries table) ────────────
DROP TABLE IF EXISTS `countries_master`;

-- ── Already handled by 2026_05_22_181915 migration (reference) ─
-- DROP TABLE IF EXISTS `theme_location_metas`;
-- DROP TABLE IF EXISTS `themes`;
-- DROP TABLE IF EXISTS `fairs_festivals`;
-- DROP TABLE IF EXISTS `festival_pages`;
-- DROP TABLE IF EXISTS `city_metas`;
-- DROP TABLE IF EXISTS `go_explorings`;
-- DROP TABLE IF EXISTS `offer_pages`;
-- DROP TABLE IF EXISTS `summers`;

SET FOREIGN_KEY_CHECKS = 1;
