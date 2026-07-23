import { cache } from "react";

const BASE = "https://projects.isearchsolution.com/crm/api/v1";
const HEADERS = { Accept: "application/json", "X-Public-Token": process.env.API_TOKEN ?? "" };

export interface FestivalBanner {
  title: string;
  banner_text: string;
  image: string;
  image_alt: string;
}

export interface FestivalHighlight {
  stat: string;
  label: string;
}

export interface FestivalStatsData {
  title: string;
  sub_title: string | null;
  highlights: FestivalHighlight[];
}

export interface FestivalItem {
  name: string;
  slug: string;
  image: string;
  image_alt: string;
}

export interface ExploreStateItem {
  state_name: string;
  state_slug: string;
  image: string;
  image_alt: string;
  cities: string[];
  popular_festivals: string[];
}

export interface MonthFestivalItem {
  name: string;
  image: string;
  image_alt: string;
  state_slug: string;
}

export interface ExploreMonthItem {
  month: number;
  month_name: string;
  festivals: MonthFestivalItem[];
}

export interface UpcomingFestivalItem {
  name: string;
  slug: string;
  image: string;
  image_alt: string;
  short_desc: string | null;
  month: number;
  days_left_in_month: number;
}

export interface WhyExperienceItem {
  title: string;
  tagline?: string;
  description?: string;
}

export interface WhyExperienceData {
  title: string;
  sub_title: string | null;
  items: WhyExperienceItem[];
}

export interface FestivalPackageItem {
  title: string;
  slug: string;
  image: string;
  image_alt: string;
  duration_days: number;
  duration_nights: number;
  location: string;
}

export interface FestivalFaqItem {
  question: string;
  answer: string;
}

export interface FestivalFaqsData {
  title: string;
  sub_title: string;
  list: FestivalFaqItem[];
}

export interface FestivalMetaData {
  meta_title: string;
  meta_description: string;
  meta_keywords: string;
  h1_heading: string;
  meta_details: string | null;
}

export interface FestivalsPageData {
  banner: FestivalBanner;
  short_description: string;
  stats: FestivalStatsData;
  festivals: FestivalItem[];
  explore_by_state: ExploreStateItem[];
  explore_by_month: ExploreMonthItem[];
  upcoming_festivals: UpcomingFestivalItem[];
  why_experience: WhyExperienceData;
  festival_packages: FestivalPackageItem[];
  faqs: FestivalFaqsData;
  meta: FestivalMetaData;
}

export interface StatePopularFestivalItem {
  name: string;
  slug: string;
  image: string;
  image_alt: string;
  location_text: string | null;
  month_text: string | null;
  short_description: string | null;
}

export interface StateFeaturedFestival {
  name: string;
  slug: string;
  image: string;
  image_alt: string;
  location_text: string;
  month_text: string;
  duration_text: string;
  short_description: string;
}

export interface ExploreMoreDestinationItem {
  state_name: string;
  state_slug: string;
  image: string;
  image_alt: string;
  rating: number;
  route_text: string;
  duration_days: number;
  duration_nights: number;
}

export interface StatePackageItem {
  title: string;
  slug: string;
  image: string;
  image_alt: string;
  short_description: string | null;
  duration_days: number;
  duration_nights: number;
  location: string;
}

export interface StateFestivalsPageData {
  banner: FestivalBanner;
  short_description: string;
  popular_festivals: {
    title: string;
    items: StatePopularFestivalItem[];
  };
  explore_by_month: ExploreMonthItem[];
  featured_festival: StateFeaturedFestival | null;
  why_visit: WhyExperienceData;
  faqs: FestivalFaqsData;
  explore_more_destinations: {
    title: string;
    items: ExploreMoreDestinationItem[];
  };
  state_packages: {
    title: string;
    items: StatePackageItem[];
  };
  meta: FestivalMetaData;
}

export const fetchFestivalsPage = cache(async (): Promise<FestivalsPageData | null> => {
  try {
    const res = await fetch(`${BASE}/page/settings/festivals`, {
      headers: HEADERS,
      next: { revalidate: 30 },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("Error fetching festivals page:", error);
    return null;
  }
});

// ── Festival Detail types ─────────────────────────────────────────────────────

export interface FestivalDetailKeyExperienceItem {
  icon: string | null;
  label: string;
}

export interface FestivalDetailHowToReachItem {
  mode: string;
  description: string;
}

export interface FestivalDetailWhyVisitItem {
  title: string;
  description: string;
}

export interface FestivalDetailFaqItem {
  question: string;
  answer: string;
}

export interface FestivalDetailHighlightItem {
  image: string;
  image_alt: string | null;
  title: string;
  slug: string;
}

export interface FestivalDetailPopularPlaceItem {
  image: string;
  image_alt: string | null;
  name: string;
}

export interface FestivalDetailPackageItem {
  title: string;
  slug: string;
  image: string;
  image_alt: string | null;
  short_description: string | null;
  duration_days: number;
  duration_nights: number;
  location: string;
}

export interface FestivalDetailExploreItem {
  state_name: string;
  state_slug: string;
  image: string;
  image_alt: string | null;
  rating: number;
  route_text: string;
  duration_days: number;
  duration_nights: number;
}

export interface FestivalDetailStatItem {
  value: string;
  label: string;
}

export interface FestivalDetailData {
  banner: { title: string; subtitle?: string | null; description?: string | null; image: string; image_alt: string | null; };
  short_description: string | null;
  intro_image: string | null;
  intro_image_alt: string | null;
  state: { name: string; slug: string; } | null;
  month: number | null;
  stats?: FestivalDetailStatItem[] | null;
  highlights?: { items: FestivalDetailHighlightItem[]; } | null;
  key_experiences: { title: string; items: FestivalDetailKeyExperienceItem[]; } | null;
  popular_places?: { title: string; items: FestivalDetailPopularPlaceItem[]; } | null;
  long_description: string | null;
  how_to_reach: FestivalDetailHowToReachItem[];
  festival_packages?: { title: string; items: FestivalDetailPackageItem[]; } | null;
  why_visit: { title: string; items: FestivalDetailWhyVisitItem[]; } | null;
  explore_more_destinations?: { title: string; items: FestivalDetailExploreItem[]; } | null;
  faqs: { title: string; list: FestivalDetailFaqItem[]; } | null;
  meta: {
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    h1_heading: string | null;
    meta_details: string | null;
  };
}

export const fetchFestivalDetail = cache(async (slug: string): Promise<FestivalDetailData | null> => {
  try {
    const res = await fetch(`${BASE}/page/settings/festivals/${slug}`, {
      headers: HEADERS,
      next: { revalidate: 30 },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error(`Error fetching festival detail [${slug}]:`, error);
    return null;
  }
});

export const fetchStateFestivals = cache(async (state: string): Promise<StateFestivalsPageData | null> => {
  try {
    const res = await fetch(`${BASE}/page/settings/festivals/state/${state}`, {
      headers: HEADERS,
      next: { revalidate: 30 },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error(`Error fetching festivals for state ${state}:`, error);
    return null;
  }
});

