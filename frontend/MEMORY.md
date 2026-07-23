# MEMORY.md — Indian Panorama (Permanent Project Memory)

> Read this file first. It lets any engineer/AI continue this project without re-reading the codebase.
> Last full audit: 2026-07-12. Status: pre-production (dev servers live, site not launched).

---

## 1. Project Overview

- **Purpose:** Marketing + lead-generation website for **Indian Panorama / Cholan Tours Pvt Ltd** — an India inbound tour operator (Trichy, Tamil Nadu). Sells tour packages, experiences, activities, attractions info, car rentals, festivals content. Primary conversion = enquiry form (sidebar on every content page).
- **Business domain:** Travel & tourism, India inbound, SEO-content-heavy.
- **Tech stack:** Next.js **16.2.6** (App Router, Turbopack dev), React **19.2.4**, TypeScript 5, CSS Modules (+2 SCSS), Tailwind v4 present but barely used (globals only), Swiper 12, yet-another-react-lightbox.
- **Backend:** External Laravel CMS at `https://projects.isearchsolution.com/crm/api/v1` (a parallel `/dev/api/v1` env exists). Auth: `X-Public-Token` header. Token + URLs in `.env`. **Frontend has zero database** — pure API consumer.
- **Build:** `npm run build` (= `rimraf .next/cache && next build`). Dev: `next dev` (port 3000). No Docker/CI config in repo. No middleware. No route handlers (`app/api` absent).

## 2. Folder Responsibilities

```
src/app/
  layout.tsx            root layout: fonts (Outfit=headings, Inter=body via next/font),
                        GTM (env GTM_ID), Header, <main>, GoogleReviews (COMMENTED OUT),
                        PartnerSlider, Footer
  page.tsx              Home (root level, OUTSIDE (pages) group!)
  not-found.tsx         404 → components/common/PageNotFound
  globals.css           design tokens (:root), base rules, swiper dot overrides,
                        .cms-content + .cms-intro (see §7), heading tokens (see §8)
  (pages)/              route group; its layout.tsx appends <PopularPackagesGate><PopularPackages/>
                        after {children} on EVERY page in the group
    [state]/            state destination (CityGuideLayout; falls back to region API)
      [city]/           city destination
        [slug]/         UNIFIED DETAIL ROUTE (see §5)
        activities/ tour-packages/ tourist-attractions/ experiences/
      activities/ festivals/ tour-packages/ tourist-attractions/ experiences/
    experiences/        hub + [theme]/ = RESOLVER route (see §6)
    tour-packages/ [slug]  activities/ tourist-attractions/ [slug](redirect only)
    car-rental/ [slug]  festivals/ [slug]  about-us our-team contact-us faq
    privacy-policy terms-and-conditions cancellation-refund-policy awards-achievements
src/app/components/     feature folders: common/ layout/ home/ city-guide/ activities/
                        tourist-attractions/ tour-packages/ car-rental/(+layouts/,shared/)
                        festivals/(+detail/) experiences/ india-experience/(LEGACY-UNUSED)
                        india/(LEGACY) about-us/ our-team/ awards-achievements/
src/services/           one file per domain; ALL server-side fetch, `Promise<any>`,
                        `next:{revalidate:30}`, return `json?.data ?? null`, never throw
src/data/               faq/experiences(rootPageData.ts TYPES ONLY)/... mostly emptied
src/types/carRental.ts  legacy types
public/images/          ~106 images, 66 MB (⚠ several 2.6–4.2 MB PNGs)
```

## 3. Rendering / Caching Strategy

- **100% SSR/ISR.** 0 of 32 `page.tsx` are client components. 58 client components are leaf widgets only (sliders, accordions, header, forms, gates).
- **Caching:** every one of 31 service fetches uses `next: { revalidate: 30 }` (uniform, deliberate — backend content is in flux during build-out). PartnerSlider + festivals were 3600s, changed to 30s on 2026-07-12. **Before launch: raise rarely-changing endpoints (partners, festivals, header, home) back to 3600+.**
- 11 pages use `export const dynamic = "force-dynamic"` (activities pages, unified detail, festivals state). Others are ISR.
- `generateStaticParams` intentionally REMOVED from experiences [theme] (all on-demand).
- **Metadata:** 29/32 pages use `generateMetadata` fed by API `meta` objects with fallbacks; static const on faq, contact-us, cancellation-refund-policy (still hardcoded pages). Every page sets canonical to `https://www.indianpanorama.in/...`.
- **No robots.ts, no sitemap.ts — MISSING, must add before launch.**

