import type { Metadata } from "next";
import TouristAttractionHomeLayout from "@/app/components/tourist-attractions/TouristAttractionHomeLayout";
import { fetchTouristAttractionsHome } from "@/services/touristAttractions";

export const dynamic = "force-dynamic";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchTouristAttractionsHome();
  const meta = data?.meta;
  return {
    title: meta?.meta_title ?? "Tourist Attractions in India | Indian Panorama",
    description: meta?.meta_description ?? "Explore India's most captivating tourist attractions — from Kerala's backwaters and Rajasthan's forts to the Himalayas.",
    keywords: meta?.meta_keywords ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/tourist-attractions" },
    robots: { index: true, follow: true },
  };
}

export default async function TouristAttractionsPage() {
  const data = await fetchTouristAttractionsHome();

  const topTours = (data?.top_attractions?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image,
    label: "Explore Now →",
    href: `/${item.state}/${item.city}/${item.slug}`,
    slug: item.slug,
  }));

  const popularStates = (data?.explore_states?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    slug: item.slug,
    image: item.image ?? "/images/kerala-tours-v2.webp",
    label: "View Tours →",
    isNew: item.is_new ?? false,
    href: `/${item.slug}/tourist-attractions`,
    toursCount: item.attraction_count ?? undefined,
  }));

  const popularCities = (data?.popular_cities?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image ?? "/images/kerala-tours-v2.webp",
    href: `/tourist-attractions/${item.slug}`,
    toursCount: item.attraction_count ?? undefined,
    description: item.short_description ?? undefined,
    popular: item.attractions ?? [],
  }));

  const regions = (data?.regions?.items ?? []).map((item, i) => ({
    id: i + 1,
    title: item.title,
    slug: item.slug,
    icon: item.icon ?? "🗺️",
    description: item.description ?? "",
    href: item.href ?? "#",
  }));

  const categories = (data?.categories?.items ?? []).map((item, i) => ({
    id: i + 1,
    title: item.title,
    slug: item.slug,
    image: item.image,
    description: item.description ?? "",
    count: item.count,
    href: `/${item.slug}`,
  }));

  const faqs = (data?.faqs?.items ?? []).map((f, i) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <TouristAttractionHomeLayout
      h1={data?.meta?.h1_heading ?? data?.banner?.title ?? "Tourist Attractions"}
      bannerTitle={data?.banner?.title ?? "Tourist Attractions"}
      bannerImage={data?.banner?.image}
      bannerSubtitle={data?.banner?.text ?? undefined}
      intro={data?.short_description ?? ""}
      topToursTitle={data?.top_attractions?.title}
      topTours={topTours}
      regionsTitle={data?.regions?.title}
      regions={regions}
      popularStatesTitle={data?.explore_states?.title}
      popularStates={popularStates}
      popularCitiesTitle={data?.popular_cities?.title}
      popularCities={popularCities}
      categoriesTitle={data?.categories?.title}
      categories={categories}
      faqsTitle={data?.faqs?.title}
      faqsSubtitle={data?.faqs?.sub_title ?? undefined}
      faqs={faqs}
    />
  );
}
