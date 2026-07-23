// ── Page type discriminator ──────────────────────────────────────────────────
export type CarRentalPageType = "route" | "package" | "city" | "fleet";

// ── Shared primitives ────────────────────────────────────────────────────────
export interface CarCategory {
  name: string;
  slug: string;
}

export interface CarItem {
  title: string;
  slug: string;
  seats: number;
  fuel_type: string;
  primary_image: string;
  primary_image_alt: string;
  category: CarCategory;
}

export interface FleetCategory {
  name: string;
  slug: string;
  cars: CarItem[];
}

export interface LocationItem {
  title?: string;
  label?: string;
  slug: string;
  url?: string;
  type?: string;
}

export interface SimpleLinkItem {
  label: string;
  slug: string;
  url?: string;
}

export interface VehicleCategory {
  name: string;
  slug: string;
  icon: string | null;
  icon_alt: string | null;
}

export interface StatItem {
  icon?: string;
  value: string;
  label: string;
}

export interface GalleryImage {
  url: string;
  alt: string;
}

export interface FaqRawItem {
  question: string;
  answer: string;
}

export interface RoadTripItem {
  id?: number;
  title: string;
  image: string;
  image_alt?: string;
  rating?: number;
  destinations?: string;
  duration_days?: number;
  duration_nights?: number;
  url?: string;
  slug?: string;
}

export interface MetaFields {
  meta_title: string | null;
  meta_description: string | null;
  meta_keywords?: string | null;
  h1_heading: string | null;
  meta_details?: string | null;
}

export interface BannerFields {
  title: string;
  image: string;
  image_alt: string;
}

// ── Base — every detail page has at minimum these fields ────────────────────
export interface BaseCarRentalData {
  slug: string;
  page_type: CarRentalPageType;
  banner: BannerFields;
  meta: MetaFields;
  fleet: { categories: FleetCategory[] };
  why_choose: { title: string; description: string; stats: StatItem[] };
  popular_locations: { title: string; items: LocationItem[] };
  faqs: { title: string | null; items: FaqRawItem[] };
  road_trips: { title: string; subtitle: string; items: RoadTripItem[] };
}

// ── City page  (/car-rental/chennai) ────────────────────────────────────────
export interface CarRentalCityData extends BaseCarRentalData {
  page_type: "city";
  description: string | null;
  gallery: GalleryImage[];
  vehicle_categories: VehicleCategory[];
  features: { title: string | null; items: string[] };
  benefits: { title: string | null; items: string[] };
  routes?: { title: string; items: SimpleLinkItem[] };
}

// ── Route page (/car-rental/cochin-to-munnar) ───────────────────────────────
export interface CarRentalRouteData extends BaseCarRentalData {
  page_type: "route";
  description: string | null;
  gallery: GalleryImage[];
  route_highlights: { title: string | null; items: RouteHighlight[] };
  trip_info: TripInfo | null;
}

export interface RouteHighlight {
  title?: string;
  name?: string;
  description?: string;
  icon?: string | null;
}

export interface TripInfo {
  distance: string;
  duration: string;
  route: string;
  best_season: string;
}

// ── Package page (/car-rental/golden-triangle-tour) ─────────────────────────
export interface CarRentalPackageData extends BaseCarRentalData {
  page_type: "package";
  description: string | null;
  gallery: GalleryImage[];
  about?: PackageAbout;
  packages?: PackageOption[];
  route_highlights?: { title: string | null; items: RouteHighlight[] };
  amenities?: PackageAmenity[];
  inclusions?: string[];
  exclusions?: string[];
  itinerary?: ItineraryDay[];
}

export interface PackageAbout {
  title: string;
  description: string;
  trip_overview?: {
    duration: string;
    best_time_to_visit: string;
    route: string;
    ideal_for: string;
  };
}

export interface PackageOption {
  slug: string;
  title: string;
  subtitle?: string;
  image: string;
  image_alt: string;
  inclusions: string[];
}

export interface PackageAmenity {
  label: string;
  description: string;
}

export interface ItineraryDay {
  day: number;
  title: string;
  description: string;
}

// ── Fleet page (/car-rental/maruti-suzuki-ertiga) ───────────────────────────
export interface CarRentalFleetData extends BaseCarRentalData {
  page_type: "fleet";
  description: string | null;
  gallery: GalleryImage[];
  vehicle_about?: FleetAbout;
  amenities?: PackageAmenity[];
  specifications: FleetSpec[];
  vehicle_categories: VehicleCategory[];
}

export interface FleetAbout {
  title: string;
  description: string;
  size: string;
  fuel: string;
  seating: string;
  purpose: string;
}

export interface FleetSpec {
  label: string;
  value: string;
}

// ── Union type used in [slug]/page.tsx ───────────────────────────────────────
export type CarRentalDetailUnion =
  | CarRentalCityData
  | CarRentalRouteData
  | CarRentalPackageData
  | CarRentalFleetData;

// ── Home page data ────────────────────────────────────────────────────────────
export interface CarRentalHomeData {
  banner: BannerFields;
  short_description: string;
  fleet: { categories: FleetCategory[] };
  long_description: string;
  checklist: { title: string; items: string[] };
  about: { title: string; description: string; gallery: GalleryImage[] };
  why_choose: { title: string; description: string; stats: StatItem[] };
  routes?: { title: string; items: SimpleLinkItem[] };
  popular_locations: { title: string; description?: string; items: LocationItem[] };
  destination?: { title: string; items: SimpleLinkItem[] };
  car_rental_packages?: { title: string; items: SimpleLinkItem[] };
  road_trips: { title: string; subtitle: string; items: RoadTripItem[] };
  faqs: { title: string | null; items: FaqRawItem[] };
  meta: MetaFields;
}
