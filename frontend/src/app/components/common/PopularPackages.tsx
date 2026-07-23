import IndiaPopularPackages from "@/app/components/india/IndiaPopularPackages";
import { fetchPopularPackages } from "@/services/tourspackages";

/**
 * Master site-wide "Popular Packages" section.
 * Fetches /packages/popular and renders after the page content (below FAQ)
 * via the (pages) layout — same data on every page.
 */
export default async function PopularPackages() {
  const items = await fetchPopularPackages();
  if (!items.length) return null;

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const packages = items.map((p: any, i: number) => ({
    id: p.id ?? i + 1,
    title: p.title ?? "",
    image: p.primary_image ?? "/images/about-banner-pages.jpg",
    image_alt: p.primary_image_alt ?? p.title ?? "",
    duration_days: p.details?.duration_days ?? null,
    duration_nights: p.details?.duration_nights ?? null,
    slug: p.slug ?? "",
  }));

  return (
    <IndiaPopularPackages
      heading="Popular Tour Packages"
      subtext="Most booked holidays, hand-picked by our travellers"
      packages={packages}
    />
  );
}
