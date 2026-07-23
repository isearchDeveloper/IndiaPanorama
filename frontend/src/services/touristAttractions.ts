import { cache } from "react";

const BASE = "https://projects.isearchsolution.com/crm/api/v1";
const HEADERS = { Accept: "application/json", "X-Public-Token": process.env.API_TOKEN ?? "" };

async function apiFetch(path: string) {
  try {
    const res = await fetch(`${BASE}${path}`, { headers: HEADERS, next: { revalidate: 30 } });
    if (!res.ok) { console.error(`Tourist Attractions API [${path}]: ${res.status}`); return null; }
    return await res.json();
  } catch (err) {
    console.error(`Tourist Attractions API error [${path}]:`, err);
    return null;
  }
}

// â”€â”€ Home page types â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export interface TAHomeTopAttractionItem {
  name: string;
  slug: string;
  image: string;
  image_alt?: string | null;
  state?: string | null;
  city?: string | null;
}

export interface TAHomeExploreStateItem {
  name: string;
  slug: string;
  image?: string | null;
  image_alt?: string | null;
  attraction_count?: number | null;
  is_new?: boolean;
}

export interface TAHomeRegionItem {
  title: string;
  slug: string;
  icon?: string | null;
  description?: string | null;
  href?: string | null;
}

export interface TAHomeCityItem {
  name: string;
  slug: string;
  image: string;
  image_alt?: string | null;
  short_description?: string | null;
  attraction_count?: number | null;
  attractions?: string[];
}

export interface TAHomeCategoryItem {
  title: string;
  slug: string;
  image: string;
  image_alt?: string | null;
  description?: string | null;
  count?: number;
}

export interface TAHomeFaqItem {
  question: string;
  answer: string;
}

export interface TAHomeData {
  banner: {
    title: string;
    image: string;
    image_alt: string | null;
    text?: string | null;
  };
  short_description: string | null;
  top_attractions?: {
    title: string;
    items: TAHomeTopAttractionItem[];
  } | null;
  explore_states?: {
    title: string;
    items: TAHomeExploreStateItem[];
  } | null;
  regions?: {
    title: string;
    items: TAHomeRegionItem[];
  } | null;
  popular_cities?: {
    title: string;
    items: TAHomeCityItem[];
  } | null;
  categories?: {
    title: string;
    items: TAHomeCategoryItem[];
  } | null;
  faqs?: {
    title: string;
    sub_title: string | null;
    items: TAHomeFaqItem[];
  } | null;
  meta: {
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    h1_heading: string | null;
    meta_details: string | null;
  };
}

// â”€â”€ State page types â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export type TAStateAttractionItem = {
  name: string;
  slug: string;
  image: string | null;
  image_alt: string | null;
  description: string | null;
  state?: string | null;
  city?: string | null;
};

export type TAStateBestTimeItem = {
  period?: string | null;
  season?: string | null;
  months?: string | null;
  description: string | null;
  icon?: string | null;
};

export type TAStateFaqItem = {
  question: string;
  answer: string;
};

export type TAStateCityItem = {
  name: string;
  slug: string;
  image: string | null;
  image_alt?: string | null;
};

export type TAStateData = {
  banner: { title: string; image: string | null; image_alt: string | null; };
  short_description: string | null;
  top_attractions?: { title: string; items: TAStateAttractionItem[]; } | null;
  cities?: TAStateCityItem[] | null;
  best_time_to_visit?: { title: string; items: TAStateBestTimeItem[]; } | null;
  faqs?: { title: string | null; sub_title: string | null; items: TAStateFaqItem[]; } | null;
  meta: {
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    h1_heading: string | null;
    meta_details: string | null;
  };
};

// â”€â”€ City/detail page types â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export type TAAttractionItem = {
  name: string;
  slug: string;
  image: string | null;
  image_alt: string | null;
  description: string;
  state?: string | null;
  city?: string | null;
};

export type TABestTimeItem = {
  period: string;
  description: string;
};

export type TAFaqItem = {
  question: string;
  answer: string;
};

export type TACityData = {
  banner: { title: string; image: string | null; image_alt: string | null; };
  short_description: string | null;
  top_attractions?: { title: string; items: TAAttractionItem[]; };
  best_time_to_visit?: { title: string; items: TABestTimeItem[]; };
  faqs?: { title: string | null; sub_title: string | null; items: TAFaqItem[]; };
  meta: {
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    h1_heading: string | null;
    meta_details: string | null;
  };
};

export type TADetailData = {
  banner: { title: string; tagline: string | null; image: string | null; image_alt: string | null; };
  short_description: string | null;
  state_name: string | null;
  city_name: string | null;
  about?: { title: string; description: string; } | null;
  quick_information?: { location?: string; duration?: string; best_for?: string; best_season?: string; } | null;
  why_visit?: { title: string; image: string | null; image_alt: string | null; description: string; highlights: string[]; } | null;
  things_to_do?: { title: string; items: { title: string; description: string; }[]; } | null;
  gallery?: { image: string; image_alt: string; }[] | null;
  nearby_attractions?: { title: string; items: { name: string; slug: string; image: string; image_alt: string; description: string; }[]; } | null;
  explore_more?: { title: string; items: { name: string; slug: string; image: string; }[]; } | null;
  faqs?: { title: string | null; sub_title: string | null; items: TAFaqItem[]; } | null;
  meta: {
    meta_title: string | null;
    meta_description: string | null;
    meta_keywords: string | null;
    h1_heading: string | null;
    meta_details: string | null;
  };
};

// â”€â”€ Fetchers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

export const fetchTouristAttractionState = cache(
  async (stateSlug: string): Promise<TAStateData | null> => {
    const json = await apiFetch(`/tourist-attractions/state/${stateSlug}`);
    return json?.data ?? null;
  }
);

export const fetchTouristAttractionsHome = cache(
  async (): Promise<TAHomeData | null> => {
    const json = await apiFetch("/tourist-attractions");
    return json?.data ?? null;
  }
);

export const fetchTouristAttractionDetail = cache(
  async (slug: string): Promise<TADetailData | null> => {
    const json = await apiFetch(`/tourist-attractions/${slug}`);
    return json?.data ?? null;
  }
);

export const fetchTouristAttractionCity = cache(
  async (stateSlug: string, citySlug: string): Promise<TACityData | null> => {
    const json = await apiFetch(`/tourist-attractions/${stateSlug}/${citySlug}`);
    return json?.data ?? null;
  }
);

