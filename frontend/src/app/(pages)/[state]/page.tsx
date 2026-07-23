import type { Metadata } from "next";
import { notFound } from "next/navigation";
import CityGuideLayout from "@/app/components/city-guide/CityGuideLayout";
import { fetchStateGuideDetails, fetchRegionDetails } from "@/services/cityguide";

interface Props {
  params: Promise<{ state: string }>;
}

// region API (packages/region/{slug}) → CityGuideLayout data shape
// eslint-disable-next-line @typescript-eslint/no-explicit-any
function mapRegionToGuide(region: any) {
  return {
    banner: {
      title: region.banner?.title ?? region.region?.name ?? "",
      banner_text: region.banner?.sub_title ?? "",
      image: region.banner?.image ?? "",
    },
    short_description: region.short_description ?? "",
    packages: region.packages ?? [],
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    faqs: { title: region.faqs?.title ?? undefined, list: region.faqs?.items ?? [] },
    meta: region.meta ?? {},
  };
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
async function getPageData(state: string): Promise<any> {
  const stateData = await fetchStateGuideDetails(state);
  if (stateData) return stateData;

  const regionData = await fetchRegionDetails(state);
  if (regionData) return mapRegionToGuide(regionData);

  return null;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state } = await params;
  const api = await getPageData(state);
  if (!api) return {};
  return {
    title: api.meta?.meta_title ?? `${api.banner?.title} | Indian Panorama`,
    description: api.meta?.meta_description ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}` },
    robots: { index: true, follow: true },
  };
}

export default async function StateDestinationPage({ params }: Props) {
  const { state } = await params;
  const api = await getPageData(state);
  if (!api) notFound();

  return <CityGuideLayout data={api} state={state} />;
}
