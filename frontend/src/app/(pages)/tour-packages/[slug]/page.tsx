import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { fetchTourPackageBySlug } from "@/services/tourPackageDetail";
import PackageDetailLayout from "@/app/components/tour-packages/PackageDetailLayout";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const data = await fetchTourPackageBySlug(slug);
  if (!data) return { title: "Tour Package Not Found" };

  const banner = data.banner ?? {};
  const meta   = data.meta   ?? {};
  const days   = banner.duration_days   ?? 0;
  const nights = banner.duration_nights ?? 0;
  const duration = `${days}D / ${nights}N`;

  return {
    title:
      meta.meta_title ??
      `${banner.title ?? slug} | ${duration} | Indian Panorama`,
    description:
      meta.meta_description ??
      (banner.tour_highlights ?? "").slice(0, 160),
    alternates: { canonical: `https://www.indianpanorama.in/tour-packages/${slug}` },
    robots: { index: true, follow: true },
    openGraph: banner.primary_image
      ? { images: [{ url: banner.primary_image, alt: banner.primary_image_alt ?? banner.title }] }
      : undefined,
  };
}

export default async function TourPackageDetailPage({ params }: Props) {
  const { slug } = await params;
  const data = await fetchTourPackageBySlug(slug);
  if (!data) notFound();
  return <PackageDetailLayout data={data} />;
}
