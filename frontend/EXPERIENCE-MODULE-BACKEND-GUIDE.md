# Experience Module — Backend Developer Guide

Frontend is fully built and running on **static data**. This document tells you exactly
what data/APIs the backend must provide so the frontend can switch from static files to
live APIs **without any layout/component change**.

---

## 1. URL Scheme (already live on frontend)

| Page | URL | Example |
|---|---|---|
| Hub | `/experiences` | categories listing |
| Category page | `/experiences/{category}` | `/experiences/nature-and-wildlife` |
| Subcategory listing (All India) | `/experiences/{subcategory}` | `/experiences/waterfalls-tours` |
| Subcategory + State listing | `/experiences/{subcategory}-in-{state}` | `/experiences/waterfalls-tours-in-kerala` |
| State hub | `/{state}/experiences` | `/kerala/experiences` |
| City hub | `/{state}/{city}/experiences` | `/kerala/munnar/experiences` |
| **Detail page** | `/{state}/{city}/{slug}-experience` | `/kerala/munnar/attukad-waterfalls-experience` |

Rules:
- URL depth never exceeds 2 directories under root (TripAdvisor pattern).
- Category slugs and subcategory slugs share ONE namespace (`/experiences/{slug}`),
  so **slugs must be unique across categories + subcategories combined**.
- Subcategory slugs must NEVER contain the token `-in-` (it is the state-filter separator).
- Detail slugs use the `-experience` suffix — same convention as `-activity` and
  `-tourist-attractions` on the unified `/{state}/{city}/{slug}` route.

## 2. Entity Model

```
Category (theme)          1 ──< Subcategory              1 ──< Experience (item)
nature-and-wildlife            waterfalls-tours               attukad-waterfalls (kerala/munnar)
heritage-tours                 beaches                        athirappilly-waterfalls (kerala/athirappilly)
spiritual                      wildlife-in-india              bhimlat-waterfalls (rajasthan/bundi)
tourist-places                 ...                            ...
```

Every **experience item** belongs to: one subcategory + one state + one city.
All listing pages (all-India, state-filtered, state hub, city hub) are just filters
over the same items table — no separate content needed per listing.

## 3. Required APIs

Base: `https://projects.isearchsolution.com/crm/api/v1` (auth: `X-Public-Token` header, same as other modules)

### 3.1 GET `/experiences/settings`  (hub page — categories etc.)
Already partially exists as `/page/settings/experiences`. Needs: banner, meta,
`themes[]` (categories) with `{ name, slug, image, image_alt, description }`.

### 3.2 GET `/experiences/category/{slug}`  (category page)
```jsonc
{
  "status": "success",
  "data": {
    "name": "Nature and Wildlife",
    "slug": "nature-and-wildlife",
    "banner": { "title": "...", "tagline": "...", "image": "..." },
    "intro": { "heading": "...", "description": "...", "illustration": "img-url|null" },
    "quick_info": [ { "label": "Location", "value": "India Wide Nature Destinations" } ],   // 4 items
    "subcategories": [
      { "name": "Waterfalls Tours", "slug": "waterfalls-tours", "image": "...", "image_alt": "...",
        "tours_count": "17 Tours", "description": "...", "popular_tag": "Jog Falls | Dudhsagar Falls" }
    ],
    "perfect_for": [ { "title": "Wildlife Lovers", "description": "...", "icon": "img-url|null" } ],
    "popular_cities": [   // "Explore Popular States" slider cards
      { "title": "Munnar Hill Experiences", "image": "...", "tours_count": "12 Tours",
        "description": "...", "popular_tag": "...", "state_slug": "kerala", "city_slug": "munnar" }
    ],
    "faqs": { "title": null, "items": [ { "question": "...", "answer": "..." } ] },
    "meta": { "meta_title": "...", "meta_description": "...", "h1_heading": "..." }
  }
}
```

