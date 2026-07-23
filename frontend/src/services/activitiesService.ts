import { cache } from "react";

const BASE = "https://projects.isearchsolution.com/crm/api/v1";

function headers() {
  return {
    Accept: "application/json",
    "X-Public-Token": process.env.API_TOKEN ?? "",
  };
}

async function apiFetch(path: string) {
  try {
    const res = await fetch(`${BASE}${path}`, {
      headers: headers(),
      next: { revalidate: 30 },
    });
    if (!res.ok) {
      console.error(`Activities API failed [${path}]: ${res.status}`);
      return null;
    }
    return await res.json();
  } catch (err) {
    console.error(`Activities API error [${path}]:`, err);
    return null;
  }
}

// â”€â”€ Types â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export interface ActivityStatItem { stat: string; label: string; }
export interface ActivityTypeItem { name: string; slug: string; image: string | null; image_alt: string | null; state_slug?: string | null; city_slug?: string | null; }
export interface ActivityCategoryItem { name: string; slug: string; image: string | null; image_alt: string | null; description: string; activities_count: number; }
export interface ActivityPerfectForItem { title: string; icon: string | null; }
export interface ActivitySeasonalItem { season_label: string; period_text: string; activities_text: string; }
export interface ActivityDestinationItem { name: string; type: "state" | "city"; state_slug: string; city_slug: string | null; image: string | null; tours_count: number; }
export interface ActivityCityExperienceItem { title: string; image: string | null; image_alt: string | null; description: string; city_name: string; state_slug: string; city_slug: string; tours_count: number; popular_activities: string[]; }
export interface ActivityFaqItem { question: string; answer: string; }
export interface ActivityPackageItem { title: string; slug: string; image: string | null; image_alt: string | null; duration_days: number; duration_nights: number; location: string; }

export interface ActivitiesLandingData {
  banner: { title: string; image: string; image_alt: string | null; text: string | null; };
  short_description: string | null;
  stats: { image: string | null; image_alt: string | null; items: ActivityStatItem[]; };
  activity_types: { title: string; items: ActivityTypeItem[]; };
  categories: { title: string; items: ActivityCategoryItem[]; };
  perfect_for: { title: string; items: ActivityPerfectForItem[]; };
  seasonal_activities: { title: string; items: ActivitySeasonalItem[]; };
  top_activities_destination: { title: string; items: ActivityDestinationItem[]; };
  city_experiences: { title: string; items: ActivityCityExperienceItem[]; };
  faqs: { title: string; sub_title: string | null; items: ActivityFaqItem[]; };
  popular_packages: { title: string; items: ActivityPackageItem[]; };
  meta: { meta_title: string | null; meta_description: string | null; meta_keywords: string | null; h1_heading: string | null; };
}

// â”€â”€ State page types â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export interface ActivityItem {
  name: string; slug: string; image: string | null; image_alt: string | null;
  description: string; location_name: string; location_slug: string;
}
/** @deprecated use ActivityItem */
export type StateTopActivityItem = ActivityItem;
export interface StatePopularExperienceItem {
  title: string; description: string; icon: string | null;
}
export interface StateTopDestinationItem {
  city_name: string; city_slug: string; image: string | null; image_alt: string | null;
  description: string; tours_count: number; popular_activities: string[];
}
export interface StateFeaturedCategoryItem {
  name: string; slug: string; image: string | null; image_alt: string | null; description: string;
}

export interface StateWaterfallItem { label: string; image: string | null; }
export interface StateThingToDoItem {
  title: string; description: string;
  duration_timing?: string | null;
  best_for?: string | null;
  approximate_cost?: string | null;
}

export interface ActivitiesStateData {
  banner: { title: string; image: string; image_alt: string | null; };
  short_description: string | null;
  about_image: string | null;
  about_image_alt: string | null;
  top_activities?: { title: string; items: StateTopActivityItem[]; };
  popular_experience?: { title: string; items: StatePopularExperienceItem[]; };
  top_destinations?: { title: string; items: StateTopDestinationItem[]; };
  featured_category?: { title: string; items: StateFeaturedCategoryItem[]; };
  waterfalls?: { title: string; items: StateWaterfallItem[]; };
  top_things_to_do?: { title: string; items: StateThingToDoItem[]; };
  faqs?: { title: string | null; sub_title: string | null; items: ActivityFaqItem[]; };
  popular_packages?: { title: string; items: ActivityPackageItem[]; };
  meta: { meta_title: string | null; meta_description: string | null; meta_keywords: string | null; h1_heading: string | null; };
}

