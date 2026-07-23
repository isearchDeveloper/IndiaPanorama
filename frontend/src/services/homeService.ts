// Server-side only — browser mein nahi chalega

const API_BASE_URL = process.env.API_BASE_URL;
const API_TOKEN = process.env.API_TOKEN;

// ─── Types ────────────────────────────────────────────────────────────────

export interface HeroSlide {
  image: string;
  image_alt: string;
  title: string;
  subtitle: string;
  button_text: string;
  button_url: string;
}

export interface TourRegion {
  id: number;
  title: string;
  slug: string;
  url: string;
  banner: string;
  banner_alt: string;
  description: string | null;
}

export interface TourPackage {
  id: number;
  title: string;
  slug: string;
  url: string;
  image: string;
  image_alt: string;
  duration_days: number;
  duration_nights: number;
  price: number | null;
}

export interface TrustedFeature {
  icon_class: string;
  title: string;
  description: string;
  sort_order: number;
}

export interface Blog {
  id: number;
  title: string;
  slug?: string;
  url?: string;
  image: string;
  image_alt?: string;
  author?: string;
  published_at?: string;
  views?: number | string;
}

export interface SeoMeta {
  meta_title: string;
  meta_description: string;
  meta_keywords: string;
  h1_heading: string;
  extra_meta_head: string;
  extra_meta_body: string;
}

export interface HomeData {
  hero_banner: {
    slides: HeroSlide[];
  };
  india_tour_packages: {
    title: string;
    subtitle: string;
    description: string | null;
    button_text: string;
    button_url: string;
    regions: TourRegion[];
  };
  customized_tours: {
    title: string;
    subtitle: string;
    button_text: string;
    button_url: string;
    packages: TourPackage[];
  };
  trusted_operator: {
    title: string;
    description: string;
    button_text: string;
    button_url: string;
    image: string;
    image_alt: string;
    master_text: string | null;
    features: TrustedFeature[];
  };
  why_indian_panorama: {
    title: string;
    subtitle: string;
    image: string;
    image_alt: string;
  };
  latest_blogs: {
    title: string;
    subtitle: string;
    button_text: string;
    button_url: string;
    blogs: Blog[];
  };
  promo_banner: {
    image: string;
    image_alt: string;
    is_active: boolean;
  };
  seo_meta: SeoMeta;
}

// ─── Helpers ──────────────────────────────────────────────────────────────

export function extractGoogleVerification(html: string): string | null {
  const match = html?.match(/google-site-verification['"]\s+content=['"]([^'"]+)['"]/);
  return match?.[1] ?? null;
}

export function extractGtmId(html: string): string | null {
  const match = html?.match(/GTM-[A-Z0-9]+/);
  return match?.[0] ?? null;
}

// ─── API Call ─────────────────────────────────────────────────────────────

export async function getHomeData(): Promise<HomeData | null> {
  if (!API_BASE_URL) {
    console.error("Missing API_BASE_URL");
    return null;
  }

  try {
    const res = await fetch(`${API_BASE_URL}/home`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`Home API failed: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("Home data fetch error:", error);
    return null;
  }
}