### 3.3 GET `/experiences/subcategory/{slug}?state={stateSlug}`  (listing pages)
`state` query param optional — absent = All India.
```jsonc
{
  "status": "success",
  "data": {
    "name": "Waterfalls Tours",
    "slug": "waterfalls-tours",
    "category": { "name": "Nature and Wildlife", "slug": "nature-and-wildlife" },
    "banner": { "image": "...", "description": "..." },
    "states": [                      // states that HAVE items in this subcategory (filter chips)
      { "name": "Kerala", "slug": "kerala" },
      { "name": "Rajasthan", "slug": "rajasthan" }
    ],
    "items": [
      {
        "title": "Attukad Waterfalls",
        "slug": "attukad-waterfalls",         // WITHOUT -experience suffix
        "state_slug": "kerala", "state_name": "Kerala",
        "city_slug": "munnar",  "city_name": "Munnar",
        "image": "...", "image_alt": "...",
        "tagline": "A cascading beauty hidden between Munnar's rolling hills"
      }
    ],
    "meta": { "meta_title": "...", "meta_description": "..." }
  }
}
```
Frontend builds card link itself: `/{state_slug}/{city_slug}/{slug}-experience`.

### 3.4 GET `/experiences/detail/{slug}`  (detail page)
Slug arrives WITHOUT the `-experience` suffix (frontend strips it).
```jsonc
{
  "status": "success",
  "type": "experience",
  "data": {
    "title": "Attukad Waterfalls",
    "slug": "attukad-waterfalls",
    "state_slug": "kerala", "state_name": "Kerala",
    "city_slug": "munnar",  "city_name": "Munnar",
    "subcategory": { "name": "Waterfalls Tours", "slug": "waterfalls-tours" },
    "tagline": "A cascading beauty hidden between Munnar's rolling hills",
    "description": "<p>HTML allowed...</p>",
    "images": [ "url1", "url2" ],            // first image = banner
    "highlights": [ "Monsoon-fed cascading waterfall", "..." ],
    "quick_info": {
      "best_time": "September to January",
      "duration": "2–3 Hours",
      "entry_fee": "Free",
      "location": "Pallivasal, Munnar, Kerala"
    },
    "faqs": [ { "question": "...", "answer": "..." } ],
    "related": [                              // same subcategory, other items
      { "title": "Athirappilly Waterfalls", "slug": "athirappilly-waterfalls",
        "image": "...", "state_slug": "kerala", "city_slug": "athirappilly",
        "subcategory_slug": "waterfalls-tours" }
    ],
    "meta": { "meta_title": "...", "meta_description": "..." }
  }
}
```
Note: the unified route `/{state}/{city}/{slug}` first checks the `-experience` suffix →
calls this API; `-activity` → activity API; `-tourist-attractions` → attraction API.

### 3.5 State hub `/{state}/experiences` and City hub `/{state}/{city}/experiences`
Same items table filtered by state / by city, plus hub-specific sections
(theme tours, best time, adventure list etc.). Current static shapes to mirror:
`src/data/experiences/stateCityExperiencesData.ts` (interfaces `StateExpData`, `CityExpData`).

## 4. Where the static data lives (current frontend)

| File | What it feeds | Your API replaces it |
|---|---|---|
| `src/data/experiences/rootPageData.ts` | `/experiences` hub | 3.1 |
| `src/data/experiences/themePagesData.ts` | category pages | 3.2 |
| `src/data/experiences/experienceModuleData.ts` | subcategory registry + item↔subcategory mapping + listing helpers | 3.3 |
| `src/data/experiences/spots.ts` | experience items + detail content | 3.3 items + 3.4 |
| `src/data/experiences/stateCityExperiencesData.ts` | state/city hubs | 3.5 |

The TypeScript interfaces in these files ARE the field contract — match the names/shape
(snake_case in API is fine, frontend maps it once in the service layer).

## 5. Click-flow summary (what must keep working)

```
/experiences → category card → /experiences/nature-and-wildlife
  → subcategory card → /experiences/waterfalls-tours   (ALL states + state chips)
      → chip "Kerala" → /experiences/waterfalls-tours-in-kerala   (filtered)
          → item card → /kerala/munnar/attukad-waterfalls-experience   (DETAIL)
  → state card → /kerala/experiences → city card → /kerala/munnar/experiences → item → DETAIL
```

State-click rule everywhere: state click keeps the current context
(waterfall list me state click = waterfalls of that state, never a generic page).