// â”€â”€ City page types â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

/** @deprecated use ActivityItem */
export type CityTopActivityItem = ActivityItem;
export interface CityAttractionItem {
  name: string; slug: string; image: string | null; image_alt: string | null; description: string;
}
export interface CityActivityItem {
  name: string; slug: string; image: string | null; image_alt: string | null;
}
export interface CityWaterfallItem {
  label: string; image: string | null;
}
export interface CityThingToDoItem {
  title: string; description: string;
  duration_timing?: string | null;
  best_for?: string | null;
  approximate_cost?: string | null;
}

export interface ActivitiesCityData {
  banner: { title: string; image: string | null; image_alt: string | null; };
  short_description: string | null;
  about_image: string | null;
  about_image_alt: string | null;
  top_activities?: { title: string; items: CityTopActivityItem[]; };
  activities_in_city?: { title: string; sub_title: string | null; items: CityActivityItem[]; };
  waterfalls?: { title: string; items: CityWaterfallItem[]; };
  top_things_to_do?: { title: string; items: CityThingToDoItem[]; };
  top_attractions?: { title: string; items: CityAttractionItem[]; };
  faqs?: { title: string | null; sub_title: string | null; items: ActivityFaqItem[]; };
  popular_packages?: { title: string; items: ActivityPackageItem[]; };
  meta: { meta_title: string | null; meta_description: string | null; meta_keywords: string | null; h1_heading: string | null; };
}

// â”€â”€ Fetchers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export const fetchActivitiesLanding = cache(async (): Promise<ActivitiesLandingData | null> => {
  const json = await apiFetch("/tourist-activities");
  return json?.data ?? null;
});

export const fetchActivitiesState = cache(async (stateSlug: string): Promise<ActivitiesStateData | null> => {
  const json = await apiFetch(`/tourist-activities/state/${stateSlug}`);
  return json?.data ?? null;
});

export const fetchActivitiesCity = cache(async (stateSlug: string, citySlug: string): Promise<ActivitiesCityData | null> => {
  const json = await apiFetch(`/tourist-activities/${stateSlug}/${citySlug}`);
  return json?.data ?? null;
});

// ── Activity Detail types ─────────────────────────────────────────────────────

export interface ActivityDetailData {
  banner: { title: string; tagline: string | null; image: string; image_alt: string | null; };
  short_description: string | null;
  state_name: string | null;
  city_name: string | null;
  about: { title: string | null; description: string | null; } | null;
  quick_information: {
    location?: string | null;
    duration?: string | null;
    best_for?: string | null;
    best_season?: string | null;
  } | null;
  experiences: { title: string | null; items: { image: string | null; image_alt: string | null; title: string; description: string; }[]; } | null;
  places: { title: string | null; items: { image: string | null; image_alt: string | null; title: string; description: string; activities_text?: string | null; }[]; } | null;
  things_to_do: { title: string | null; items: { title: string; description: string; }[]; } | null;
  itinerary: { title: string | null; items: { title: string; description: string; }[]; } | null;
  gallery: { image: string; image_alt: string | null; }[];
  faqs: { title: string | null; sub_title: string | null; items: { question: string; answer: string; }[]; } | null;
  explore_more_attractions: { title: string | null; items: { name: string; slug: string; image: string | null; image_alt: string | null; description: string; }[]; } | null;
  meta: { meta_title: string | null; meta_description: string | null; meta_keywords: string | null; h1_heading: string | null; };
}

export type ActivityDetailResult = ActivityDetailData & { type: string | null };

export const fetchActivityDetail = cache(async (slug: string): Promise<ActivityDetailResult | null> => {
  const json = await apiFetch(`/tourist-activities/${slug}`);
  if (!json || !json.data) return null;
  return {
    type: json.type ?? null,
    ...json.data,
  };
});