## 4. API Strategy (all endpoints)

Base (crm): `projects.isearchsolution.com/crm/api/v1`, header `X-Public-Token: process.env.API_TOKEN`.

| Service file | Endpoint(s) | Used by |
|---|---|---|
| headerService | /header-menu (header+footer) | root layout Header/Footer |
| homeService | /home-page (or similar single) | Home |
| experiencesService | ONE helper `fetchExperiences(path)`; 6 exports: /page/settings/experiences, /experiences/category/{slug}, /experiences/state/{s}, /experiences/{s}/{c}, /experiences/subcategory/{slug}?state=, /experiences/detail/{slug} | all experience pages (base env: `EXPERIENCES_API_BASE_URL`) |
| activitiesService | /page/settings/activities?, activity detail (suffix slugs) | activities pages + unified detail |
| touristAttractions | TA landing/state/city/detail | TA pages + unified detail |
| tourspackages | /packages/{root,state/{s},city,popular} | packages pages, PopularPackages master |
| cityguide | state guide, city page, region (/packages/region/{slug}) ×4 | destination pages `/[state]`,`/[state]/[city]` |
| festivalsService | festivals root/state/detail ×3 | festival pages |
| carRental | /car-rental, /car-rental/{slug} (type-switched) | car-rental pages |
| pageSettings | /page/setting/{slug} (generic CMS pages) | privacy-policy, terms (extend to faq/contact/refund later) |
| about/awards/team | one each | company pages |
| PartnerSlider (inline fetch) | /partners | root layout |
| services/api.ts | ❌ DEAD (axios instance, 0 importers) |

**Duplicate-call note:** `generateMetadata` + page both call the same service per request (2× per render) — Next dedupes identical fetch URLs per request, so acceptable; wrapping services in React `cache()` is the clean future fix.

**Backend flux is the #1 operational hazard.** Shapes have changed repeatedly (documented cases: `best_time.list` items `{label,text}`; `why_choose.items` strings; `quick_info` object→array; `popular_experience` null→object{title,slug,image}; cities use `city_slug` not `slug`; states list includes `{name:"All States",slug:"all"}` which the UI filters). RULE: every new field mapping gets `??` fallbacks + `typeof`/`Array.isArray` guards; missing section ⇒ hide section (never crash, never static-fallback — user mandate).

## 5. Unified Detail Route (core architecture decision)

`/[state]/[city]/[slug]` — ONE route, slug **suffix decides type**:
- `-experience` → experiencesService.getExperienceDetail(baseSlug) → `ExperienceDetailLayout` (quickInfo = label/value ARRAY)
- `-activity` → fetchActivityDetail (checks `type === "activity"`)
- `-tourist-attractions` → fetchTouristAttractionDetail → `AttractionDetailContent`
Breadcrumb labels ALWAYS from URL slugs via `slugToLabel` (never API names).

## 6. Experiences Module (TripAdvisor pattern, fully API-driven)

URLs (max 2 dirs): `/experiences` hub → `/experiences/{category}` → `/experiences/{subcategory}` (all-India) → `/experiences/{subcategory}-in-{state}` (filtered) → detail (§5). State hubs `/{state}/experiences`, city hubs `/{state}/{city}/experiences`.

`experiences/[theme]/page.tsx` is a **resolver**: parse `-in-` (state validated via `getExperienceState` — invalid state ⇒ 404, SEO guard against infinite fake URLs) → subcategory API → category API (plain OR `-in-{state}` state-context mode: h1/banner "X in Kerala", breadcrumb w/ state, subcategory card links get `linkSuffix="-in-{state}"`) → 404.

Rules: category+subcategory slugs share ONE namespace (must be unique); subcategory slugs must NEVER contain `-in-`; item slugs stored WITHOUT `-experience` suffix. Contract doc for backend: `EXPERIENCE-MODULE-BACKEND-GUIDE.md` (repo root).

