import type { Metadata } from "next";
import { notFound } from "next/navigation";
import CityGuideLayout from "@/app/components/city-guide/CityGuideLayout";
import { fetchCityPageDetails } from "@/services/cityguide";

interface Props {
  params: Promise<{ state: string; city: string }>;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state, city } = await params;
  const api = await fetchCityPageDetails(state, city);
  if (!api) return {};
  return {
    title: api.meta?.meta_title ?? `${api.banner?.title} | Indian Panorama`,
    description: api.meta?.meta_description ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/${city}` },
    robots: { index: true, follow: true },
  };
}

export default async function CityDestinationPage({ params }: Props) {
  const { state, city } = await params;
  const api = await fetchCityPageDetails(state, city);
  if (!api) notFound();

  return <CityGuideLayout data={api} state={state} city={city} />;
}
