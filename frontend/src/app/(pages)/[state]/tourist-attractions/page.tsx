import type { Metadata } from "next";
import { notFound } from "next/navigation";
import TouristAttractionStateLayout from "@/app/components/tourist-attractions/TouristAttractionStateLayout";
import { fetchTouristAttractionState } from "@/services/touristAttractions";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ state: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state } = await params;
  const data = await fetchTouristAttractionState(state);
  if (!data) return { title: "Not Found" };

  const stateName = data.banner.title;
  return {
    title: data.meta.meta_title ?? `${stateName} Tourist Attractions | Indian Panorama`,
    description: data.meta.meta_description ?? `Explore the best tourist attractions in ${stateName} with Indian Panorama.`,
    keywords: data.meta.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/tourist-attractions` },
    robots: { index: true, follow: true },
  };
}

export default async function StateTouristAttractionsPage({ params }: Props) {
  const { state } = await params;
  const data = await fetchTouristAttractionState(state);
  if (!data) notFound();

  const stateName = data.banner.title;

  const attractions = (data.top_attractions?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image ?? "/images/kerala-tours-v2.webp",
    image_alt: item.image_alt ?? item.name,
    description: item.description ?? "",
    href: `/${item.state}/${item.city}/${item.slug}`,
  }));

  const bestTime = (data.best_time_to_visit?.items ?? []).map((item, i) => ({
    id: i + 1,
    season: item.season ?? item.period ?? "",
    months: item.months ?? "",
    description: item.description ?? "",
    icon: item.icon ?? "🌤️",
  }));

  const cities = (data.cities ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image ?? null,
    href: `/${state}/${item.slug}/tourist-attractions`,
  }));

  const faqs = (data.faqs?.items ?? []).map((f, i) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <TouristAttractionStateLayout
      stateSlug={state}
      stateName={stateName}
      bannerImage={data.banner.image ?? undefined}
      bannerImageAlt={data.banner.image_alt ?? stateName}
      h1={data.meta.h1_heading ?? data.banner.title ?? stateName}
      intro={data.short_description ?? undefined}
      attractionsTitle={data.top_attractions?.title}
      attractions={attractions}
      cities={cities}
      bestTimeTitle={data.best_time_to_visit?.title}
      bestTime={bestTime}
      faqsTitle={data.faqs?.title ?? undefined}
      faqsSubtitle={data.faqs?.sub_title ?? undefined}
      faqs={faqs}
    />
  );
}
