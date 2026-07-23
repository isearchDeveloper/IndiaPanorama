import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { fetchFestivalDetail } from "@/services/festivalsService";
import FestivalDetailLayout from "@/app/components/festivals/FestivalDetailLayout";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const data = await fetchFestivalDetail(slug);
  if (!data) return {};
  return {
    title: data.meta.meta_title ?? `${data.banner.title} | Indian Panorama`,
    description: data.meta.meta_description ?? undefined,
    keywords: data.meta.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/festivals/${slug}` },
    robots: { index: true, follow: true },
  };
}

export default async function FestivalDetailPage({ params }: Props) {
  const { slug } = await params;
  const data = await fetchFestivalDetail(slug);
  if (!data) notFound();

  return (
    <FestivalDetailLayout
      data={data}
      breadcrumbs={[
        { label: "Home",      href: "/" },
        { label: "Festivals", href: "/festivals" },
        { label: data.banner.title, href: `/festivals/${slug}` },
      ]}
    />
  );
}