## 7. CMS HTML System (typography)

- Global **`.cms-content`** (globals.css end): styles CMS tags structurally only — p margins (0.9em), ul/ol/li, em-based headings (h1&h2=1.4em, h3=1.25em, h4=1.1em; CMS h1 visually demoted, page keeps one real h1), strong=600+primary, links=primary underline, blockquote, responsive img, x-scroll table, pre/code, hr. Font-size/color/line-height inherit from wrapper class.
- Global **`.cms-intro`** = canonical body text (16px / 1.75 / var(--color-text-secondary)).
- `ReadMoreHtml` (common) = clamp + Read More toggle; its inner div carries `cms-content`; callers pass `cms-intro ${styles.x}` where canonical.
- **RULE: every new `dangerouslySetInnerHTML` div gets `cms-content`** (30 sites currently). Card-desc clamps add `.desc p { display:inline; margin:0 }`.

## 8. Design Tokens & Conventions

- `globals.css :root`: colors (--color-primary #FF991B, --color-green #1a3a1c, text-primary #1a1a1a, text-secondary #555…), type scale --text-xs…5xl, `--heading-section: clamp(20px,2.2vw,30px)`, `--heading-sub: clamp(18px,2vw,24px)` (166 heading classes consolidated to these two — hero/banner headings 32px+ intentionally excluded).
- Text colors ALWAYS tokens (hardcoded #1a1a1a/#555/#444 purged from modules).
- Components live DIRECTLY in feature folders (user rule: no `root/`-style subfolders).
- Reuse-first (user mandate): before creating a component check common/, city-guide/ (ThingsToDo, TravelTips…), experiences/ (ExploreGrid, SignatureExperiences, ExperienceThemesGrid, ThemeQuickInfo, SubcategoryListing), festivals/FestivalWhyChoose (universal why-choose), ActivityPerfectFor, IconCards, ReadMoreHtml, Banner, Breadcrumb, SidebarForm, FaqSection, NearbySwiper, GalleryLightbox.
- Sliders: custom **5-dot pagination pattern** (MAX_DOTS=5, proportional windowing, SSR-rendered buttons) — do NOT use Swiper dynamicBullets (proven unreliable here). globals.css scopes `.swiper-pagination-bullets-dynamic:not(.swiper-pagination)`.
- Services: `Promise<any>` + eslint-disable (user rejected big interfaces), endpoints/env-driven, single fetch helper per domain where possible.
- Language: user communicates in Hinglish; code comments partly Hinglish.

## 9. Site-wide Behaviors

- **PopularPackages master section**: `(pages)/layout.tsx` appends it to every grouped page; Home adds it manually (home is outside the group). `PopularPackagesGate` (client) hides it on 8 company pages AND on 404s (PageNotFound sets `data-page-not-found`, gate checks marker in useEffect — needed because thrown notFound() still renders (pages)/layout).
- **GoogleReviews (Elfsight)**: component ready (`common/GoogleReviews.tsx`, same exclusions+404 gate) but **commented out in root layout**; also embedded in `PackageHero` right column replacing the static review card (old card kept behind `SHOW_STATIC_REVIEWS = false`). ⚠ Verify PackageHero JSX compiles — last session ended mid-fix of stale IDE diagnostics there.
- **SidebarForm**: enquiry form with fake "I'm not a robot" tick (700ms spinner). ⚠ **handleSubmit does NOT send data anywhere** — preventDefault + success toast only. BUSINESS-CRITICAL GAP before launch.
- **Google Translate widget**: custom cookie clearing across all domain levels (GoogleTranslate.tsx) — English-restore bug fix; needs LIVE-domain testing.
- **CityGuideBanner**: intentionally renders image only (overlay/title/description hidden by user request; props kept).
- **Car rental**: `[slug]` type-switch city/route/package(→notFound, sections hidden as comments)/fleet+vehicle(→CarRentalVehicleLayout; FleetLayout deleted). RouteAbout goes full-width when API image missing.

## 10. Known Issues / Technical Debt (verified 2026-07-12)

CRITICAL
1. **API token in `next.config.ts` `env:{API_TOKEN}`** — inlines the secret into CLIENT JS bundle + committed to git. Remove from next.config (services read process.env server-side; .env already has it). File: next.config.ts.
2. **SidebarForm sends nothing** (no endpoint). components/common/SidebarForm.tsx.
3. **No robots.ts / sitemap.ts** — SEO launch blocker.
4. **66 MB /public**, 8 PNGs at 2.6–4.2 MB each used as hero/bg images (about-banner-pages.png 2.6MB is the DEFAULT fallback banner sitewide) → LCP killer. Convert to ≤200KB WebP.

HIGH
5. 91 raw `<img>` vs 35 next/Image files — no optimization/lazy sizing on most content images.
6. **9 unused npm deps** (verified 0 imports): jimp, aos, framer-motion, flatpickr, react-google-recaptcha, react-phone-input-2, react-toastify, libphonenumber-js, @heroicons/react (+ axios used only by dead services/api.ts). Bloats install/build.
7. **0 error.tsx / 0 loading.tsx** anywhere — an API hiccup = raw crash page; no streaming skeletons.
8. Dead code folders: components/india-experience/ (all 0 importers), india/, city-guide StatesGrid/CitiesGrid/ZoneWise/WhyChooseUs, tourist-attractions/TouristAttractionDetailLayout, car-rental CarDestinationTags/CarPackageLinks (hidden intentionally — keep), old redirect route tourist-attractions/[slug] (safety net), (pages)/cancellation-refund-policy 435-line hardcoded page, faq/FaqData.ts, india/indiaPageData.ts.
9. 2 leftover console.log in [state]/[city]/activities (page.tsx:53, ActivitiesCarousel.tsx:21).

MEDIUM
10. `NEXT_PUBLIC_API_BASE_URL` exposes backend base to client (only dead api.ts uses it — remove both).
11. generateMetadata+page double service call (React cache() wrap would halve).
12. Category-in-state pages show all-India subcategories (backend needs `category/{slug}?state=`); duplicate-content risk on `-in-` variants until backend filters content.
13. FaqSection `.inner` max-width 1350 no padding — ~23px misalignment vs 1304px container content; small screens edge risk (user self-edited; touch only on ask).
14. `/kerala/munnar` city-guide 404 (backend data gap, not code). Backend dev DB creds flaky (was 500 "Access denied").

## 11. Things NOT to change (user mandates)

- No static fallbacks on API pages — missing section = hidden section.
- Services stay `any`-typed, thin, env-driven.
- Don't delete "hidden" code the user commented (car-rental sections, GoogleReviews in layout, PackageHero static reviews) — they toggle back.
- Breadcrumbs from URL slugs. Suffix-based detail routing. Flat 2-dir experience URLs. `-in-` reserved.
- No maps in SidebarForm. Fake-tick captcha stays until real one requested.
- Heading tokens/`cms-content`/`cms-intro` system — extend, don't fork.
- Home page stays outside (pages) group (its PopularPackages is manual).

## 12. Pre-Launch Checklist (condensed)

secrets out of next.config → robots+sitemap → wire SidebarForm to real endpoint (+server validation/rate-limit) → compress hero PNGs → error.tsx+loading.tsx (root at minimum) → remove dead deps/api.ts/console.logs → revalidate tuning (30s→3600 for stable endpoints) → live-domain test: Google Translate cookies, Elfsight widget, GTM → decide `-in-` variant canonicals once backend filters → delete legacy folders (after user confirm) → real build+start smoke on prod URL.

## 13. Env Setup

`.env` (never commit values): `API_BASE_URL` (crm base — header service), `API_TOKEN`, `GTM_ID`, `NEXT_PUBLIC_API_BASE_URL` (legacy, removable), `EXPERIENCES_API_BASE_URL` (crm base for all experience endpoints; dev↔live switch = this one line). Commands: `npm run dev` / `npm run build` / `npm start`. Windows note: paths with `[slug]` need bash (PowerShell treats brackets as wildcards).

## 14. Session Memory Pointer

Deeper day-by-day history (decisions, API shape changes, who asked what) lives in the AI memory dir: `C:\Users\abc\.claude\projects\d--indian-panaroma-indian-panorama\memory\` (MEMORY.md index + pending_work.md etc.). Backend API contract: `EXPERIENCE-MODULE-BACKEND-GUIDE.md`.
